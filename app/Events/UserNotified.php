<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast to a user's personal private channel when they receive a new notification.
 * Frontend listens on `private user.{id}` for `.notification.created`.
 */
class UserNotified implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type  = 'info',
        public readonly string $icon  = 'bx-bell',
        public readonly string $color = 'primary',
        public readonly int    $unreadCount = 0,
        public readonly ?array $data  = null,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'title'        => $this->title,
            'message'      => $this->message,
            'type'         => $this->type,
            'icon'         => $this->icon,
            'color'        => $this->color,
            'unread_count' => $this->unreadCount,
            'data'         => $this->data,
        ];
    }
}
