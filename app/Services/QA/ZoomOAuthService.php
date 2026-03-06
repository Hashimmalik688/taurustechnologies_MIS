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
                $recordings = $response->json('recordings') ?? [];
                $url = $recordings[0]['download_url'] ?? $recordings[0]['file_url'] ?? null;
                if ($url) {
                    Log::info('[QA:ZoomOAuth] Got fresh signed URL from admin API', ['call_log_id' => $callLogId]);
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
}
