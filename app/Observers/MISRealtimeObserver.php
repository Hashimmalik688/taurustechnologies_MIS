<?php

namespace App\Observers;

use App\Services\MISRealtimeUpdateService;
use Illuminate\Database\Eloquent\Model;

class MISRealtimeObserver
{
    /** @var array<int,string> */
    private array $domains;

    /** @var string */
    private string $modelLabel;

    public function __construct(array $domains, string $modelLabel)
    {
        $this->domains = $domains;
        $this->modelLabel = $modelLabel;
    }

    public function created(Model $model): void
    {
        $this->publish('created', $model);
    }

    public function updated(Model $model): void
    {
        $this->publish('updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->publish('deleted', $model);
    }

    private function publish(string $action, Model $model): void
    {
        app(MISRealtimeUpdateService::class)->publish(
            domains: $this->domains,
            model: $this->modelLabel,
            modelId: (int) ($model->getKey() ?? 0),
            action: $action,
            meta: [
                'table' => $model->getTable(),
            ]
        );
    }
}
