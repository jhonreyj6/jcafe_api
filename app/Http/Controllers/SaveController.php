<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameSave;
use Validator;
use Storage;
use Auth;
use Carbon;
use DB;

class SaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saves = DB::table('game_saves')->where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $saves = $saves->transform(function ($value) {
            $value->created_at = Carbon::create($value->created_at)->toDayDateTimeString();
            $value->download_link = Storage::disk('s3')->url($value->file_name);
            return $value;
        });
        return response()->json($saves, 200);
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
            'file' => 'file|max:100240|mimes:zip,rar,7zip,rar4'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        Storage::disk('s3')
            ->putFileAs('users/' . Auth::user()->id . '/saves', $request->file('file'), $request->file('file')->getClientOriginalName(), 'public');

        $save = GameSave::create([
            'user_id' => Auth::id(),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize() / 1000,
        ]);

        $save->created_at = Carbon::create($save->created_at)->toDayDateTimeString();
        $save->download_link = Storage::disk('s3')->url($save->file_name);

        return response()->json($save, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $save = GameSave::where([
            'user_id' => Auth::id(),
            'id' => $id,
        ])->firstOrFail();

        return Storage::disk('s3')->download('users/' . Auth::id() . '/saves/' . $save->file_name);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $save = GameSave::where([
            'user_id' => Auth::id(),
            'id' => $id,
        ])->firstOrFail();

        Storage::disk('s3')->delete('users/' . Auth::id() . '/saves/' . $save->file_name);

        $save->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
