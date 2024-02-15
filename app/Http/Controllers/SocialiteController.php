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
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $data = Socialite::driver($provider)->user();
        return $data;
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
                return redirect('/login')->withErrors([
                    'email' => 'Email has already taken!',
                ]);
            }
        }

        Auth::login($user);
        return redirect('/');
    }
}
