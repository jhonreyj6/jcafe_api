<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Auth;
use Validator;
use App\Events\NewChatMessage;
use Storage;
use DB;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $room = ChatRoom::where('participant_id', Auth::id())->first();
        if ($room) {
            $chats = Chat::where('room_id', $room->id)
                ->latest()
                ->paginate(10)
            ;
            foreach ($chats as $chat) {
                $chat->sending = false;
            }

            return $chats;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'message' => 'max:200|regex:/^[a-zA-Z\d\W_]+$/',
            // 'file.*' => 'file|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $chat_room = ChatRoom::where('participant_id', Auth::id())->first();
        if (!$chat_room) {
            $chat_room = ChatRoom::create([
                'participant_id' => Auth::id(),
            ]);
        }

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
            'room_id' => $chat_room->id
        ]);

        $chat->getUserDetails;
        $chat->getUserDetails()->image_url = 'test';

        broadcast(new NewChatMessage($chat))->toOthers();

        return $chat;
    }
}
