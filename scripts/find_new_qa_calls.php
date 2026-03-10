<?php
/**
 * Find leads by phone number and locate their Zoom phone call recordings + transcript webhooks.
 * For use when creating new QaCall records for fresh calls.
 */
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\QA\QaCall;

// Phones from screenshot (excluding Archie Loy 8564955580 already done as #67)
$phones = [
    '9795961758',  // Lee Charles Watson - Haris Waqar
    '3132450873',  // Warren Gipson / Joeann Gipson - Haris Waqar
];

echo "=== Finding leads + Zoom data for new QA calls ===\n\n";

foreach ($phones as $phone) {
    echo "── Phone: $phone ──\n";
    $leads = DB::table('leads')
        ->where('phone_number', $phone)
        ->orWhere('phone_number', 'like', '%' . $phone . '%')
        ->orderByDesc('sale_date')
        ->get(['id', 'cn_name', 'phone_number', 'closer_id', 'closer_name', 'sale_date', 'carrier_name', 'status']);

    foreach ($leads as $l) {
        echo "  Lead #{$l->id}: {$l->cn_name}, closer={$l->closer_name}(#{$l->closer_id}), sale_date={$l->sale_date}, carrier={$l->carrier_name}, status={$l->status}\n";
    }

    // Check existing QaCalls for this phone
    $qaCalls = QaCall::where('customer_phone', 'like', '%' . $phone . '%')
        ->orWhere('customer_phone', $phone)
        ->orWhere('customer_phone', '+1' . $phone)
        ->get(['id', 'customer_phone', 'processing_status', 'zoom_call_id', 'agent_name']);
    foreach ($qaCalls as $qc) {
        echo "  Existing QaCall #{$qc->id}: {$qc->customer_phone}, agent={$qc->agent_name}, status={$qc->processing_status}, zoom={$qc->zoom_call_id}\n";
    }

    // Search zoom_webhook_logs for transcript_completed events for this phone
    $txLogs = DB::table('zoom_webhook_logs')
        ->where('event', 'phone.recording_transcript_completed')
        ->where('raw_payload', 'like', '%' . $phone . '%')
        ->orderByDesc('id')
        ->get(['id', 'event', 'created_at']);
    foreach ($txLogs as $tl) {
        echo "  transcript_completed log #{$tl->id} at {$tl->created_at}\n";
        $p = json_decode(DB::table('zoom_webhook_logs')->where('id', $tl->id)->value('raw_payload'), true);
        $obj = $p['payload']['object'] ?? [];
        echo "    call_id=" . ($obj['id'] ?? 'n/a') . " date_time=" . ($obj['date_time'] ?? 'n/a') . "\n";
        $rec = $obj['recordings'][0] ?? [];
        echo "    tx_url=" . substr($rec['transcript_download_url'] ?? 'NONE', 0, 80) . "\n";
    }

    // Search recording_completed events too
    $recLogs = DB::table('zoom_webhook_logs')
        ->where('event', 'phone.recording_completed')
        ->where('raw_payload', 'like', '%' . $phone . '%')
        ->orderByDesc('id')
        ->get(['id', 'event', 'created_at']);
    foreach ($recLogs as $rl) {
        echo "  recording_completed log #{$rl->id} at {$rl->created_at}\n";
        $p = json_decode(DB::table('zoom_webhook_logs')->where('id', $rl->id)->value('raw_payload'), true);
        $obj = $p['payload']['object'] ?? [];
        echo "    call_id=" . ($obj['id'] ?? 'n/a') . " callee=" . ($obj['callee_number'] ?? 'n/a') . "\n";
    }
    echo "\n";
}

// Also show all transcript_completed logs from Mar 09-10 not yet assigned to a QaCall
echo "=== All transcript_completed logs (Mar 09-10 2026) ===\n";
$allTx = DB::table('zoom_webhook_logs')
    ->where('event', 'phone.recording_transcript_completed')
    ->where('created_at', '>=', '2026-03-09 00:00:00')
    ->orderByDesc('id')
    ->get(['id', 'created_at', 'raw_payload']);

foreach ($allTx as $tl) {
    $p = json_decode($tl->raw_payload, true);
    $obj = $p['payload']['object'] ?? [];
    $rec = $obj['recordings'][0] ?? [];
    $callId = $obj['id'] ?? 'n/a';
    $hasUrl = !empty($rec['transcript_download_url']) ? 'HAS_URL' : 'NO_URL';
    // Check if QaCall exists for this call_id
    $existing = QaCall::where('zoom_call_id', 'like', '%' . substr($callId, 0, 8) . '%')->first(['id', 'processing_status']);
    $qcInfo = $existing ? "QaCall#{$existing->id}({$existing->processing_status})" : "NO_QACALL";
    echo "  Log #{$tl->id} @{$tl->created_at}: call_id=$callId $hasUrl $qcInfo\n";
}
