<?php

namespace App\Services\QA;

use App\Models\ZoomToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomOAuthService
{
    /**
     * Get a valid access token from the admin-managed app (app_type = 'admin').
     * Requires scope: phone:read:call_recording:admin
     * Admin must have authorized once via /zoom/admin-authorize.
     */
    public function getAccessToken(): string
    {
        $tokenRecord = ZoomToken::adminApp()
            ->whereNotNull('refresh_token')
            ->orderByDesc('updated_at')
            ->first();

        if (!$tokenRecord) {
            throw new \RuntimeException(
                'No admin Zoom app token found. Authorize the admin app at /zoom/admin-authorize'
            );
        }

        if ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast()) {
            Log::info('[QA:ZoomOAuth] Admin token expired, refreshing...', [
                'user_id' => $tokenRecord->user_id,
            ]);

            $response = Http::timeout(15)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $tokenRecord->refresh_token,
                    'client_id'     => config('zoom.admin_app.client_id'),
                    'client_secret' => config('zoom.admin_app.client_secret'),
                ]);

            if (!$response->successful()) {
                Log::error('[QA:ZoomOAuth] Admin token refresh failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException(
                    'Zoom admin token refresh failed. Re-authorize at /zoom/admin-authorize. Error: ' . $response->body()
                );
            }

            $data = $response->json();
            $tokenRecord->update([
                'access_token'  => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at'    => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);

            Log::info('[QA:ZoomOAuth] Admin token refreshed successfully');
        }

        return $tokenRecord->access_token;
    }

    /**
     * Download a recording MP3 from Zoom to local storage using the admin app token.
     *
     * Strategy:
     *  1. GET /phone/call_logs/{callLogId}/recordings  → fetch fresh signed download_url
     *  2. Stream the signed download_url to disk
     *
     * Requires scope: phone:read:call_recording:admin on the admin-managed app.
     *
     * @param string $downloadUrl  Webhook-provided download URL (used as fallback if API fetch fails)
     * @param string $localPath    Full path to save the MP3 file
     * @param string|null $callLogId  Zoom call_log_id — used to fetch a fresh signed URL (preferred)
     * @param string|null $zoomUserId Unused — kept for backward-compatible call signature
     * @throws \RuntimeException
     */
    public function downloadRecording(string $downloadUrl, string $localPath, ?string $callLogId = null, ?string $zoomUserId = null): void
    {
        $token = $this->getAccessToken();

        $dir = dirname($localPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        Log::info('[QA:ZoomOAuth] Starting recording download', [
            'call_log_id' => $callLogId,
            'local_path'  => $localPath,
        ]);

        // ── Step 1: Fetch a fresh signed URL from the admin recordings API ──
        // The webhook download_url expires quickly; the API always returns a fresh URL.
        $signedUrl = null;
        if ($callLogId) {
            $signedUrl = $this->fetchSignedUrlFromApi($callLogId, $token);
        }

        // ── Step 2: Download the file ────────────────────────────────────────
        // Prefer the fresh signed URL, fall back to original webhook URL
        $urlToDownload = $signedUrl ?? $downloadUrl;

        $response = Http::timeout(300)
            ->withToken($token)
            ->withOptions(['sink' => $localPath])
            ->get($urlToDownload);

        if ($response->successful() && file_exists($localPath) && filesize($localPath) > 1000) {
            $size = filesize($localPath);
            Log::info('[QA:ZoomOAuth] Recording downloaded successfully', [
                'call_log_id' => $callLogId,
                'size_mb'     => round($size / 1048576, 2),
                'method'      => $signedUrl ? 'admin_api_signed_url' : 'webhook_url_fallback',
            ]);
            return;
        }

        if (file_exists($localPath)) {
            @unlink($localPath);
        }

        throw new \RuntimeException(
            'Failed to download recording (HTTP ' . $response->status() . '). '
            . 'Ensure phone:read:call_recording:admin scope is active on the admin app and /zoom/admin-authorize has been completed.'
        );
    }

    /**
     * Fetch a fresh signed download URL from the admin call-log recordings API.
     * GET /phone/call_logs/{callLogId}/recordings
     * Requires: phone:read:call_recording:admin
     */
    private function fetchSignedUrlFromApi(string $callLogId, string $token): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get("https://api.zoom.us/v2/phone/call_logs/{$callLogId}/recordings");

            if ($response->successful()) {
                $body       = $response->json() ?? [];
                $recordings = $body['recordings'] ?? [];

                // Format A: { "recordings": [ { "download_url": "...", ... } ] }
                if (!empty($recordings)) {
                    $url = $recordings[0]['download_url'] ?? $recordings[0]['file_url'] ?? null;
                } else {
                    // Format B: flat root-level object { "download_url": "...", "file_url": "..." }
                    $url = $body['download_url'] ?? $body['file_url'] ?? null;
                }

                if ($url) {
                    Log::info('[QA:ZoomOAuth] Got fresh signed URL from admin API', [
                        'call_log_id' => $callLogId,
                        'format'      => empty($recordings) ? 'flat' : 'array',
                    ]);
                } else {
                    Log::info('[QA:ZoomOAuth] No audio download_url found in recordings API', [
                        'call_log_id'  => $callLogId,
                        'response_keys' => array_keys($body),
                    ]);
                }
                return $url;
            }

            Log::warning('[QA:ZoomOAuth] Admin recordings API returned HTTP ' . $response->status(), [
                'call_log_id' => $callLogId,
                'body'        => substr($response->body(), 0, 200),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[QA:ZoomOAuth] Admin recordings API exception: ' . $e->getMessage(), [
                'call_log_id' => $callLogId,
            ]);
        }

        return null;
    }

    /**
     * Download and parse a Zoom VTT transcript directly from a known URL.
     * Used when the URL has already been captured from the
     * phone.recording_transcript_completed webhook payload.
     */
    public function downloadTranscriptFromUrl(string $transcriptUrl, string $agentName = ''): ?string
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::timeout(30)
                ->withToken($token)
                ->get($transcriptUrl);

            if (!$response->successful()) {
                Log::warning('[QA:ZoomOAuth] Direct transcript URL download failed', [
                    'status' => $response->status(),
                    'url'    => substr($transcriptUrl, 0, 80),
                ]);
                return null;
            }

            $vttContent = $response->body();
            if (empty(trim($vttContent))) {
                return null;
            }

            $labeled = $this->parseVttToLabeledText($vttContent, $agentName);

            Log::info('[QA:ZoomOAuth] Downloaded transcript from direct URL', [
                'chars'          => strlen($labeled),
                'agent_turns'    => substr_count($labeled, "\nAGENT:") + (str_starts_with($labeled, 'AGENT:') ? 1 : 0),
                'customer_turns' => substr_count($labeled, "\nCUSTOMER:"),
            ]);

            return $labeled ?: null;

        } catch (\Throwable $e) {
            Log::warning('[QA:ZoomOAuth] downloadTranscriptFromUrl exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch a Zoom-generated transcript for a call as plain text.
     *
     * Strategy:
     *  1. Call GET /phone/call_logs/{callLogId}/recordings to get transcript_download_url
     *  2. Download the VTT file using the admin token
     *  3. Map VTT speaker names → AGENT: / CUSTOMER: labels using $agentName
     *
     * Returns labeled transcript string (AGENT:/CUSTOMER: prefixed lines), or
     * null if Zoom transcription is unavailable for this call.
     * Requires: phone:read:call_recording:admin
     */
    public function fetchZoomTranscript(string $callLogId, string $agentName = ''): ?string
    {
        try {
            $token = $this->getAccessToken();

            // Step 1: Get transcript_download_url from recordings API
            $response = Http::timeout(15)
                ->withToken($token)
                ->get("https://api.zoom.us/v2/phone/call_logs/{$callLogId}/recordings");

            if (!$response->successful()) {
                Log::warning('[QA:ZoomOAuth] Recordings API failed when fetching transcript URL', [
                    'call_log_id' => $callLogId,
                    'status'      => $response->status(),
                ]);
                return null;
            }

            $body       = $response->json() ?? [];
            $recordings = $body['recordings'] ?? [];

            // The API can return either an array under 'recordings' or a flat single-recording object.
            // Normalise to a single recording object for metadata extraction.
            $recMeta = !empty($recordings) ? (array) $recordings[0] : $body;

            // Auto-detect agent name from the recording's direction + caller/callee names.
            // This is more reliable than the stored agent_name which may be 'Unknown Agent'.
            $direction  = $recMeta['direction'] ?? 'outbound';
            $calleeName = $recMeta['callee_name'] ?? '';
            $callerName = $recMeta['caller_name'] ?? '';
            $agentNameFromApi = ($direction === 'inbound') ? $calleeName : $callerName;

            // Override agentName when the passed value is unhelpful
            $agentNormCheck = strtolower(trim($agentName));
            if (in_array($agentNormCheck, ['', 'unknown agent', 'unknown', 'n/a']) && $agentNameFromApi !== '') {
                $agentName = $agentNameFromApi;
                Log::info('[QA:ZoomOAuth] Using API-derived agent name for VTT parsing', [
                    'call_log_id' => $callLogId,
                    'direction'   => $direction,
                    'agent_name'  => $agentName,
                ]);
            }

            $transcriptUrl = null;

            // Format A: recordings array
            foreach ($recordings as $rec) {
                if (!empty($rec['transcript_download_url'])) {
                    $transcriptUrl = $rec['transcript_download_url'];
                    break;
                }
            }

            // Format B: flat root-level object { "transcript_download_url": "..." }
            if (!$transcriptUrl) {
                $transcriptUrl = $body['transcript_download_url'] ?? null;
            }

            if (!$transcriptUrl) {
                Log::info('[QA:ZoomOAuth] No transcript_download_url in recordings', [
                    'call_log_id'     => $callLogId,
                    'recording_count' => count($recordings),
                    'response_keys'   => array_keys($response->json() ?? []),
                ]);
                return null;
            }

            // Step 2: Download the VTT transcript file
            $vttResponse = Http::timeout(30)
                ->withToken($token)
                ->get($transcriptUrl);

            if (!$vttResponse->successful()) {
                Log::warning('[QA:ZoomOAuth] Transcript VTT download failed', [
                    'call_log_id' => $callLogId,
                    'status'      => $vttResponse->status(),
                ]);
                return null;
            }

            $vttContent = $vttResponse->body();
            if (empty(trim($vttContent))) {
                return null;
            }

            // Step 3: Parse VTT → AGENT:/CUSTOMER: labeled transcript
            $labeled = $this->parseVttToLabeledText($vttContent, $agentName);

            Log::info('[QA:ZoomOAuth] Zoom transcript fetched and labeled', [
                'call_log_id'    => $callLogId,
                'chars'          => strlen($labeled),
                'agent_turns'    => substr_count($labeled, "\nAGENT:") + (str_starts_with($labeled, 'AGENT:') ? 1 : 0),
                'customer_turns' => substr_count($labeled, "\nCUSTOMER:"),
            ]);

            return $labeled ?: null;

        } catch (\Throwable $e) {
            Log::warning('[QA:ZoomOAuth] fetchZoomTranscript exception: ' . $e->getMessage(), [
                'call_log_id' => $callLogId,
            ]);
            return null;
        }
    }

    /**
     * Parse a WebVTT string into an AGENT:/CUSTOMER: labeled transcript.
     *
     * Zoom VTT files embed speaker names via <v SpeakerName> tags on each cue.
     * We map the cue whose speaker name matches $agentName to "AGENT:",
     * and all other speakers to "CUSTOMER:".
     *
     * Falls back to plain text (no labels) if the VTT has no <v> speaker tags.
     */
    private function parseVttToLabeledText(string $vtt, string $agentName = ''): string
    {
        $lines = preg_split('/\r?\n/', $vtt);

        // Collect segments: [['speaker' => string, 'text' => string], ...]
        $segments = [];
        $currentSpeaker = '';
        $currentParts   = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line === 'WEBVTT') continue;
            if (preg_match('/^\d+$/', $line)) continue;            // cue sequence number
            if (str_contains($line, ' --> ')) continue;            // timestamp line
            if (preg_match('/^(NOTE|STYLE|REGION)/', $line)) continue;

            // Detect speaker change via <v SpeakerName> tag
            if (preg_match('/<v ([^>]+)>(.*)/', $line, $m)) {
                // Flush current segment
                if ($currentParts) {
                    $segments[] = ['speaker' => $currentSpeaker, 'text' => implode(' ', $currentParts)];
                    $currentParts = [];
                }
                $currentSpeaker = trim($m[1]);
                $text = trim(preg_replace('/<[^>]+>/', '', $m[2]));
                if ($text !== '') $currentParts[] = $text;
            } else {
                // Continuation line — strip any remaining inline tags
                $text = trim(preg_replace('/<[^>]+>/', '', $line));
                if ($text !== '') $currentParts[] = $text;
            }
        }

        // Flush final segment
        if ($currentParts) {
            $segments[] = ['speaker' => $currentSpeaker, 'text' => implode(' ', $currentParts)];
        }

        // If no speaker tags were present, return plain joined text
        $hasSpeakers = !empty(array_filter($segments, fn($s) => $s['speaker'] !== ''));
        if (!$hasSpeakers) {
            return implode(' ', array_column($segments, 'text'));
        }

        // Map speaker names → AGENT / CUSTOMER
        $agentNorm = strtolower(trim($agentName));

        // Treat generic/uninformative agent names as unknown to avoid false matches.
        // e.g. 'unknown agent' contains 'agent', which would incorrectly match a
        // VTT speaker literally named "Agent".
        if (in_array($agentNorm, ['', 'unknown agent', 'unknown', 'n/a', 'agent', 'customer'])) {
            $agentNorm = '';
        }

        $labeledLines = [];
        foreach ($segments as $seg) {
            if (empty($seg['text'])) continue;

            $speakerNorm = strtolower(trim($seg['speaker']));

            $isAgent = false;
            if ($agentNorm !== '') {
                // Check if either name is a substring of the other (handles partial matches
                // like "James" matching "James Hooper"), but only when the shorter string
                // is at least 4 chars to avoid false positives on short common words.
                $longer  = strlen($speakerNorm) >= strlen($agentNorm) ? $speakerNorm : $agentNorm;
                $shorter = strlen($speakerNorm) <  strlen($agentNorm) ? $speakerNorm : $agentNorm;
                $isAgent = strlen($shorter) >= 4 && str_contains($longer, $shorter);
            }

            $label = $isAgent ? 'AGENT' : 'CUSTOMER';

            $labeledLines[] = $label . ': ' . $seg['text'];
        }

        return implode("\n", $labeledLines);
    }
}
