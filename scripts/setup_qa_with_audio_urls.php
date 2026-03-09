<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ZoomPhoneApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\QA\QaCall;
use App\Models\User;
use App\Jobs\QA\DownloadAndProcessRecording;

// 5 phones from the screenshot
$calls = [
    '3144224455' => 'David B Wireman (Abdullah Ayub)',
    '4012866978' => 'Rhunette E Wooten (Abdullah Ayub)',
    '4798494637' => 'George T Elliott Jr (Attiq ur Rehman)',
    '3036532613' => 'Karen J Page (Farzand Ali)',
    '8564955580' => 'Archie Loy (Farzand Ali)',
];

$svc   = app(ZoomPhoneApiService::class);
$token = $svc->getAdminAccessToken();
if (!$token) { die("[ERROR] No admin token\n"); }

echo "=== Setup QA + Store Audio URLs (" . date('Y-m-d H:i:s') . ") ===\n\n";

foreach ($calls as $phone => $name) {
    echo "── $name ($phone) ──\n";

    // Find best log
    $log = DB::table('zoom_webhook_logs')
        ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
        ->where('call_start_time', 'like', '2026-03-09%')
        ->orderByDesc('duration_seconds')
        ->first();

    if (!$log) {
        $log = DB::table('zoom_webhook_logs')
            ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
            ->orderByDesc('duration_seconds')->first();
        if ($log) echo "  [no Mar-09 record, using: {$log->call_start_time}]\n";
    }

    if (!$log) { echo "  [ERROR: no webhook log found]\n\n"; continue; }

    $callLogId = $log->zoom_call_id;
    echo "  zoom_call_id : $callLogId\n";
    echo "  log_id       : {$log->id}\n";
    echo "  duration     : " . round($log->duration_seconds / 60, 1) . " min\n";

    // Fetch fresh recording info from Zoom API
    $resp = Http::timeout(20)->withToken($token)
        ->get("https://api.zoom.us/v2/phone/call_logs/{$callLogId}/recordings");

    $audioUrl       = null;
    $transcriptUrl  = null;
    $agentName      = null;

    if ($resp->successful()) {
        $body = $resp->json() ?? [];
        $recs = $body['recordings'] ?? [];
        if (empty($recs) && isset($body['id'])) { $recs = [$body]; }

        foreach ($recs as $rec) {
            $audioUrl      = $rec['download_url'] ?? $rec['file_url'] ?? $audioUrl;
            $transcriptUrl = $rec['transcript_download_url'] ?? $transcriptUrl;
            if (!$agentName) {
                $dir       = $rec['direction'] ?? 'outbound';
                $agentName = ($dir === 'inbound') ? ($rec['callee_name'] ?? '') : ($rec['caller_name'] ?? '');
            }
        }
        echo "  audio        : " . ($audioUrl ? 'YES' : 'NO') . "\n";
        echo "  transcript   : " . ($transcriptUrl ? 'YES ← READY!' : 'NO (still processing)') . "\n";
    } else {
        echo "  [Recordings API] HTTP {$resp->status()}\n";
    }

    // Save audio/transcript URLs back to webhook log
    $updateData = ['updated_at' => now()];
    if ($audioUrl)      $updateData['recording_url']   = $audioUrl;
    if ($transcriptUrl) $updateData['transcript_url']  = $transcriptUrl;
    DB::table('zoom_webhook_logs')->where('id', $log->id)->update($updateData);

    // Resolve agent user
    $agentUser = $log->agent_id ? User::find($log->agent_id) : null;

    // Get or create QaCall
    $qaCall = QaCall::where('zoom_call_id', $callLogId)->first();

    $qaData = [
        'zoom_call_log_id'   => $callLogId,
        'agent_user_id'      => $agentUser?->id,
        'agent_name'         => $agentUser?->name ?? $agentName ?? 'Unknown Agent',
        'agent_email'        => $agentUser?->email,
        'caller_number'      => $log->caller_number,
        'callee_number'      => $log->callee_number,
        'duration_seconds'   => $log->duration_seconds,
        'call_start_time'    => $log->call_start_time ?? now(),
        'recording_url'      => $audioUrl,
        'processing_status'  => 'pending',
        'failure_reason'     => null,
        'transcript_plain'   => null,
        'transcript_diarized'=> null,
    ];

    if ($transcriptUrl) {
        $qaData['zoom_transcript_url'] = $transcriptUrl;
    }

    if ($qaCall) {
        $qaCall->update($qaData);
        echo "  → Reset existing QaCall #{$qaCall->id}\n";
    } else {
        $qaCall = QaCall::create(array_merge(['zoom_call_id' => $callLogId], $qaData));
        echo "  → Created new QaCall #{$qaCall->id}\n";
    }

    DownloadAndProcessRecording::dispatch($qaCall->id);
    echo "  → Dispatched to qa-processing queue\n\n";
}

echo "Done. Zoom transcripts typically appear within 5–30 min of call end.\n";
echo "Jobs will auto-retry every 5 min (3× max).\n";
echo "Monitor: php artisan qa:status\n";
