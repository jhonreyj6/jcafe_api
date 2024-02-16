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
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
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

        Auth::login($user);
        return response()->json(['message' => 'success'], 200);
    }
}
