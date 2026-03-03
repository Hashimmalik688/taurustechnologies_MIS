<?php

namespace App\Services\QA;

use App\Models\ZoomToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomOAuthService
{
    /**
     * Get a valid OAuth access token from the zoom_tokens table.
     * Uses the stored user OAuth tokens (from /zoom/authorize flow).
     * Automatically refreshes if expired.
     */
    public function getAccessToken(): string
    {
        // Get the most recent valid token (any user who authorized the Zoom app)
        $tokenRecord = ZoomToken::whereNotNull('refresh_token')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$tokenRecord) {
            throw new \RuntimeException(
                'No Zoom OAuth token found. An admin must authorize the Zoom app first at /zoom/authorize'
            );
        }

        // Check if token is expired — refresh if needed
        if ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast()) {
            Log::info('[QA:ZoomOAuth] Token expired, refreshing...', [
                'user_id' => $tokenRecord->user_id,
                'expired_at' => $tokenRecord->expires_at,
            ]);

            $response = Http::timeout(15)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $tokenRecord->refresh_token,
                    'client_id' => config('zoom.oauth.client_id'),
                    'client_secret' => config('zoom.oauth.client_secret'),
                ]);

            if (!$response->successful()) {
                Log::error('[QA:ZoomOAuth] Token refresh failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException(
                    'Zoom OAuth token refresh failed. Admin may need to re-authorize at /zoom/authorize. Error: ' . $response->body()
                );
            }

            $data = $response->json();
            $tokenRecord->update([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);

            Log::info('[QA:ZoomOAuth] Token refreshed successfully', [
                'user_id' => $tokenRecord->user_id,
                'new_expires_at' => $tokenRecord->expires_at,
            ]);
        }

        return $tokenRecord->access_token;
    }

    /**
     * Download a recording file from Zoom to local storage.
     * Tries the direct download_url first (Bearer token), then falls back
     * to fetching the signed file_url via the call_logs recordings API.
     *
     * @param string $downloadUrl The Zoom recording download URL
     * @param string $localPath The local file path to save to
     * @param string|null $callLogId Optional call_log_id for file_url fallback
     * @param string|null $zoomUserId Optional Zoom user ID for user-level fallback
     * @throws \RuntimeException
     */
    public function downloadRecording(string $downloadUrl, string $localPath, ?string $callLogId = null, ?string $zoomUserId = null): void
    {
        $token = $this->getAccessToken();

        Log::info('[QA:ZoomOAuth] Downloading recording', [
            'url' => $downloadUrl,
            'call_log_id' => $callLogId,
        ]);

        // Ensure directory exists
        $dir = dirname($localPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // ── Method 1: Direct download_url with Bearer token ─────────────
        $response = Http::timeout(300)
            ->withToken($token)
            ->withOptions(['sink' => $localPath])
            ->get($downloadUrl);

        if ($response->successful() && file_exists($localPath) && filesize($localPath) > 1000) {
            $this->logDownloadSuccess($localPath, 'download_url');
            return;
        }

        // Clean up failed attempt
        if (file_exists($localPath)) {
            @unlink($localPath);
        }

        Log::warning('[QA:ZoomOAuth] Direct download_url failed (HTTP ' . $response->status() . '), trying file_url fallback', [
            'download_url' => $downloadUrl,
            'call_log_id' => $callLogId,
        ]);

        // ── Method 2: Fetch file_url via call_logs recordings API ───────
        // The call_logs/{callLogId}/recordings endpoint returns a signed
        // file_url that works without the phone:read:call_recording scope.
        if ($callLogId) {
            $fileUrl = $this->getFileUrlFromCallLog($callLogId, $token);

            if ($fileUrl) {
                $response = Http::timeout(300)
                    ->withOptions(['sink' => $localPath])
                    ->get($fileUrl);

                if ($response->successful() && file_exists($localPath) && filesize($localPath) > 1000) {
                    $this->logDownloadSuccess($localPath, 'file_url (call_log_id)');
                    return;
                }

                if (file_exists($localPath)) {
                    @unlink($localPath);
                }

                Log::warning('[QA:ZoomOAuth] file_url via call_log_id also failed', [
                    'status' => $response->status(),
                ]);
            }
        }

        // ── Method 3: Search user's recordings for matching call ────────
        if ($zoomUserId) {
            $fileUrl = $this->getFileUrlFromUserRecordings($zoomUserId, $downloadUrl, $token);

            if ($fileUrl) {
                $response = Http::timeout(300)
                    ->withOptions(['sink' => $localPath])
                    ->get($fileUrl);

                if ($response->successful() && file_exists($localPath) && filesize($localPath) > 1000) {
                    $this->logDownloadSuccess($localPath, 'file_url (user recordings)');
                    return;
                }

                if (file_exists($localPath)) {
                    @unlink($localPath);
                }
            }
        }

        throw new \RuntimeException(
            'Failed to download recording: HTTP ' . $response->status()
            . '. All download methods exhausted (download_url, file_url fallbacks).'
        );
    }

    /**
     * Get a signed file_url from the call_logs recordings API.
     */
    private function getFileUrlFromCallLog(string $callLogId, string $token): ?string
    {
        try {
            // The call_log_id may or may not have dashes — try both formats
            $ids = [$callLogId];
            $noDashes = str_replace('-', '', $callLogId);
            $withDashes = strlen($noDashes) === 32
                ? substr($noDashes, 0, 8) . '-' . substr($noDashes, 8, 4) . '-' . substr($noDashes, 12, 4)
                  . '-' . substr($noDashes, 16, 4) . '-' . substr($noDashes, 20)
                : null;

            if ($withDashes && $withDashes !== $callLogId) {
                $ids[] = $withDashes;
            }
            if ($noDashes !== $callLogId) {
                $ids[] = $noDashes;
            }

            foreach ($ids as $id) {
                $response = Http::timeout(15)
                    ->withToken($token)
                    ->get("https://api.zoom.us/v2/phone/call_logs/{$id}/recordings");

                if ($response->successful()) {
                    $data = $response->json();
                    $fileUrl = $data['file_url'] ?? null;

                    if ($fileUrl) {
                        Log::info('[QA:ZoomOAuth] Got file_url from call_log recordings API', [
                            'call_log_id' => $id,
                        ]);
                        return $fileUrl;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[QA:ZoomOAuth] Failed to fetch file_url from call_logs API', [
                'call_log_id' => $callLogId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Search a user's recordings to find a matching file_url by download_url.
     */
    private function getFileUrlFromUserRecordings(string $zoomUserId, string $downloadUrl, string $token): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get("https://api.zoom.us/v2/phone/users/{$zoomUserId}/recordings", [
                    'from' => now()->subDays(7)->format('Y-m-d'),
                    'to' => now()->format('Y-m-d'),
                    'page_size' => 100,
                ]);

            if ($response->successful()) {
                $recordings = $response->json()['recordings'] ?? [];

                foreach ($recordings as $rec) {
                    if (($rec['download_url'] ?? '') === $downloadUrl) {
                        // Found a match — fetch full recording detail using call_log_id
                        $recCallLogId = $rec['call_log_id'] ?? null;
                        if ($recCallLogId) {
                            return $this->getFileUrlFromCallLog($recCallLogId, $token);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[QA:ZoomOAuth] Failed to search user recordings for file_url', [
                'zoom_user_id' => $zoomUserId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Log a successful download.
     */
    private function logDownloadSuccess(string $localPath, string $method): void
    {
        $fileSize = filesize($localPath);
        Log::info('[QA:ZoomOAuth] Recording downloaded', [
            'path' => $localPath,
            'method' => $method,
            'size_bytes' => $fileSize,
            'size_mb' => round($fileSize / 1048576, 2),
        ]);
    }
}
