<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$phones = [
    '3144224455' => 'David B Wireman (Abdullah Ayub)',
    '4012866978' => 'Rhunette E Wooten (Abdullah Ayub)',
    '4798494637' => 'George T Elliott Jr (Attiq ur Rehman)',
    '3036532613' => 'Karen J Page (Farzand Ali)',
    '2155195301' => 'Bernell Hudson (Haris Waqar)',
    '9855902841' => 'Tilden R Jenkins (Haris Waqar)',
];

echo "=== QA Pre-Check for 6 Sales Records (Mar 09, 2026) ===\n\n";

foreach ($phones as $phone => $name) {
    echo "Phone: $phone ($name)\n";

    // Find best log - prefer Mar 09 longest call
    $log = DB::table('zoom_webhook_logs')
        ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
        ->where('call_start_time', 'like', '2026-03-09%')
        ->orderByDesc('duration_seconds')
        ->first();

    if (!$log) {
        // Fallback: any date
        $log = DB::table('zoom_webhook_logs')
            ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
            ->orderByDesc('duration_seconds')
            ->first();
        echo "  [WARNING] No Mar-09 record found, using best overall\n";
    }

    if (!$log) {
        echo "  [ERROR] No call log found at all!\n\n";
        continue;
    }

    $dur  = round($log->duration_seconds / 60, 1);
    $recUrl   = !empty($log->recording_url)   ? 'YES' : 'NO';
    $recId    = !empty($log->recording_id)    ? substr($log->recording_id, 0, 20) : 'none';
    $transUrl = !empty($log->transcript_url)  ? 'YES' : 'NO';
    $transText= !empty($log->transcript_text) ? strlen($log->transcript_text).'ch' : 'none';

    echo "  log_id  : {$log->id}\n";
    echo "  zoom_id : {$log->zoom_call_id}\n";
    echo "  duration: {$dur} min ({$log->duration_seconds}s)\n";
    echo "  caller  : {$log->caller_number} -> {$log->callee_number}\n";
    echo "  event   : {$log->event_type}\n";
    echo "  agent_id: {$log->agent_id}\n";
    echo "  rec_url : {$recUrl}  rec_id: {$recId}\n";
    echo "  trans_url: {$transUrl}  trans_text: {$transText}\n";

    // Check existing QA
    $qa = DB::table('qa_calls')
        ->where('zoom_call_id', $log->zoom_call_id)
        ->first(['id', 'processing_status', 'scored_by', 'created_at']);
    if ($qa) {
        echo "  [QA EXISTING] qa_call_id={$qa->id} status={$qa->processing_status} scored_by={$qa->scored_by}\n";
    } else {
        echo "  [QA STATUS ] Not yet in QA queue\n";
    }
    echo "\n";
}
