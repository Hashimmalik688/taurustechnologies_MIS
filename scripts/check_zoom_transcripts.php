<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\ZoomToken;

// Get token
$tokenRecord = ZoomToken::adminApp()
    ->whereNotNull('refresh_token')
    ->orderByDesc('updated_at')
    ->first();

if (!$tokenRecord) {
    die("No admin Zoom token found\n");
}

// Refresh if expired
if ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast()) {
    $r = Http::timeout(15)->asForm()->post('https://zoom.us/oauth/token', [
        'grant_type'    => 'refresh_token',
        'refresh_token' => $tokenRecord->refresh_token,
        'client_id'     => config('zoom.admin_app.client_id'),
        'client_secret' => config('zoom.admin_app.client_secret'),
    ]);
    $data = $r->json();
    $tokenRecord->update([
        'access_token'  => $data['access_token'],
        'refresh_token' => $data['refresh_token'],
        'expires_at'    => now()->addSeconds($data['expires_in'] ?? 3600),
    ]);
    $token = $data['access_token'];
    echo "Token refreshed\n";
} else {
    $token = $tokenRecord->access_token;
    echo "Using cached token (expires: {$tokenRecord->expires_at})\n";
}

// Query Zoom Phone call logs for last 7 days
$today = now()->format('Y-m-d');
$weekAgo = now()->subDays(7)->format('Y-m-d');

echo "\nFetching Zoom Phone call logs from {$weekAgo} to {$today}...\n\n";

$response = Http::timeout(30)
    ->withToken($token)
    ->get('https://api.zoom.us/v2/phone/call_logs', [
        'from'      => $weekAgo,
        'to'        => $today,
        'page_size' => 300,
        'type'      => 'all',
    ]);

if (!$response->successful()) {
    echo "API error: HTTP {$response->status()}\n";
    echo $response->body() . "\n";
    exit(1);
}

$data = $response->json();
$callLogs = $data['call_logs'] ?? $data['records'] ?? [];
$total = count($callLogs);
echo "Total call log records returned: $total\n\n";

// Filter: over 7 min (420s)
$longCalls = array_filter($callLogs, fn($c) => ($c['duration'] ?? 0) >= 420);
echo "Calls over 7 minutes: " . count($longCalls) . "\n\n";

// For each long call, check if it has a recording/transcript
$header = str_pad("Call ID",          26) . str_pad("Duration",10) . str_pad("Caller",18) . str_pad("Callee",18) . str_pad("Agent",22) . str_pad("Time (PT)",22) . str_pad("Recording",12) . "Transcript\n";
echo $header;
echo str_repeat('-', 140) . "\n";

$first = true;
foreach ($longCalls as $call) {
    $callLogId  = $call['id'] ?? $call['call_log_id'] ?? '?';
    $dur        = $call['duration'] ?? 0;
    $mins       = floor($dur / 60) . 'm' . ($dur % 60) . 's';
    $caller     = $call['caller_number'] ?? $call['caller']['phone_number'] ?? '?';
    $callee     = $call['callee_number'] ?? $call['callee']['phone_number'] ?? '?';
    $owner      = $call['owner']['name'] ?? $call['caller']['name'] ?? '?';
    $startRaw   = $call['date_time'] ?? $call['start_time'] ?? '';
    $startMT    = $startRaw ? \Carbon\Carbon::parse($startRaw)->setTimezone('America/Los_Angeles')->format('M j g:ia') : '?';

    // Fetch recordings for this call log
    $recResp = Http::timeout(15)->withToken($token)
        ->get("https://api.zoom.us/v2/phone/call_logs/{$callLogId}/recordings");

    $hasRecording  = false;
    $transcriptUrl = null;

    if ($recResp->successful()) {
        $body = $recResp->json();
        // Dump raw response for first long call for debugging
        if ($first) {
            echo "\n[DEBUG] Raw recordings API response for call {$callLogId}:\n";
            echo json_encode($body, JSON_PRETTY_PRINT) . "\n\n";
            $first = false;
        }
        $recs = $body['recordings'] ?? [];
        foreach ($recs as $rec) {
            $hasRecording = true;
            if (!empty($rec['transcript_download_url'])) {
                $transcriptUrl = 'YES';
                break;
            }
        }
    } else {
        if ($first) {
            echo "\n[DEBUG] Recordings API HTTP {$recResp->status()} for call {$callLogId}:\n" . $recResp->body() . "\n\n";
            $first = false;
        }
    }

    $recFlag   = $hasRecording ? 'YES' : 'no';
    $transFlag = $transcriptUrl ?? 'NONE';

    echo str_pad($callLogId, 26) . str_pad($mins, 10) . str_pad($caller, 18) . str_pad($callee, 18) . str_pad($owner, 22) . str_pad($startMT, 22) . str_pad($recFlag, 12) . $transFlag . "\n";
}

echo "\n";
