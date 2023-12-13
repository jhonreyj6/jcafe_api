<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chat';
    protected $fillable= [
      'user_id',
      'message',
      'room_id'
    ];

    public function getUserDetails() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
