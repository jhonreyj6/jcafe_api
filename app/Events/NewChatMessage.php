<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $chat;


    public function __construct($chat)
    {
        $this->chat = $chat;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->chat->room_id);
    }


    // public function broadcastOn()
    // {
    //     return new Channel('chat');
    // }

    public function broadcastAs()
    {
        return 'new_chat_message';
    }

}
