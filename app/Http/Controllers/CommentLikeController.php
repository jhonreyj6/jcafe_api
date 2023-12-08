<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentLike;
use Auth;

class CommentLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($post_id, $comment_id)
    {
        Comment::whereId($comment_id)->firstOrFail();
        $like_exist = CommentLike::where(['comment_id' => $comment_id, 'user_id' => Auth::id()])->first();

        if($like_exist) {
            return $this->destroy($like_exist);
        }

        $like = CommentLike::create([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id
        ]);

        return response()->json(['message' => 'like', 'data' => $like], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function destroy(CommentLike $commentLike)
    {
        $commentLike->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
