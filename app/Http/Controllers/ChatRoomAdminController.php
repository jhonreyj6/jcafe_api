<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Auth;
use App\Models\User;
use DB;

class ChatRoomAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = User::where('role', 'admin')->get();
        $room = ChatRoom::whereNotIn('participant_id', $admin->pluck('id'))->get();
        $room->map(function ($value) {
            $value->getUserDetails;
            $value->getUnreadChat;
            return $value;
        });

        return response()->json($room, 200);
    }



    public function search(Request $request)
    {
        $users = User::where(function ($value) use ($request) {
            $value
                ->where('first_name', 'LIKE', '%' . $request->input('query') . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request->input('query') . '%')
                ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%" . $request->input('query') . "%")
                ->orWhere('id', 'LIKE', '%' . $request->input('query') . '%')
            ;
        })->where('role', null)->get();

        $room = ChatRoom::whereIn('participant_id', $users->pluck('id'))->get();

        $room->map(function ($value) {
            $value->getUserDetails;
            $value->getUnreadChat;
            return $value;
        });

        return response()->json($room, 200);

    }

    public function show($id)
    {
        $room = ChatRoom::whereId($id)->where('participant_id', Auth::id())->first();

        return response()->json($room, 200);

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
