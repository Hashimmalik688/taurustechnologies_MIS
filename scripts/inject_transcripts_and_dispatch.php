<?php
/**
 * Injects Zoom transcript URLs from webhook payloads into QaCall records
 * and re-dispatches the QA jobs.
 *
 * Batch 1 (Mar 09 - original 5 calls): David Wireman, Rhunette Wooten,
 *   George Elliott, Karen Page, Archie Loy
 * Batch 2 (Mar 09 - 4 additional): Lee Watson, Warren/Joeann Gipson (same call)
 */
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QA\QaCall;
use App\Jobs\QA\DownloadAndProcessRecording;
use Illuminate\Support\Facades\DB;

echo "=== Inject Transcripts + Dispatch QA Jobs ===\n\n";

// Maps zoom_call_id => [qa_call_id, webhook_log_id, name]
$mapping = [
    '250cdbf8-0a1c-415f-9aeb-37e5aa08dc33' => [61, 27600, 'David B Wireman'],
    'fb8d3405-251d-4b8d-9c2f-b9a7e7383dbe' => [62, 31141, 'Rhunette E Wooten'],
    'cbe58ed4-78b3-48e5-823b-440c7037f5b8' => [63, 31164, 'George T Elliott Jr'],
    '3a0837ea-c948-4b61-a325-43b74c8caa9a' => [64, 31142, 'Karen J Page'],
    'cc116da3-3f04-44cd-9d81-281ea9f0b7c1' => [67, 27624, 'Archie Loy'],
];

$dispatched = 0;

foreach ($mapping as $callId => [$qaCid, $wlogId, $name]) {
    echo "── $name (QaCall #$qaCid, log #$wlogId) ──\n";

    // Get transcript URL from the transcript_completed webhook payload
    $wlog = DB::table('zoom_webhook_logs')->where('id', $wlogId)->first();
    if (!$wlog) {
        echo "  ERROR: webhook_log id=$wlogId not found\n\n";
        continue;
    }

    $p   = json_decode($wlog->raw_payload, true);
    $rec = $p['payload']['object']['recordings'][0] ?? [];
    $txUrl = $rec['transcript_download_url'] ?? null;

    if (!$txUrl) {
        echo "  ERROR: no transcript_download_url in payload\n";
        echo "  Keys: " . implode(', ', array_keys($rec)) . "\n\n";
        continue;
    }

    echo "  transcript_url: " . substr($txUrl, 0, 80) . "...\n";

    // Update QaCall: inject transcript URL, reset status, clear old results
    $updated = QaCall::where('id', $qaCid)->update([
        'zoom_transcript_url' => $txUrl,
        'processing_status'   => 'pending',
        'failure_reason'      => null,
        'transcript_plain'    => null,
        'transcript_diarized' => null,
        'transcript_source'   => null,
    ]);

    if (!$updated) {
        echo "  WARNING: QaCall #$qaCid not found or not updated\n\n";
        continue;
    }

    // Also store the transcript URL on the webhook log record
    DB::table('zoom_webhook_logs')->where('id', $wlogId)->update([
        'transcript_url' => $txUrl,
    ]);

    // Dispatch fresh job using QaCall ID (job constructor takes qaCallId)
    DownloadAndProcessRecording::dispatch($qaCid)->onQueue('qa-processing');

    echo "  ✓ Updated + dispatched\n\n";
    $dispatched++;
}

echo "=== Done: $dispatched / " . count($mapping) . " jobs dispatched ===\n";
