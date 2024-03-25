<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Auth;
use App\Models\User;
class ChatRoomAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $admin = User::where('role', 'admin')->get();
        $room = ChatRoom::whereNotIn('participant_id', $admin->pluck('id'))->get();
        $room->map( function($value) {
            $value->getUserDetails;
            $value->getUnreadChat;
            return $value;
        });

        return $room;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $room = ChatRoom::whereId($id)->where('participant_id', Auth::id())->first();

        return $room;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
