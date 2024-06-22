<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Storage;
use App\Http\Controllers\AuthController;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $user->image_url = Storage::disk("s3")->url('users/' . $user->id . '/images/' . $user->profile_img);
        return response()->json($user, 200);
    }

    public function store(Request $request)
    {
        //
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'string|min:2|max:20|required',
            'last_name' => 'string|min:2|max:20|required',
            'birthday' => 'date_format:Y-m-d',
            'contact' => 'digits:11',
            'address' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $user = User::where('id', Auth::user()->id)->first();

        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'birthday' => $request->input('birthday'),
            'contact' => $request->input('contact'),
            'address' => $request->input('address')
        ]);

        return response()->json(['message' => 'updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function updateImage(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        if ($user->profile_img) {
            Storage::disk('s3')->delete('users/' . $user->id . '/images/' . $user->profile_img);
        }

        $pathToFile = Storage::disk('s3')->putFileAs('users/' . $user->id . '/images', $request->file('image'), $request->file('image')->hashName(), 'public');

        $user->update([
            'profile_img' => $request->file('image')->hashName()
        ]);

        return response()->json(['Profile' => 'Image Updated'], 200);
    }
}


