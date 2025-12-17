<?php

namespace App\Services;

use App\Models\ZoomToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected string $baseUrl;
    protected string $authType;

    public function __construct()
    {
        $this->baseUrl = config('zoom.base_url');
        $this->authType = config('zoom.auth_type');
    }

    /**
     * Get access token for API requests.
     */
    public function getAccessToken(): string
    {
        if ($this->authType === 'server_to_server') {
            return $this->getS2SToken();
        }

        return $this->getOAuthToken();
    }

    /**
     * Generate Server-to-Server JWT token.
     */
    protected function getS2SToken(): string
    {
        $config = config('zoom.s2s');
        
        $payload = [
            'iss' => $config['client_id'],
            'exp' => now()->addMinutes(5)->timestamp,
        ];

        // TODO: Generate JWT token using $config['client_secret']
        // For now, return placeholder
        return 'placeholder_s2s_token';
    }

    /**
     * Get OAuth access token from stored refresh token.
     */
    protected function getOAuthToken(): string
    {
        $token = ZoomToken::active()->first();

        if (!$token) {
            throw new \Exception('No active Zoom token found. Please authenticate first.');
        }

        if ($token->isExpired() && $token->refresh_token) {
            $this->refreshOAuthToken($token);
        }

        return config('zoom.encrypt_tokens') 
            ? Crypt::decrypt($token->access_token)
            : $token->access_token;
    }

    /**
     * Refresh OAuth access token using refresh token.
     */
    public function refreshOAuthToken(ZoomToken $token): void
    {
        $config = config('zoom.oauth');
        $refreshToken = config('zoom.encrypt_tokens')
            ? Crypt::decrypt($token->refresh_token)
            : $token->refresh_token;

        $response = Http::post('https://zoom.us/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token->update([
                'access_token' => config('zoom.encrypt_tokens') ? Crypt::encrypt($data['access_token']) : $data['access_token'],
                'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);
        }
    }

    /**
     * Make authenticated API request to Zoom.
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $response = Http::withToken($token)
            ->timeout(config('zoom.timeout'))
            ->{strtolower($method)}($url, $data);

        if (!$response->successful()) {
            throw new \Exception("Zoom API Error: {$response->status()} - {$response->body()}");
        }

        return $response->json();
    }

    /**
     * Get user meetings.
     */
    public function getUserMeetings(string $userId): array
    {
        return $this->request('GET', "/users/{$userId}/meetings");
    }

    /**
     * Create a meeting.
     */
    public function createMeeting(string $userId, array $data): array
    {
        return $this->request('POST', "/users/{$userId}/meetings", $data);
    }

    /**
     * Store OAuth token after authentication.
     */
    public function storeToken(string $accessToken, ?string $refreshToken = null, int $expiresIn = 3600): ZoomToken
    {
        $token = ZoomToken::updateOrCreate(
            ['account_id' => config('zoom.s2s.account_id')],
            [
                'access_token' => config('zoom.encrypt_tokens') ? Crypt::encrypt($accessToken) : $accessToken,
                'refresh_token' => $refreshToken ? (config('zoom.encrypt_tokens') ? Crypt::encrypt($refreshToken) : $refreshToken) : null,
                'expires_at' => now()->addSeconds($expiresIn),
                'token_type' => 'Bearer',
                'auth_type' => 'oauth',
            ]
        );

        return $token;
    }
}
