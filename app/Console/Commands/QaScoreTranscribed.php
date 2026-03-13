<?php

namespace App\Console\Commands;

use App\Jobs\QA\DownloadAndProcessRecording;
use App\Models\QA\QaCall;
use App\Models\Setting;
use Illuminate\Console\Command;

class QaScoreTranscribed extends Command
{
    protected $signature = 'qa:score-transcribed
        {--dry-run : Show what would be dispatched without actually doing it}
        {--reset-stuck : Reset calls stuck in "scoring" status before redispatching}';

    protected $description = 'Dispatch QA scoring for all calls that have a transcript but are not yet completed';

    public function handle(): int
    {
        $dryRun     = $this->option('dry-run');
        $resetStuck = $this->option('reset-stuck');

        // Respect the global QA enabled toggle
        if (! Setting::get('qa_enabled', true)) {
            $this->warn('QA scoring is currently PAUSED (qa_enabled = false). No jobs dispatched.');
            $this->line('Resume by toggling QA on in the dashboard at /qa/scoring.');
            return self::SUCCESS;
        }

        $calls = QaCall::whereNotNull('transcript_plain')
            ->where('transcript_plain', '!=', '')
            ->whereNotIn('processing_status', ['completed'])
            ->orderBy('id')
            ->get(['id', 'processing_status', 'transcript_source', 'duration_seconds', 'zoom_call_log_id', 'retry_count', 'created_at']);

        if ($calls->isEmpty()) {
            $this->info('No calls with transcripts pending scoring.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Found {$calls->count()} call(s) with transcript ready for scoring:");
        $this->line(str_repeat('─', 70));
        $this->line(sprintf('  %-6s %-14s %-8s %-6s %-8s %s', 'ID', 'STATUS', 'SOURCE', 'DUR(s)', 'RETRIES', 'CREATED'));
        $this->line(str_repeat('─', 70));

        foreach ($calls as $call) {
            $this->line(sprintf(
                '  %-6s %-14s %-8s %-6s %-8s %s',
                $call->id,
                $call->processing_status,
                $call->transcript_source ?? 'unknown',
                $call->duration_seconds ?? '?',
                $call->retry_count ?? 0,
                $call->created_at->format('Y-m-d H:i')
            ));
        }

        $this->line(str_repeat('─', 70));

        if ($dryRun) {
            $this->warn('DRY RUN — no jobs dispatched. Remove --dry-run to proceed.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Dispatch scoring job for {$calls->count()} call(s)?", true)) {
            $this->line('Aborted.');
            return self::SUCCESS;
        }

        $this->newLine();
        $dispatched = 0;

        foreach ($calls as $call) {
            // Reset calls stuck in 'scoring' so the job doesn't think another
            // process is mid-flight (retry_count guard in Step 4 only applies
            // to WhisperX, but a clean status avoids confusion in logs).
            if ($resetStuck && $call->processing_status === 'scoring') {
                $call->update(['processing_status' => 'pending']);
                $this->line("  ↺ Reset qa_call #{$call->id} from 'scoring' → 'pending'");
            }

            DownloadAndProcessRecording::dispatch($call->id);
            $dispatched++;
            $this->line("  ✓ Dispatched qa_call #{$call->id} (source={$call->transcript_source}, dur={$call->duration_seconds}s)");
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════");
        $this->info("  Dispatched: {$dispatched}");
        $this->info("  Queue:      qa-processing");
        $this->info("═══════════════════════════════════════");
        $this->newLine();
        $this->line('Monitor with: php artisan qa:status');

        return self::SUCCESS;
    }
}
