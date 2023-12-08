<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CommentLike;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
      'post_id',
      'user_id',
      'message'
    ];

    public function getLikes() {
        return $this->hasMany(CommentLike::class, 'comment_id', 'id');
    }
}
