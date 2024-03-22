<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'post_id',
        'file_link',
        'file_type',
        'file_width',
        'file_height',
    ];

    protected $table = 'post_attachments';
}
