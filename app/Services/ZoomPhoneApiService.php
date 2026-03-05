<?php

namespace App\Services;

use App\Models\ZoomToken;
use App\Models\ZoomWebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ZoomPhoneApiService
{
    private ?string $accessToken = null;

    // ─── Authentication ─────────────────────────────────────────────

    /**
     * Get a valid OAuth access token from zoom_tokens table.
     * Refreshes automatically if expired using the refresh_token grant.
     */
    public function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        // Get the most recent token (prefer non-expired, fall back to any)
        $tokenRecord = ZoomToken::active()->orderByDesc('expires_at')->first()
            ?? ZoomToken::orderByDesc('expires_at')->first();

        if (! $tokenRecord) {
            Log::error('[ZoomAPI] No OAuth token found in zoom_tokens table. Please authorize via /zoom/authorize first.');
            return null;
        }

        // Refresh if expired
        if ($tokenRecord->isExpired()) {
            $tokenRecord = $this->refreshToken($tokenRecord);
            if (! $tokenRecord) {
                return null;
            }
        }

        $this->accessToken = $tokenRecord->access_token;
        return $this->accessToken;
    }

    /**
     * Get a valid access token for a specific ZoomToken record.
     * Refreshes automatically if expired.
     */
    public function getAccessTokenForRecord(ZoomToken $tokenRecord): ?string
    {
        if ($tokenRecord->isExpired()) {
            $tokenRecord = $this->refreshToken($tokenRecord);
            if (! $tokenRecord) {
                return null;
            }
        }

        return $tokenRecord->access_token;
    }

    /**
     * Refresh an expired OAuth token using the refresh_token grant.
     * Uses admin app credentials for admin tokens, user app credentials for user tokens.
     */
    private function refreshToken(ZoomToken $tokenRecord): ?ZoomToken
    {
        $isAdmin      = $tokenRecord->app_type === 'admin';
        $clientId     = $isAdmin ? config('zoom.admin_app.client_id')     : config('zoom.oauth.client_id');
        $clientSecret = $isAdmin ? config('zoom.admin_app.client_secret') : config('zoom.oauth.client_secret');

        if (! $clientId || ! $clientSecret) {
            Log::error('[ZoomAPI] Missing ' . ($isAdmin ? 'admin' : 'OAuth') . ' client_id/client_secret in config/zoom.php');
            return null;
        }

        if (! $tokenRecord->refresh_token) {
            Log::error('[ZoomAPI] No refresh_token available. Re-authorize via /zoom/authorize.');
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $tokenRecord->refresh_token,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $tokenRecord->update([
                    'access_token'  => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at'    => now()->addSeconds($data['expires_in']),
                ]);

                Log::info('[ZoomAPI] Token refreshed successfully', [
                    'user_id'    => $tokenRecord->user_id,
                    'expires_at' => $tokenRecord->expires_at,
                ]);

                return $tokenRecord->fresh();
            }

            Log::error('[ZoomAPI] Token refresh failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('[ZoomAPI] Token refresh exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get a valid access token for the admin-managed app specifically.
     */
    public function getAdminAccessToken(): ?string
    {
        $adminTokenRecord = ZoomToken::adminApp()->orderByDesc('expires_at')->first();
        if (! $adminTokenRecord) {
            Log::warning('[ZoomAPI] No admin app token found. Authorize at /zoom/admin-authorize.');
            return null;
        }
        return $this->getAccessTokenForRecord($adminTokenRecord);
    }

    /**
     * Get the recording download URL for a ZoomWebhookLog entry.
     * Primary: GET /phone/call_logs/{callLogId}/recordings  (scope: phone:read:call_recording:admin)
     * Fallback: GET /phone/recordings/{recordingId}          (scope: phone:read:call_recording:admin)
     */
    public function getRecordingUrl(ZoomWebhookLog $log): ?string
    {
        $adminToken = $this->getAdminAccessToken();
        if (! $adminToken) {
            return null;
        }

        // Strategy 1: Use zoom_call_id to list recordings for that specific call
        // Endpoint: GET /phone/call_logs/{callLogId}/recordings
        if ($log->zoom_call_id) {
            try {
                $response = Http::timeout(15)->withToken($adminToken)
                    ->get("https://api.zoom.us/v2/phone/call_logs/{$log->zoom_call_id}/recordings");

                if ($response->successful()) {
                    $recordings = $response->json('recordings') ?? [];
                    if (! empty($recordings)) {
                        $rec = $recordings[0]; // Most calls have one recording
                        if (! empty($rec['id'])) {
                            $log->update(['recording_id' => $rec['id']]);
                        }
                        // Prefer download_url, fall back to file_url
                        return $rec['download_url'] ?? $rec['file_url'] ?? null;
                    }
                } elseif ($response->status() !== 404) {
                    Log::warning('[ZoomAPI] call_logs recordings endpoint failed', [
                        'call_id' => $log->zoom_call_id,
                        'status'  => $response->status(),
                        'body'    => substr($response->body(), 0, 200),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[ZoomAPI] call_logs recordings exception: ' . $e->getMessage());
            }
        }

        // Strategy 2: Use a stored recording_id directly
        // Endpoint: GET /phone/recordings/{recordingId}
        $recordingId = $log->recording_id
            ?? (is_array($log->raw_payload) ? ($log->raw_payload['recording_id'] ?? null) : null);

        if ($recordingId) {
            $url = $this->fetchDownloadUrlByRecordingId($adminToken, $recordingId);
            if ($url) return $url;
        }

        return null;
    }

    /**
     * Fetch a fresh signed download URL for a known recording ID.
     */
    private function fetchDownloadUrlByRecordingId(string $token, string $recordingId): ?string
    {
        try {
            $response = Http::timeout(15)->withToken($token)
                ->get("https://api.zoom.us/v2/phone/recordings/{$recordingId}");
            if ($response->successful()) {
                return $response->json('download_url');
            }
            Log::warning('[ZoomAPI] Recording fetch failed', [
                'recording_id' => $recordingId,
                'status'       => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('[ZoomAPI] fetchDownloadUrl exception: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Test the connection — returns user info or null.
     */
    public function testConnection(): ?array
    {
        $token = $this->getAccessToken();
        if (! $token) return null;

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get('https://api.zoom.us/v2/users/me');

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('[ZoomAPI] Test connection failed: ' . $e->getMessage());
        }

        return null;
    }

    // ─── Call Logs ──────────────────────────────────────────────────

    /**
     * Fetch call logs from Zoom Phone API for a date range.
     * Uses: GET /phone/call_logs
     * Docs: https://developers.zoom.us/docs/api/rest/reference/phone/methods/#operation/accountCallLogs
     *
     * @param  Carbon  $from   Start date
     * @param  Carbon  $to     End date
     * @return array   Array of call log entries
     */
    public function getCallLogs(Carbon $from, Carbon $to): array
    {
        $token = $this->getAccessToken();
        if (! $token) return [];

        $allLogs = [];
        $nextPageToken = null;
        $page = 0;
        $pageSize = config('zoom.call_logs.max_page_size', 300);

        do {
            $page++;
            try {
                $query = [
                    'from'      => $from->format('Y-m-d'),
                    'to'        => $to->format('Y-m-d'),
                    'page_size' => $pageSize,
                    'type'      => 'all', // all call types
                ];

                if ($nextPageToken) {
                    $query['next_page_token'] = $nextPageToken;
                }

                $response = Http::timeout(30)
                    ->withToken($token)
                    ->get('https://api.zoom.us/v2/phone/call_logs', $query);

                if (! $response->successful()) {
                    Log::error('[ZoomAPI] Call logs fetch failed', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                        'page'   => $page,
                    ]);
                    break;
                }

                $data = $response->json();
                $logs = $data['call_logs'] ?? [];
                $allLogs = array_merge($allLogs, $logs);
                $nextPageToken = $data['next_page_token'] ?? null;

                Log::info("[ZoomAPI] Fetched page {$page}: " . count($logs) . ' call logs');

            } catch (\Exception $e) {
                Log::error('[ZoomAPI] Call logs exception on page ' . $page . ': ' . $e->getMessage());
                break;
            }

            // Safety: max 20 pages (6000 records)
        } while ($nextPageToken && $page < 20);

        return $allLogs;
    }

    /**
     * Fetch call logs for a specific user.
     * Uses: GET /phone/users/{userId}/call_logs
     */
    public function getUserCallLogs(string $userId, Carbon $from, Carbon $to): array
    {
        $token = $this->getAccessToken();
        if (! $token) return [];

        $allLogs = [];
        $nextPageToken = null;
        $page = 0;

        do {
            $page++;
            try {
                $query = [
                    'from'      => $from->format('Y-m-d'),
                    'to'        => $to->format('Y-m-d'),
                    'page_size' => 300,
                    'type'      => 'all',
                ];

                if ($nextPageToken) {
                    $query['next_page_token'] = $nextPageToken;
                }

                $response = Http::timeout(30)
                    ->withToken($token)
                    ->get("https://api.zoom.us/v2/phone/users/{$userId}/call_logs", $query);

                if (! $response->successful()) {
                    Log::error("[ZoomAPI] User call logs failed for {$userId}", [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    break;
                }

                $data = $response->json();
                $logs = $data['call_logs'] ?? [];
                $allLogs = array_merge($allLogs, $logs);
                $nextPageToken = $data['next_page_token'] ?? null;

            } catch (\Exception $e) {
                Log::error("[ZoomAPI] User call logs exception: " . $e->getMessage());
                break;
            }
        } while ($nextPageToken && $page < 10);

        return $allLogs;
    }

    // ─── Sync to Database ───────────────────────────────────────────

    /**
     * Sync Zoom API call logs into zoom_webhook_logs table.
     *
     * Strategy:
     *   1. PRIMARY — Use the admin account token (user_id=1 / Hashim) to call
     *      GET /phone/call_logs which returns ALL 19 extensions' history in one
     *      paginated request. Requires scope: phone:read:list_call_logs:admin
     *      (add in Zoom Marketplace → Scopes, then re-authorize via /zoom/authorize).
     *   2. FALLBACK — If admin endpoint returns 400/403 (scope not yet added),
     *      fall back to iterating per-user tokens (covers 11 of 19 users).
     *
     * Call logs are intentionally NOT linked to CRM user IDs — closers are
     * tracked by extension/name independently of CRM account creation.
     *
     * @return array  ['sync_mode' => string, 'synced' => int, 'skipped' => int, 'errors' => int, 'total_api' => int, 'users_synced' => int, 'users_failed' => int]
     */
    public function syncCallLogs(Carbon $from, Carbon $to): array
    {
        $result = ['sync_mode' => 'per-user (fallback)', 'synced' => 0, 'skipped' => 0, 'errors' => 0, 'total_api' => 0, 'users_synced' => 0, 'users_failed' => 0];

        // ── Step 1: Try account-level endpoint with admin token ───────────
        $adminLogs = $this->fetchAccountCallLogs($from, $to);

        if ($adminLogs !== null) {
            $result['sync_mode']    = 'account-level (admin)';
            $result['total_api']    = count($adminLogs);
            $result['users_synced'] = 1;

            Log::info('[ZoomAPI] Account-level sync: ' . count($adminLogs) . ' records from all extensions');

            $seenCallIds = [];
            foreach ($adminLogs as $log) {
                $callId = $log['id'] ?? null;
                if (! $callId || isset($seenCallIds[$callId])) {
                    if (! $callId) $result['errors']++;
                    continue;
                }
                $seenCallIds[$callId] = true;
                try {
                    $this->upsertCallLog($log, $result);
                } catch (\Exception $e) {
                    Log::error('[ZoomAPI] Sync error for call: ' . $callId, ['error' => $e->getMessage()]);
                    $result['errors']++;
                }
            }

            Log::info('[ZoomAPI] Account-level sync complete', $result);
            return $result;
        }

        // ── Step 2: Fallback — per-user token iteration ───────────────────
        Log::warning('[ZoomAPI] Admin endpoint unavailable — falling back to per-user sync. '
            . 'Add scope phone:read:list_call_logs:admin in Zoom Marketplace then re-authorize.');

        // Get all tokens with refresh_tokens (we can try to refresh expired ones)
        $tokens = ZoomToken::whereNotNull('refresh_token')
            ->where('refresh_token', '!=', '')
            ->get();

        if ($tokens->isEmpty()) {
            Log::error('[ZoomAPI] No tokens with refresh_tokens found. Users must authorize via /zoom/authorize.');
            return $result;
        }

        Log::info("[ZoomAPI] Starting per-user sync across {$tokens->count()} token(s)", [
            'from' => $from->toDateTimeString(),
            'to'   => $to->toDateTimeString(),
        ]);

        // Track seen call IDs to avoid duplicate processing
        $seenCallIds = [];

        foreach ($tokens as $tokenRecord) {
            $token = $this->getAccessTokenForRecord($tokenRecord);
            if (! $token) {
                Log::warning("[ZoomAPI] Failed to get token for user_id {$tokenRecord->user_id}");
                $result['users_failed']++;
                continue;
            }

            // Fetch this user's call logs via /phone/users/me/call_logs
            $userLogs = $this->fetchUserCallLogs($token, $from, $to);

            if ($userLogs === null) {
                $result['users_failed']++;
                continue;
            }

            $result['users_synced']++;
            $result['total_api'] += count($userLogs);

            foreach ($userLogs as $log) {
                $callId = $log['id'] ?? null;
                if (! $callId) {
                    $result['errors']++;
                    continue;
                }

                // Skip if we already processed this call ID in this run
                if (isset($seenCallIds[$callId])) {
                    continue;
                }
                $seenCallIds[$callId] = true;

                try {
                    $this->upsertCallLog($log, $result);
                } catch (\Exception $e) {
                    Log::error('[ZoomAPI] Sync error for call: ' . $callId, [
                        'error' => $e->getMessage(),
                    ]);
                    $result['errors']++;
                }
            }
        }

        Log::info('[ZoomAPI] Per-user sync complete', $result);
        return $result;
    }

    /**
     * Fetch ALL account call logs via GET /phone/call_logs using the admin-managed app token.
     * Requires scope: phone:read:list_call_logs:admin on the admin-managed app.
     * Returns null if token is unavailable or scope is missing, so caller falls back.
     */
    private function fetchAccountCallLogs(Carbon $from, Carbon $to): ?array
    {
        // Use the admin-managed app token specifically (app_type = 'admin')
        $adminTokenRecord = ZoomToken::adminApp()
            ->orderByDesc('expires_at')
            ->first();

        if (! $adminTokenRecord) {
            Log::info('[ZoomAPI] No admin app token found. Authorize at /zoom/admin-authorize to enable account-level sync.');
            return null;
        }

        $token = $this->getAccessTokenForRecord($adminTokenRecord);
        if (! $token) {
            Log::warning('[ZoomAPI] Admin app token refresh failed.');
            return null;
        }

        $allLogs       = [];
        $nextPageToken = null;
        $page          = 0;
        $pageSize      = config('zoom.call_logs.max_page_size', 300);

        do {
            $page++;
            try {
                $query = [
                    'from'      => $from->format('Y-m-d'),
                    'to'        => $to->format('Y-m-d'),
                    'page_size' => $pageSize,
                    'type'      => 'all',
                ];
                if ($nextPageToken) {
                    $query['next_page_token'] = $nextPageToken;
                }

                $response = Http::timeout(30)
                    ->withToken($token)
                    ->get('https://api.zoom.us/v2/phone/call_logs', $query);

                if ($response->status() === 400 || $response->status() === 403) {
                    Log::warning('[ZoomAPI] Account-level call_logs returned ' . $response->status() . '. '
                        . 'Add scope phone:read:list_call_logs:admin and re-authorize at /zoom/authorize.');
                    return null;
                }

                if (! $response->successful()) {
                    Log::error('[ZoomAPI] Account call_logs error', [
                        'status' => $response->status(),
                        'body'   => substr($response->body(), 0, 300),
                    ]);
                    return $page === 1 ? null : $allLogs;
                }

                $data          = $response->json();
                $logs          = $data['call_logs'] ?? [];
                $allLogs       = array_merge($allLogs, $logs);
                $nextPageToken = $data['next_page_token'] ?? null;

                Log::info("[ZoomAPI] Account call_logs page {$page}: " . count($logs) . ' records (total_records=' . ($data['total_records'] ?? '?') . ')');

            } catch (\Exception $e) {
                Log::error('[ZoomAPI] Account call_logs exception page ' . $page . ': ' . $e->getMessage());
                return $page === 1 ? null : $allLogs;
            }

        } while ($nextPageToken && $page < 50);

        return $allLogs;
    }

    /**
     * Fetch call logs for a single user token via /phone/users/me/call_logs.
     */
    private function fetchUserCallLogs(string $token, Carbon $from, Carbon $to): ?array
    {
        $allLogs = [];
        $nextPageToken = null;
        $page = 0;

        do {
            $page++;
            try {
                $query = [
                    'from'      => $from->format('Y-m-d'),
                    'to'        => $to->format('Y-m-d'),
                    'page_size' => 300,
                    'type'      => 'all',
                ];

                if ($nextPageToken) {
                    $query['next_page_token'] = $nextPageToken;
                }

                $response = Http::timeout(30)
                    ->withToken($token)
                    ->get('https://api.zoom.us/v2/phone/users/me/call_logs', $query);

                if (! $response->successful()) {
                    Log::warning('[ZoomAPI] User call logs request failed', [
                        'status' => $response->status(),
                        'body'   => substr($response->body(), 0, 200),
                        'page'   => $page,
                    ]);
                    return $page === 1 ? null : $allLogs; // Return what we have if pagination fails
                }

                $data = $response->json();
                $logs = $data['call_logs'] ?? [];
                $allLogs = array_merge($allLogs, $logs);
                $nextPageToken = $data['next_page_token'] ?? null;

                Log::info("[ZoomAPI] /me page {$page}: " . count($logs) . ' call logs');

            } catch (\Exception $e) {
                Log::error('[ZoomAPI] User call logs exception page ' . $page . ': ' . $e->getMessage());
                return $page === 1 ? null : $allLogs;
            }
        } while ($nextPageToken && $page < 20);

        return $allLogs;
    }

    /**
     * Upsert a single call log entry into zoom_webhook_logs.
     * If zoom_call_id exists, enriches missing fields. Otherwise creates new record.
     */
    private function upsertCallLog(array $log, array &$result): void
    {
        $callId = $log['id'];

        // Check if this call already exists (from webhook or previous sync)
        $existing = ZoomWebhookLog::where('zoom_call_id', $callId)->first();

        if ($existing) {
            // Enrich fields that might be missing from webhook data
            $updates = [];

            if (! $existing->duration_seconds && isset($log['duration'])) {
                $updates['duration_seconds'] = $log['duration'];
            }
            if (! $existing->call_result && isset($log['result'])) {
                $updates['call_result'] = $log['result'];
            }
            if (! $existing->call_type && isset($log['direction'])) {
                $updates['call_type'] = strtolower($log['direction']);
            }
            if (! $existing->caller_number && isset($log['caller_number'])) {
                $updates['caller_number'] = $log['caller_number'];
            }
            if (! $existing->callee_number && isset($log['callee_number'])) {
                $updates['callee_number'] = $log['callee_number'];
            }
            if (! $existing->recording_id && isset($log['recording_id'])) {
                $updates['recording_id'] = $log['recording_id'];
            }
            // Upgrade 'pending_api_fetch' placeholder if we now have a real URL from the payload
            if ($existing->recording_url === 'pending_api_fetch' && ! isset($log['has_recording'])) {
                $updates['recording_url'] = ($log['has_recording'] ?? false) ? 'pending_api_fetch' : null;
            }

            if ($updates) {
                $existing->update($updates);
            }

            $result['skipped']++;
            return;
        }

        // Determine call type from direction
        $callType = isset($log['direction']) ? strtolower($log['direction']) : 'unknown';

        // Try to match lead by phone number
        $leadId = null;
        $agentId = null;

        $phoneToMatch = $log['callee_number'] ?? $log['caller_number'] ?? null;
        if ($phoneToMatch) {
            $cleanPhone = preg_replace('/[^\d]/', '', $phoneToMatch);
            $last10 = substr($cleanPhone, -10);
            $lead = \App\Models\Lead::where('phone_number', 'like', '%' . $last10 . '%')->first();
            if ($lead) {
                $leadId = $lead->id;
            }
        }

        // Try to match agent by email or owner info
        $ownerEmail = $log['owner']['email'] ?? null;
        if ($ownerEmail) {
            $agent = \App\Models\User::where('email', $ownerEmail)->first();
            if ($agent) {
                $agentId = $agent->id;
            }
        }

        // Create new entry from API data
        ZoomWebhookLog::create([
            'event_type'       => 'phone.api_call_log',
            'zoom_call_id'     => $callId,
            'call_session_id'  => $log['call_id'] ?? null,
            'caller_number'    => $log['caller_number'] ?? null,
            'caller_name'      => $log['caller_name'] ?? null,
            'caller_email'     => $ownerEmail,
            'caller_user_id'   => $log['owner']['id'] ?? null,
            'caller_extension' => $log['owner']['extension_number'] ?? null,
            'callee_number'    => $log['callee_number'] ?? null,
            'callee_name'      => $log['callee_name'] ?? null,
            'call_type'        => $callType,
            'call_status'      => $log['result'] ?? null,
            'call_result'      => $log['result'] ?? null,
            'call_start_time'  => isset($log['date_time']) ? Carbon::parse($log['date_time'])->utc() : null,
            'call_end_time'    => isset($log['date_time']) && isset($log['duration'])
                                    ? Carbon::parse($log['date_time'])->addSeconds($log['duration'])->utc()
                                    : null,
            'duration_seconds' => $log['duration'] ?? 0,
            'recording_id'     => $log['recording_id'] ?? null,
            'recording_url'    => ($log['has_recording'] ?? false) ? 'pending_api_fetch' : null,
            'lead_id'          => $leadId,
            'agent_id'         => $agentId,
            'raw_payload'      => $log,
            'is_processed'     => true,
            'processing_notes' => 'Synced from Zoom Phone API (user-level)',
            'processed_at'     => now(),
        ]);

        $result['synced']++;
    }
}
