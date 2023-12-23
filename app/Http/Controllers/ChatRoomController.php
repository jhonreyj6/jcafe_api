<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
// use App\Http\Requests\StoreChatRoomRequest;
// use App\Http\Requests\UpdateChatRoomRequest;
use Illuminate\Http\Request;
use Auth;

class ChatRoomController extends Controller
{
    public function index() {
        $room = ChatRoom::all();

        return $room;
    }

    public function show()
    {
        $room = ChatRoom::where('participant_id', Auth::id())->first();

        return $room;
    }
}
