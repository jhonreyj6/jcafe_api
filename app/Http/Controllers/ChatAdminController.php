<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Auth;
use Validator;
use App\Events\NewChatMessage;

class ChatAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chat = Chat::where('room_id', $request->input('room_id'))->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($chat, 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'regex:/^[a-zA-Z\d\W_]+$/|max:1000|required',
            'room_id' => 'numeric|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $chat_room = ChatRoom::whereId($request->input('room_id'))->first();
        if (!$chat_room) {
            $chat_room = ChatRoom::create([
                'participant_id' => Auth::id(),
            ]);
        }

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
            'room_id' => $chat_room->id,
        ]);

        broadcast(new NewChatMessage($chat))->toOthers();

        return response()->json($chat, 200);
    }
}
