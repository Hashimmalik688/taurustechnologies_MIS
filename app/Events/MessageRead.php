<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $conversationId;
    public int $userId;
    public string $readAt;

    public function __construct(int $conversationId, int $userId, string $readAt)
    {
        $this->conversationId = $conversationId;
        $this->userId         = $userId;
        $this->readAt         = $readAt;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.conversation.' . $this->conversationId);
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'user_id'         => $this->userId,
            'read_at'         => $this->readAt,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }
}
