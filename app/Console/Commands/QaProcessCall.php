<?php

namespace App\Console\Commands;

use App\Jobs\QA\DownloadAndProcessRecording;
use App\Models\QA\QaCall;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QaProcessCall extends Command
{
    protected $signature = 'qa:process
        {calls* : One or more zoom_call_ids or webhook_log IDs to process}
        {--force : Re-process even if already QA\'d}
        {--agent= : Override agent user ID}';

    protected $description = 'Manually trigger QA processing for specific calls by zoom_call_id (or webhook_log ID with prefix "log:")';

    public function handle(): int
    {
        $inputs = $this->argument('calls');
        $force  = $this->option('force');

        $this->newLine();
        $this->info('QA Manual Processing — ' . count($inputs) . ' call(s)');
        $this->line(str_repeat('─', 60));

        $queued = 0;
        $skipped = 0;

        foreach ($inputs as $input) {
            $this->newLine();

            // Support both "log:12345" (webhook_log id) and plain zoom_call_id
            if (str_starts_with($input, 'log:')) {
                $logId = (int) substr($input, 4);
                $log   = DB::table('zoom_webhook_logs')->where('id', $logId)->first();
                if (!$log) {
                    $this->error("  [log:{$logId}] Not found in zoom_webhook_logs.");
                    $skipped++;
                    continue;
                }
                $zoomCallId   = $log->zoom_call_id;
                $recordingUrl = $log->recording_url;
                $durationSec  = $log->duration_seconds ?? 0;
                $callerNumber = $log->caller_number ?? null;
                $calleeNumber = $log->callee_number ?? null;
                $callLogId    = $log->matched_call_log_id ?? null;
            } else {
                $zoomCallId = $input;
                // Find best matching log entry (prefer recording_completed events)
                $log = DB::table('zoom_webhook_logs')
                    ->where('zoom_call_id', $zoomCallId)
                    ->whereNotNull('recording_url')
                    ->orderByDesc('duration_seconds')
                    ->first();

                if (!$log) {
                    $this->error("  [{$zoomCallId}] No webhook log with a recording_url found.");
                    $skipped++;
                    continue;
                }

                $recordingUrl = $log->recording_url;
                $durationSec  = $log->duration_seconds ?? 0;
                $callerNumber = $log->caller_number ?? null;
                $calleeNumber = $log->callee_number ?? null;
                $callLogId    = $log->matched_call_log_id ?? null;
            }

            $durationMin = round($durationSec / 60, 1);
            $this->line("  Call ID : {$zoomCallId}");
            $this->line("  Duration: {$durationMin} min ({$durationSec}s)");
            $this->line("  Numbers : {$callerNumber} → {$calleeNumber}");

            // Check for existing QaCall
            $existing = QaCall::where('zoom_call_id', $zoomCallId)->first();
            if ($existing && !$force) {
                $this->warn("  Already in QA queue (status: {$existing->processing_status}). Use --force to re-process.");
                $skipped++;
                continue;
            }

            if ($existing && $force) {
                // Reset status so the job re-runs
                $existing->update([
                    'processing_status' => 'pending',
                    'failure_reason'    => null,
                    'transcript_plain'  => null,
                    'transcript_diarized' => null,
                ]);
                $qaCall = $existing;
                $this->line('  Resetting existing record for re-processing...');
            } else {
                // Resolve agent
                $agentUserId = $this->option('agent');
                $agentUser   = $agentUserId ? User::find($agentUserId) : null;

                if (!$agentUser && $log->agent_id ?? null) {
                    $agentUser = User::find($log->agent_id);
                }

                // Create a fresh QaCall from the webhook log data
                $qaCall = QaCall::create([
                    'zoom_call_id'      => $zoomCallId,
                    'zoom_call_log_id'  => $zoomCallId,
                    'agent_user_id'     => $agentUser?->id,
                    'agent_name'        => $agentUser?->name ?? 'Unknown Agent',
                    'agent_email'       => $agentUser?->email,
                    'caller_number'     => $callerNumber,
                    'callee_number'     => $calleeNumber,
                    'duration_seconds'  => $durationSec,
                    'call_start_time'   => $log->call_start_time ?? now(),
                    'recording_url'     => $recordingUrl,
                    'processing_status' => 'pending',
                ]);
            }

            DownloadAndProcessRecording::dispatch($qaCall->id);
            $this->info("  ✓ Dispatched to qa-processing queue (QaCall #{$qaCall->id})");
            $queued++;
        }

        $this->newLine();
        $this->line(str_repeat('─', 60));
        $this->info("Done — {$queued} dispatched, {$skipped} skipped.");
        $this->line('Check progress: php artisan qa:status');
        $this->newLine();

        return 0;
    }
}
