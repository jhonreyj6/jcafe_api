<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 422);
        }

        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['password' => 'Wrong Password!'], 422);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // err

        $plans = SubscriptionPlan::all();
        $isSubscribed = false;
        foreach ($plans as $plan) {
            if (auth()->user()->subscribed($plan)) {
                $isSubscribed = true;
            }
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth::user(),
            'expires_in' => auth()->factory()->getTTL() * 60,
            'subscription' => $isSubscribed,
            'profile_image' => Storage::disk('s3')->url('users/' . auth()->user()->id . '/images/' . auth()->user()->profile_img),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'min:2|max:20|string|required',
            'last_name' => 'min:2|max:20|string|required',
            'email' => 'email:rfc,dns|required|unique:users,email|max:32',
            'password' => 'min:6|max:20|required',
            'confirm' => 'same:password|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 500);
        }

        User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['' => ''], 200);
    }
}
