<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MISDataUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly array $domains,
        public readonly string $model,
        public readonly ?int $modelId,
        public readonly string $action,
        public readonly array $meta = [],
        public readonly ?int $actorId = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('mis.updates');
    }

    public function broadcastAs(): string
    {
        return '.mis.data.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'domains'    => $this->domains,
            'model'      => $this->model,
            'model_id'   => $this->modelId,
            'action'     => $this->action,
            'meta'       => $this->meta,
            'actor_id'   => $this->actorId,
            'changed_at' => now()->toIso8601String(),
        ];
    }
}
