<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever a Ravens lead mutation should be reflected live on the calling UI.
 */
class RavensLeadUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  int         $leadId
     * @param  string      $action
     * @param  int         $userId
     * @param  string      $userName
     * @param  string|null $status
     * @param  string|null $disposition
     * @param  string|null $callbackNote
     * @param  string|null $callbackUpdatedAt
     * @param  string|null $message
     */
    public function __construct(
        public readonly int $leadId,
        public readonly string $action,
        public readonly int $userId,
        public readonly string $userName,
        public readonly ?string $status = null,
        public readonly ?string $disposition = null,
        public readonly ?string $callbackNote = null,
        public readonly ?string $callbackUpdatedAt = null,
        public readonly ?string $message = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('ravens.calling');
    }

    public function broadcastAs(): string
    {
        return '.lead.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'             => $this->leadId,
            'action'              => $this->action,
            'user_id'             => $this->userId,
            'user_name'           => $this->userName,
            'status'              => $this->status,
            'disposition'         => $this->disposition,
            'callback_note'       => $this->callbackNote,
            'callback_updated_at' => $this->callbackUpdatedAt,
            'message'             => $this->message,
        ];
    }
}
