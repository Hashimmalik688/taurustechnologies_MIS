<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a PJC submits a form and assigns it to a closer.
 * Broadcast to the assigned closer's private channel so their dashboard
 * updates instantly — no page reload needed.
 *
 * Frontend: `private user.{closerId}` → `.pjc.lead.submitted`
 */
class PjcLeadSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $closerId,
        public readonly int    $leadId,
        public readonly string $cnName,
        public readonly string $phoneNumber,
        public readonly string $pjcName,
        public readonly string $date,
        public readonly string $status,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->closerId);
    }

    public function broadcastAs(): string
    {
        return 'pjc.lead.submitted';
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'      => $this->leadId,
            'cn_name'      => $this->cnName,
            'phone_number' => $this->phoneNumber,
            'pjc_name'     => $this->pjcName,
            'date'         => $this->date,
            'status'       => $this->status,
        ];
    }
}
