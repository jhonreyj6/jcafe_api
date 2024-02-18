<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use Auth;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return response()->json(['message' => 'me'], 200);
        // return Socialite::driver($provider)->stateless()->redirect();
        // return response()->json(['url' => Socialite::with('facebook')->redirect()->getTargetUrl()], 200);
    }

    public function callback($provider)
    {
        return response()->json(['message' => 'socialite'], 200);
        $data = Socialite::driver($provider)->stateless()->user();
        $user = User::where([
            "provider" => $provider,
            "provider_id" => $data->getId(),
        ])->first();

        if (!$user) {
            $exist = User::where('email', $data->getEmail())->first();
            if(!$exist) {
                $user = User::create([
                    'first_name' => $data->name,
                    'email' => $data->email,
                    'provider_id' => $data->id,
                    'provider' => $provider,
                ]);
            } else {
                return response()->json(['message' => 'Email has already taken!'], 500);
            }
        }

        $token = auth()->login($user);
        $user = Auth::user();
        return view('socialite.callback', ['user'=> $user,'token'=> $token]);
    }
}
