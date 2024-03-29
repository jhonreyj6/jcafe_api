<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{room_id}', function ($user, $room_id) {
    if (Auth::check()) {
        return ['id' => $user->id, 'name' => $user->first_name . ' ' . $user->last_name];
    }
});

Broadcast::channel('socialite.{token}', function ($user, $access_token) {
    // if (Auth::check()) {
        return ['user' => $user, 'token' => $access_token];
    // }
});

Broadcast::channel('test', function ($user, $room_id) {
    return true;
});
