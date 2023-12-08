<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostAttachment;
use App\Models\PostLike;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
      'user_id',
      'message',
    ];

    // public function __set($name, $value) {
    //     $this->$name = $value;
    // }

    // public function __get($name) {
    //    return $name;
    // }

    public function getUserDetails() {
      return $this->belongsTo(User::class, 'user_id' , 'id');
    }

    public function getComments() {
      return $this->hasMany(Comment::class, 'post_id' , 'id');
    }

    public function getPostAttachments() {
      return $this->hasMany(PostAttachment::class, 'post_id' , 'id');
    }

    public function getLikes() {
        return $this->hasMany(PostLike::class, 'post_id' , 'id');
    }

    public function getPostAttachmentImages() {
        return $this->getPostAttachments()->where(function($query) {
            $query->where('file_link', 'LIKE', '%'.'jpg'.'%')
            ->orWhere('file_link', 'LIKE', '%'.'png'.'%');
        });
    }

    public function getPostAttachmentFiles() {
        return $this->getPostAttachments()->whereNot('file_link', 'LIKE', '%'.'jpg'.'%')->whereNot('file_link', 'LIKE', '%'.'png'.'%');
    }


}
