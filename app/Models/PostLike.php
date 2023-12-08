<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'post_id',
      'reaction'
    ];

    protected $table = 'post_likes';
    protected $casts = [
        'user_id' => 'integer',
        'post_id' => 'integer',
    ];


}
