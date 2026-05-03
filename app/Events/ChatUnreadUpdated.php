<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast to a user's personal private channel when they receive a new chat message.
 * Replaces the 3-second HTTP polling for the chat unread badge.
 * Frontend listens on `private user.{id}` for `.chat.unread`.
 */
class ChatUnreadUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly int    $unreadCount,
        public readonly int    $conversationId,
        public readonly string $senderName,
        public readonly string $preview,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'chat.unread';
    }

    public function broadcastWith(): array
    {
        return [
            'unread_count'    => $this->unreadCount,
            'conversation_id' => $this->conversationId,
            'sender_name'     => $this->senderName,
            'preview'         => $this->preview,
        ];
    }
}
