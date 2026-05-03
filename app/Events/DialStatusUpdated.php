<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever a Ravens closer acquires a lock, releases a lock, or records a dial.
 * Broadcast on a public "ravens.calling" channel so all callers see live badge/lock updates
 * without polling /ravens/leads/dial-status every 10 seconds.
 *
 * Payload carries only the delta for that one lead — the frontend merges it in.
 */
class DialStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  int    $leadId
     * @param  string $action   'locked' | 'unlocked' | 'dialed'
     * @param  int    $userId
     * @param  string $userName
     * @param  string $initials  Two-letter initials
     * @param  string $color     Hex colour for badge
     * @param  string|null $dialedAt  Human-readable time (e.g. "2:41 PM") — null for lock events
     * @param  string|null $outcome   Dial outcome, if known
     */
    public function __construct(
        public readonly int    $leadId,
        public readonly string $action,
        public readonly int    $userId,
        public readonly string $userName,
        public readonly string $initials,
        public readonly string $color,
        public readonly ?string $dialedAt = null,
        public readonly ?string $outcome  = null,
    ) {}

    public function broadcastOn(): Channel
    {
        // Public channel — all Ravens callers are authenticated but we don't need
        // private per-user delivery here. Anyone on the calling page should see.
        return new Channel('ravens.calling');
    }

    public function broadcastAs(): string
    {
        return '.dial.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'   => $this->leadId,
            'action'    => $this->action,   // 'locked' | 'unlocked' | 'dialed'
            'user_id'   => $this->userId,
            'user_name' => $this->userName,
            'initials'  => $this->initials,
            'color'     => $this->color,
            'dialed_at' => $this->dialedAt,
            'outcome'   => $this->outcome,
        ];
    }
}
