<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a conversation receives a new message, is renamed, or is archived.
 * Broadcast on the recipient's personal channel so the sidebar refreshes instantly
 * without the 60-second conversation-list poll.
 */
class ConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int    $userId         The user whose conversation list should refresh
     * @param int    $conversationId
     * @param string $senderName
     * @param string $preview        Short message preview (no PII — truncated)
     * @param string $updatedAt      Human-readable time
     */
    public function __construct(
        public readonly int    $userId,
        public readonly int    $conversationId,
        public readonly string $senderName,
        public readonly string $preview,
        public readonly string $updatedAt,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return '.conversation.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'sender_name'     => $this->senderName,
            'preview'         => $this->preview,
            'updated_at'      => $this->updatedAt,
        ];
    }
}
