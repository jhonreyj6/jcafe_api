<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostLike;
use Auth;

class PostLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id)
    {
        $like = PostLike::where(function($query) use ($id) {
            $query->where('post_id', $id)
            ->where('user_id', Auth::id());
        })->first();

        if($like) {
            return $this->destroy($id);

        } else {
            $post = PostLike::create([
                'user_id' => Auth::id(),
                'post_id' => $id,
            ]);

            return response()->json(['message' => 'like', 'data' => $post], 200);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy($id)
    {
        $like = PostLike::where(function($query) use ($id) {
            $query->where('post_id', $id)
            ->where('user_id', Auth::id());
        })->first();

        $like->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
