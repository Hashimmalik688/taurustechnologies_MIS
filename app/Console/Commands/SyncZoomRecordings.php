<?php

namespace App\Console\Commands;

use App\Models\ZoomWebhookLog;
use App\Services\ZoomPhoneApiService;
use Illuminate\Console\Command;

class SyncZoomRecordings extends Command
{
    protected $signature = 'zoom:sync-recordings
                            {--limit=100 : Max pending records to process per run}
                            {--retry : Also retry records marked as not_available}';

    protected $description = 'Backfill recording download URLs for calls pending resolution from Zoom admin API';

    public function handle(ZoomPhoneApiService $service): int
    {
        $adminToken = $service->getAdminAccessToken();
        if (! $adminToken) {
            $this->error('No admin app token found. Authorize at /zoom/admin-authorize first.');
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $retry = $this->option('retry');

        $query = ZoomWebhookLog::whereNotNull('zoom_call_id');

        if ($retry) {
            $query->whereIn('recording_url', ['pending_api_fetch', 'not_available']);
        } else {
            $query->where('recording_url', 'pending_api_fetch');
        }

        $pending = $query->orderBy('call_start_time', 'desc')->limit($limit)->get();

        if ($pending->isEmpty()) {
            $this->info('No pending recording URLs to resolve.');
            return self::SUCCESS;
        }

        $this->info("Resolving {$pending->count()} recording URL(s)...");
        $this->newLine();

        $resolved = 0;
        $failed   = 0;

        foreach ($pending as $log) {
            $url = $service->getRecordingUrl($log);
            if ($url) {
                $log->update(['recording_url' => $url]);
                $resolved++;
                $this->line("  ✓ #{$log->id} — {$log->zoom_call_id}");
            } else {
                $log->update(['recording_url' => 'not_available']);
                $failed++;
                $this->line("  ✗ #{$log->id} — {$log->zoom_call_id} (not available)");
            }
        }

        $this->newLine();
        $this->info("═══════════════════════════════════");
        $this->info("  Resolved:      {$resolved}");
        $this->info("  Not available: {$failed}");
        $this->info("═══════════════════════════════════");

        return self::SUCCESS;
    }
}
