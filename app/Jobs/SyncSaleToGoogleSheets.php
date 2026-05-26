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
 * Asynchronously syncs a closed/sold lead row to the configured Google Sheet.
 *
 * Dispatched after every Ravens sale submission (submitSale + createSale).
 * Failures are silently logged and never bubble up to the Ravens Closer UI.
 *
 * Queue: default  (set QUEUE_CONNECTION=database in .env for async processing)
 */
class SyncSaleToGoogleSheets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry up to 5 times before giving up. */
    public int $tries = 5;

    /** Queue worker timeout — must exceed the HTTP timeout in GoogleSheetsService. */
    public int $timeout = 360;

    /** Exponential backoff: 60s, 120s, 240s, 480s between retries. */
    public function backoff(): array
    {
        return [60, 120, 240, 480];
    }

    public function __construct(public Lead $lead)
    {
    }

    public function handle(GoogleSheetsService $sheets): void
    {
        $sheets->appendSale($this->lead);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical("PERMANENT FAILURE: Google Sheets sync failed after all retries", [
            'lead_id' => $this->lead->id,
            'customer_name' => $this->lead->cn_name,
            'closer_name' => $this->lead->closer_name,
            'sale_at' => $this->lead->sale_at?->toDateTimeString(),
            'team' => $this->lead->team,
            'error' => $exception->getMessage(),
            'resync_command' => "php artisan sales:resync-google-sheets --lead-id={$this->lead->id}",
        ]);
    }
}
