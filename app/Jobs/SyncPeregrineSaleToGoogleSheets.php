<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Asynchronously syncs a Peregrine validator-approved sale to the
 * MIS Peregrines Leads Google Sheet.
 *
 * Dispatched after every Peregrine validator sale submission.
 * Failures are silently logged and never bubble up to the Validator UI.
 *
 * Set GOOGLE_SHEETS_PEREGRINE_SCRIPT_URL in .env to enable.
 */
class SyncPeregrineSaleToGoogleSheets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 360;

    public function backoff(): array
    {
        return [60, 120, 240, 480];
    }

    public function __construct(public Lead $lead)
    {
    }

    public function handle(GoogleSheetsService $sheets): void
    {
        $sheets->appendPeregrineSale($this->lead);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncPeregrineSaleToGoogleSheets failed for Lead #{$this->lead->id}: {$exception->getMessage()}");
    }
}