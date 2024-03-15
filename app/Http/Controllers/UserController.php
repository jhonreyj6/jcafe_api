<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::orderBy('created_at', 'desc')->paginate(10);

        return $user;
    }

    public function search(Request $request)
    {
        $user = User::where('first_name', 'LIKE', '%' . $request->input('query') . '%')
            ->orWhere('last_name', 'LIKE', '%' . $request->input('query') . '%')
            ->orWhere('id', 'LIKE', '%' . $request->input('query') . '%')
            ->orWhere('address', 'LIKE', '%' . $request->input('query') . '%')
            ->orWhere('email', 'LIKE', '%' . $request->input('query') . '%')
            ->paginate(10);

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'string|required|min:2|max:20',
            'last_name' => 'string|required|min:2|max:20',
            'email' => 'email:rfc,dns|required',
            'password' => 'required|string',
            'role' => Rule::in(['admin', null, 'client']),
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'address' => $request->input('address'),
            'contact' => $request->input('contact'),
            'birthday' => $request->input('birthday'),
            'role' => $request->input('role')
        ]);

        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::whereId($id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'first_name' => 'string|nullable|min:2|max:20',
            'last_name' => 'string|nullable|min:2|max:20',
            'email' => 'email:rfc,dns|nullable',
            // 'password' => 'nullable|sometimes|string',
            'password' => 'nullable|string',
            'confirm_password' => 'same:password',
            'address' => 'max:30|nullable',
            'contact' => 'numeric|nullable|',
            'birthday' => 'date|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password') ? $request->input('password') : $user->password,
            'address' => $request->input('address'),
            'contact' => $request->input('contact'),
            'birthday' => $request->input('birthday')
        ]);

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        User::whereIn('id', $request->input('id'))->delete();

        return response()->json(['message' => 'ok'], 200);
    }


}
