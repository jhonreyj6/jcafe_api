<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ResetPassword;
use App\Mail\ResetPasswordMail;
use Mail;
use App\Models\User;
use Hash;

class ResetPasswordController extends Controller
{




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email:rfc,dns|required|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }


        $reset_request = ResetPassword::where('email', $request->input('email'))->first();


        if ($reset_request) {
            $reset_request->update([
                'access_token' => uniqid() . '_' . Str::random(10),
            ]);
        } else {
            $reset_request = ResetPassword::create([
                'email' => $request->input('email'),
                'access_token' => uniqid() . '_' . Str::random(10),
            ]);
        }

        Mail::to($request->input('email'))->send(new ResetPasswordMail($reset_request));

        return response()->json(['message' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        $data = ResetPassword::where('access_token', $token)->firstOrFail();

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'string',
            'confirm_password' => 'same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }


        $data = ResetPassword::where('access_token', $request->input('access_token'))->firstOrFail();
        $user = User::where('email', $data->email)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json(['message' => ''], 200);
    }


}
