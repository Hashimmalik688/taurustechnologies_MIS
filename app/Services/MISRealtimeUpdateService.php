<?php

namespace App\Services;

use App\Events\MISDataUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MISRealtimeUpdateService
{
    /**
     * Broadcasts a global MIS data update event with lightweight per-key throttling.
     */
    public function publish(array $domains, string $model, ?int $modelId, string $action, array $meta = []): void
    {
        $domains = array_values(array_unique(array_filter(array_map(fn ($d) => strtolower(trim((string) $d)), $domains))));
        if (empty($domains)) {
            return;
        }

        $throttleKey = sprintf(
            'mis:rt:%s:%s:%s:%s',
            implode('|', $domains),
            strtolower($model),
            $action,
            $modelId ?? 0
        );

        if (Cache::has($throttleKey)) {
            return;
        }

        Cache::put($throttleKey, 1, now()->addSeconds(2));

        try {
            broadcast(new MISDataUpdated(
                domains: $domains,
                model: $model,
                modelId: $modelId,
                action: $action,
                meta: $meta,
                actorId: Auth::id(),
            ));
        } catch (\Throwable) {
            // Non-fatal. Domain writes should never fail due to websocket issues.
        }
    }
}
