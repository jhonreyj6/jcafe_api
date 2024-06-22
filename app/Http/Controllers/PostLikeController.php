<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostLike;
use Auth;

class PostLikeController extends Controller
{

    public function store($id)
    {
        $like = PostLike::where(function ($query) use ($id) {
            $query->where('post_id', $id)
                ->where('user_id', Auth::id());
        })->first();

        if ($like) {
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $like = PostLike::where(function ($query) use ($id) {
            $query->where('post_id', $id)
                ->where('user_id', Auth::id());
        })->first();

        $like->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
