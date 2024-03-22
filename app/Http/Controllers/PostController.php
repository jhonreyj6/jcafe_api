<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use Storage;
use App\Models\PostAttachment;
use Str;
use Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(5);
        foreach ($posts as $post) {
            $post->getUserDetails;
            $post->getPostAttachmentImages;
            $post->getPostAttachmentFiles;
            $post->getLikes;
            $post->comment_counts = 0;
            $post->created_time = Carbon::create($post->created_at)->toDayDateTimeString();

            if ($post->getLikes) {
                $post->authLikes = $post->getLikes->where('user_id', Auth::id())->first() ? 1 : 0;
            } else {
                $post->authLikes = 0;
            }


            if ($post->getPostAttachmentImages) {
                foreach ($post->getPostAttachmentImages as $image) {
                    $image->image_url = Storage::disk('s3')->url('posts/files/' . $image->file_link);
                }
            }

            if ($post->getPostAttachmentFiles) {
                foreach ($post->getPostAttachmentFiles as $file) {
                    $file->file_url = Storage::disk('s3')->url('posts/files/' . $file->file_link);
                }
            }

            if ($post->getUserDetails) {
                $post->getUserDetails->image_url = Storage::disk('s3')->url('users/' . $post->user_id . '/images/' . $post->getUserDetails->profile_img);
            }
        }

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['sometimes', 'string', 'nullable', Rule::requiredIf(!$request->file('files'))],
            'files.*' => 'file',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $post = Post::create([
            'message' => Str::of($request->input('message'))->trim(),
            'user_id' => Auth::id(),
        ]);

        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                Storage::disk('s3')->putFileAs('/posts/files', $file, $file->hashName(), 'public');

                $image_or_video = ['jpg', 'png', 'gif', 'bmp'];

                if (in_array($file->getClientOriginalExtension(), $image_or_video)) {
                    $data = getimagesize($file);

                    PostAttachment::create([
                        'post_id' => $post->id,
                        'file_link' => $file->hashName(),
                        'name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_width' => $data[0],
                        'file_height' => $data[1],
                    ]);
                } else {
                    PostAttachment::create([
                        'post_id' => $post->id,
                        'file_link' => $file->hashName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        $post->getUserDetails;
        $post->getComments()->orderBy('created_at', 'desc');
        $post->getPostAttachmentImages;
        $post->getPostAttachmentFiles;
        $post->getLikes;
        $post->comment_counts = 0;
        if ($post->getPostAttachmentImages) {
            foreach ($post->getPostAttachmentImages as $image) {
                $image->image_url = Storage::disk('s3')->url('posts/files/' . $image->file_link);
            }
        }

        if ($post->getPostAttachmentImages) {
            foreach ($post->getPostAttachmentFiles as $file) {
                $file->file_url = Storage::disk('s3')->url('posts/files/' . $file->file_link);
            }
        }

        return $post;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::whereId($id)->paginate(1);
        $post->getCollection()->transform(function ($value) {
            $value->getUserDetails;
            $value->created_time = Carbon::create($value->created_at)->toDayDateTimeString();
            $value->getPostAttachmentImages;
            $value->getPostAttachmentFiles;
            $value->getLikes;
            $value->getUserDetails->image_url = Storage::disk('s3')->url('users/' . $value->getUserDetails['id'] . '/images/' . $value->getUserDetails['profile_img']);

            foreach ($value->getPostAttachmentImages as $image) {
                $image->image_url = Storage::disk('s3')->url('posts/files/' . $image->file_link);
            }

            if ($value->getLikes) {
                $value->authLikes = $value->getLikes->where('user_id', Auth::id())->first() ? 1 : 0;
            } else {
                $value->authLikes = 0;
            }
            return $value;
        });

        return $post;
    }

    public function download(Request $request)
    {
        $file = PostAttachment::where([
            'id' => $request->input('id'),
        ])->firstOrFail();

        return Storage::disk('s3')->url('posts/files/' . $file->file_link);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'exists:posts,id',
            'message' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $post = Post::where(function ($query) use ($request) {
            $query->where('id', $request->input('id'));
            $query->where('user_id', Auth::id());
        })->firstOrFail();
        $post->message = Str::of($request->input('message'))->trim();
        $post->save();

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $post = Post::where(function ($query) use ($request) {
            $query->whereId($request->input('id'));
            $query->where('user_id', Auth::id());
        })->firstOrFail();

        foreach ($post->getPostAttachments as $attachment) {
            Storage::disk('s3')->delete('posts/files/' . $attachment->file_link);
        }

        $post->getComments()->delete();
        $post->getLikes()->delete();
        $post->delete();

        return response()->json(['message' => 'deleted'], 200);
    }
}
