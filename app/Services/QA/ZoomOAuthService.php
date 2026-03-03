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
     *
     * @param string $downloadUrl The Zoom recording download URL
     * @param string $localPath The local file path to save to
     * @throws \RuntimeException
     */
    public function downloadRecording(string $downloadUrl, string $localPath): void
    {
        $token = $this->getAccessToken();

        Log::info('[QA:ZoomOAuth] Downloading recording', ['url' => $downloadUrl]);

        // Ensure directory exists
        $dir = dirname($localPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $response = Http::timeout(300)
            ->withToken($token)
            ->withOptions(['sink' => $localPath])
            ->get($downloadUrl);

        if (!$response->successful()) {
            // Clean up partial file
            if (file_exists($localPath)) {
                @unlink($localPath);
            }
            throw new \RuntimeException('Failed to download recording: HTTP ' . $response->status());
        }

        $fileSize = filesize($localPath);
        Log::info('[QA:ZoomOAuth] Recording downloaded', [
            'path' => $localPath,
            'size_bytes' => $fileSize,
            'size_mb' => round($fileSize / 1048576, 2),
        ]);

        if ($fileSize < 1000) {
            @unlink($localPath);
            throw new \RuntimeException('Downloaded file too small (' . $fileSize . ' bytes) — likely an error response');
        }
    }
}
