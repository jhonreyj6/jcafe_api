<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;
use App\Models\User;
use Auth;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';
    protected $fillable = [
        'participant_id',
    ];

    public function getChat()
    {
        return $this->hasMany(Chat::class, 'room_id')->orderBy('created_at','desc');
    }

    public function getUserDetails() {
        return $this->belongsTo(User::class, 'participant_id', 'id');
    }

    public function getUnreadChat() {
        return $this->hasMany(Chat::class, 'room_id')->where(function($value) {
            $value->where('status', 0)
            ->where('user_id', '!=', Auth::id());
        });
    }
}
