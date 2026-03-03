<?php

namespace App\Services\QA;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomOAuthService
{
    /**
     * Get a Server-to-Server OAuth access token from Zoom.
     * Cached for 55 minutes (tokens last 60 minutes).
     */
    public function getAccessToken(): string
    {
        return Cache::remember('zoom_s2s_token', 3300, function () {
            $accountId = config('zoom.s2s.account_id');
            $clientId = config('zoom.s2s.client_id');
            $clientSecret = config('zoom.s2s.client_secret');

            if (!$accountId || !$clientId || !$clientSecret) {
                throw new \RuntimeException('Zoom S2S credentials not configured');
            }

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if (!$response->successful()) {
                Log::error('[QA:ZoomOAuth] Token fetch failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('Failed to get Zoom S2S token: ' . $response->body());
            }

            return $response->json('access_token');
        });
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
