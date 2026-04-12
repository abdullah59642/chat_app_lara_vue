<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PublicChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $user;
    // public $conversation;
    public $message;
    public $conversation;
    /**
     * Create a new event instance.
     */
    public function __construct(Conversation $conversation, $message)
    {
        // $this->user = $user;
        // $this->conversation = $conversation;
        $this->message = $message;
        $this->conversation = $conversation;
    }

    public function broadcastWith()
    {
        return [
            'conversation_id' => $this->conversation->id,
            'message' => $this->message,
        ];


    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('chat');
        
    }
}
