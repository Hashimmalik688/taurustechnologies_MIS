<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ZoomPhoneApiService;
use App\Services\QA\ZoomOAuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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
if (!$token) { die("[ERROR] No admin token — authorize via /zoom/admin-authorize first\n"); }

echo "=== Zoom API Recording + Transcript Check (" . date('Y-m-d H:i:s') . ") ===\n\n";

$toProcess = [];

foreach ($calls as $phone => $name) {
    echo "── $name ($phone) ──\n";

    // Find best log record on Mar 09 first, then any date
    $log = DB::table('zoom_webhook_logs')
        ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
        ->where('call_start_time', 'like', '2026-03-09%')
        ->orderByDesc('duration_seconds')
        ->first();

    if (!$log) {
        $log = DB::table('zoom_webhook_logs')
            ->whereRaw("caller_number LIKE '%$phone%' OR callee_number LIKE '%$phone%'")
            ->orderByDesc('duration_seconds')
            ->first();
        if ($log) echo "  [no Mar-09 record, using: {$log->call_start_time}]\n";
    }

    if (!$log) {
        echo "  [ERROR: no webhook log found for this number]\n\n";
        continue;
    }

    $callLogId = $log->zoom_call_id;
    echo "  zoom_call_id: $callLogId\n";
    echo "  log_id      : {$log->id}\n";
    echo "  duration    : " . round($log->duration_seconds / 60, 1) . " min ({$log->duration_seconds}s)\n";
    echo "  agent_id    : " . ($log->agent_id ?? 'unknown') . "\n";

    // ── Query Zoom API for recordings ──────────────────────────────────────
    $resp = Http::timeout(20)->withToken($token)
        ->get("https://api.zoom.us/v2/phone/call_logs/{$callLogId}/recordings");

    if (!$resp->successful()) {
        echo "  [Recordings API] HTTP {$resp->status()}: " . substr($resp->body(), 0, 300) . "\n\n";
        continue;
    }

    $body = $resp->json() ?? [];
    $recs = $body['recordings'] ?? [];
    if (empty($recs) && isset($body['id'])) {
        $recs = [$body]; // flat single-recording response
    }

    if (empty($recs)) {
        echo "  [Recordings API] No recordings. Keys: " . implode(', ', array_keys($body)) . "\n\n";
        continue;
    }

    $bestTranscriptUrl = null;
    $bestDownloadUrl   = null;
    $bestDuration      = 0;
    $agentName         = null;

    foreach ($recs as $i => $rec) {
        $tUrl = $rec['transcript_download_url'] ?? null;
        $dUrl = $rec['download_url'] ?? $rec['file_url'] ?? null;
        $dur  = $rec['duration'] ?? 0;

        echo "  Recording[$i]: type={$rec['recording_type']} dur={$dur}s";
        echo " | audio=" . ($dUrl ? 'YES' : 'NO');
        echo " | transcript=" . ($tUrl ? 'YES' : 'NO') . "\n";

        if ($dur >= $bestDuration) {
            $bestDuration    = $dur;
            $bestDownloadUrl = $dUrl;
            if ($tUrl) $bestTranscriptUrl = $tUrl;
        }

        // Extract agent name from API for VTT parsing
        if (!$agentName) {
            $direction  = $rec['direction'] ?? 'outbound';
            $agentName  = ($direction === 'inbound') ? ($rec['callee_name'] ?? '') : ($rec['caller_name'] ?? '');
        }
    }

    if ($bestTranscriptUrl) {
        echo "  ✅ Transcript available! Downloading...\n";

        // Download the VTT transcript
        $zoomAuth    = app(ZoomOAuthService::class);
        $transcriptText = $zoomAuth->downloadTranscriptFromUrl($bestTranscriptUrl, $agentName ?? '');

        if ($transcriptText) {
            echo "  ✅ Transcript downloaded: " . strlen($transcriptText) . " chars\n";
            $toProcess[$phone] = [
                'name'          => $name,
                'log'           => $log,
                'transcript'    => $transcriptText,
                'duration'      => $log->duration_seconds,
                'agent_name'    => $agentName,
                'transcript_url'=> $bestTranscriptUrl,
            ];
        } else {
            echo "  [ERROR] Transcript download failed (VTT parse error or auth issue)\n";
        }
    } elseif ($bestDownloadUrl) {
        echo "  ⚠️  Audio available but NO transcript yet (Zoom still processing)\n";
        echo "  Audio URL: " . substr($bestDownloadUrl, 0, 80) . "...\n";
    } else {
        echo "  ❌ No audio or transcript available\n";
    }

    // Also update the webhook log if we have a transcript URL
    if ($bestTranscriptUrl) {
        DB::table('zoom_webhook_logs')
            ->where('id', $log->id)
            ->update(['transcript_url' => $bestTranscriptUrl, 'updated_at' => now()]);
        echo "  [DB] transcript_url saved to webhook log {$log->id}\n";
    }

    echo "\n";
}

echo "=== Summary ===\n";
echo count($toProcess) . " / " . count($calls) . " calls have transcripts ready.\n\n";

if (empty($toProcess)) {
    echo "Nothing to process — transcripts not yet available in Zoom.\n";
    exit(0);
}

// ── Dispatch QA for calls that have transcripts ─────────────────────────────
echo "Proceeding to inject transcripts and dispatch QA...\n\n";

foreach ($toProcess as $phone => $data) {
    $log        = $data['log'];
    $agentUser  = $log->agent_id ? \App\Models\User::find($log->agent_id) : null;

    // Get or create QaCall
    $qaCall = \App\Models\QA\QaCall::where('zoom_call_id', $log->zoom_call_id)->first();

    if ($qaCall) {
        // Reset and update with fresh transcript
        $qaCall->update([
            'processing_status'  => 'pending',
            'failure_reason'     => null,
            'transcript_plain'   => $data['transcript'],
            'transcript_diarized'=> $data['transcript'],
            'transcript_source'  => 'zoom',
            'zoom_transcript_url'=> $data['transcript_url'],
        ]);
        echo "✅ Updated existing QaCall #{$qaCall->id} for {$data['name']}\n";
    } else {
        $qaCall = \App\Models\QA\QaCall::create([
            'zoom_call_id'       => $log->zoom_call_id,
            'zoom_call_log_id'   => $log->zoom_call_id,
            'agent_user_id'      => $agentUser?->id,
            'agent_name'         => $agentUser?->name ?? $data['agent_name'] ?? 'Unknown Agent',
            'agent_email'        => $agentUser?->email,
            'caller_number'      => $log->caller_number,
            'callee_number'      => $log->callee_number,
            'duration_seconds'   => $log->duration_seconds,
            'call_start_time'    => $log->call_start_time ?? now(),
            'recording_url'      => null,
            'zoom_transcript_url'=> $data['transcript_url'],
            'transcript_plain'   => $data['transcript'],
            'transcript_diarized'=> $data['transcript'],
            'transcript_source'  => 'zoom',
            'processing_status'  => 'pending',
        ]);
        echo "✅ Created new QaCall #{$qaCall->id} for {$data['name']}\n";
    }

    \App\Jobs\QA\DownloadAndProcessRecording::dispatch($qaCall->id);
    echo "   → Dispatched to qa-processing queue\n";
}

echo "\nDone. Monitor with: php artisan qa:status\n";
