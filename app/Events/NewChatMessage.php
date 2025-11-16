<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->comment->pengaduan_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->comment->id,
            'message' => $this->comment->message,
            'sender' => $this->comment->user->name,
            'time' => $this->comment->created_at->format('H:i'),
            'file' => $this->comment->file_data ? json_decode($this->comment->file_data, true) : null,
        ];
    }
}