<?php
/**
 * Batch 2 QA dispatch: Lee Watson + Warren/Joeann Gipson (Mar 09, 2026)
 *
 * Lee Watson (#59): Already has transcript URL, just needs status reset.
 * Gipson (new):    New QaCall for zoom_call_id 069aeeb9cc5480b2a80 (25.7 min call).
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QA\QaCall;
use App\Jobs\QA\DownloadAndProcessRecording;

echo "=== Batch 2 QA Dispatch: Lee Watson + Gipson ===\n\n";

// ── 1. Lee Watson (QaCall #59) ───────────────────────────────────────────────
echo "── Lee Charles Watson (QaCall #59) ──\n";
$leeCall = QaCall::find(59);
if ($leeCall) {
    $leeCall->update([
        'processing_status' => 'pending',
        'failure_reason'    => null,
        'transcript_plain'  => null,
        'transcript_diarized' => null,
        'transcript_source' => null,
        'retry_count'       => 0,
    ]);
    DownloadAndProcessRecording::dispatch(59)->onQueue('qa-processing');
    echo "  ✓ Reset to pending + dispatched\n";
    echo "  zoom_call_id: {$leeCall->zoom_call_id}\n";
    echo "  transcript_url: " . substr($leeCall->zoom_transcript_url, 0, 80) . "...\n\n";
} else {
    echo "  ERROR: QaCall #59 not found\n\n";
}

// ── 2. Warren/Joeann Gipson (new QaCall) ─────────────────────────────────────
$gipsonZoomCallId = '069aeeb9cc5480b2a80';
$gipsonTxUrl      = 'https://zoom.us/v2/phone/recording_transcript/download/550ec3ca3ad64d7987f18a93be4831c6';
$gipsonLogId      = '550ec3ca-3ad6-4d79-87f1-8a93be4831c6';  // zoom_call_log_id
$gipsonStartTime  = '2026-03-09 09:48:02'; // recording ended @10:13:46 minus 1544s

echo "── Warren/Joeann Gipson (new QaCall) ──\n";

// Check if already exists
$existing = QaCall::where('zoom_call_id', $gipsonZoomCallId)->first();
if ($existing) {
    echo "  Already exists as QaCall #{$existing->id} (status={$existing->processing_status})\n";
    // Reset and re-dispatch if not recently completed
    if ($existing->processing_status !== 'completed') {
        $existing->update([
            'zoom_transcript_url' => $gipsonTxUrl,
            'processing_status'   => 'pending',
            'failure_reason'      => null,
            'transcript_plain'    => null,
            'transcript_diarized' => null,
            'transcript_source'   => null,
            'retry_count'         => 0,
        ]);
        DownloadAndProcessRecording::dispatch($existing->id)->onQueue('qa-processing');
        echo "  ✓ Reset + dispatched\n\n";
    } else {
        echo "  Already completed, skipping\n\n";
    }
} else {
    // Create new QaCall
    $gipsonCall = QaCall::create([
        'zoom_call_id'        => $gipsonZoomCallId,
        'agent_user_id'       => 20,              // Haris Waqar
        'agent_name'          => 'Haris Waqar',
        'agent_email'         => 'davidhariss37@gmail.com',
        'zoom_user_id'        => 'Jr14svAdSXGsMrUywRSFsA',
        'zoom_call_log_id'    => $gipsonLogId,
        'caller_number'       => '805',
        'callee_number'       => '+13132450873',
        'duration_seconds'    => 1544,
        'call_start_time'     => $gipsonStartTime,
        'zoom_transcript_url' => $gipsonTxUrl,
        'processing_status'   => 'pending',
        'retry_count'         => 0,
    ]);
    echo "  ✓ Created QaCall #{$gipsonCall->id}\n";
    DownloadAndProcessRecording::dispatch($gipsonCall->id)->onQueue('qa-processing');
    echo "  ✓ Dispatched\n";
    echo "  zoom_call_id: $gipsonZoomCallId\n";
    echo "  callee: +13132450873 (Warren/Joeann Gipson)\n";
    echo "  duration: 1544s (~25.7 min)\n\n";
}

echo "=== Done — monitor with: php artisan qa:status ===\n";
