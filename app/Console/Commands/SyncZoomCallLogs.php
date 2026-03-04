<?php

namespace App\Console\Commands;

use App\Services\ZoomPhoneApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncZoomCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:sync-call-logs
                            {--hours=1 : Number of hours back to sync}
                            {--from= : Override start date (Y-m-d)}
                            {--to= : Override end date (Y-m-d)}';

    /**
     * The console command description.
     */
    protected $description = 'Sync call logs from Zoom Phone API into zoom_webhook_logs table';

    /**
     * Execute the console command.
     */
    public function handle(ZoomPhoneApiService $service): int
    {
        $this->info('Starting Zoom Phone call log sync...');

        // Determine date range
        if ($this->option('from')) {
            $from = Carbon::parse($this->option('from'))->startOfDay();
            $to   = $this->option('to')
                ? Carbon::parse($this->option('to'))->endOfDay()
                : now();
        } else {
            $hours = (int) $this->option('hours');
            $from  = now()->subHours($hours);
            $to    = now();
        }

        $this->info("Date range: {$from->toDateTimeString()} → {$to->toDateTimeString()}");

        // Test authentication first
        $this->info('Authenticating with Zoom API...');
        $token = $service->getAccessToken();

        if (! $token) {
            $this->error('Failed to get Zoom API token. Check logs for details.');
            $this->error('Ensure you have authorized via /zoom/authorize and have a valid token.');
            return self::FAILURE;
        }

        $this->info('Authenticated successfully. Fetching call logs...');

        // Run sync
        $result = $service->syncCallLogs($from, $to);

        // Output results
        $this->newLine();
        $this->info('═══════════════════════════════════');
        $this->info('  Zoom Call Log Sync Results');
        $this->info('═══════════════════════════════════');
        $this->info("  Users synced:         {$result['users_synced']}");
        $this->info("  Users failed:         {$result['users_failed']}");
        $this->info("  API records fetched:  {$result['total_api']}");
        $this->info("  New records synced:   {$result['synced']}");
        $this->info("  Existing (skipped):   {$result['skipped']}");

        if ($result['errors'] > 0) {
            $this->warn("  Errors:               {$result['errors']}");
        } else {
            $this->info("  Errors:               0");
        }

        $this->info('═══════════════════════════════════');
        $this->newLine();

        return $result['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
