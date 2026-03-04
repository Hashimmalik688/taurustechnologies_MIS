<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\ZoomToken;
use App\Models\ZoomWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomPhoneEmbedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Zoom Phone Embed page — full dialer + lead search + recent calls.
     */
    public function index()
    {
        $recentCalls = ZoomWebhookLog::whereIn('event_type', [
                'phone.caller_call_log_completed',
                'phone.callee_call_log_completed',
                'phone.call_log_completed',
            ])
            ->orderByDesc('call_start_time')
            ->limit(20)
            ->get();

        $hasToken = $this->hasValidToken();

        return view('admin.zoom.phone-embed', compact('recentCalls', 'hasToken'));
    }

    /**
     * Generate a Zoom access token for the Smart Embed SDK.
     * Tries OAuth user token first, falls back to S2S.
     */
    public function generateToken(Request $request)
    {
        // 1) Try OAuth user token (already authorized via /zoom/authorize)
        $token = $this->getUserToken();
        if ($token) {
            return response()->json([
                'access_token' => $token,
                'token_type'   => 'user_oauth',
            ]);
        }

        // 2) Fallback: Server-to-Server OAuth (account-level)
        $s2sToken = $this->getS2SToken();
        if ($s2sToken) {
            return response()->json([
                'access_token' => $s2sToken,
                'token_type'   => 's2s',
            ]);
        }

        return response()->json([
            'error' => 'No valid Zoom token available. Please authorize via Settings → Zoom Phone.',
        ], 401);
    }

    /**
     * Search leads by name / phone for click-to-dial.
     */
    public function searchLeads(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $leads = Lead::where(function ($query) use ($q) {
                $query->where('cn_name', 'like', "%{$q}%")
                      ->orWhere('phone_number', 'like', "%{$q}%")
                      ->orWhere('secondary_phone_number', 'like', "%{$q}%");
            })
            ->select('id', 'cn_name', 'phone_number', 'secondary_phone_number', 'state', 'carrier_name')
            ->limit(10)
            ->get();

        return response()->json($leads);
    }

    // ─── Token Helpers ──────────────────────────────────────────────────

    /**
     * Check if a valid token is available.
     */
    private function hasValidToken(): bool
    {
        return $this->getUserToken() !== null || $this->getS2SToken() !== null;
    }

    /**
     * Get the current user's OAuth access token (refresh if expired).
     */
    private function getUserToken(): ?string
    {
        $record = ZoomToken::where('user_id', Auth::id())->first();
        if (! $record) {
            // Try any valid token in the system
            $record = ZoomToken::active()->first();
        }
        if (! $record) {
            return null;
        }

        if ($record->isExpired()) {
            return $this->refreshUserToken($record);
        }

        return $record->access_token;
    }

    /**
     * Refresh an expired OAuth token.
     */
    private function refreshUserToken(ZoomToken $record): ?string
    {
        try {
            $response = Http::timeout(15)->asForm()->post('https://zoom.us/oauth/token', [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $record->refresh_token,
                'client_id'     => config('zoom.oauth.client_id'),
                'client_secret' => config('zoom.oauth.client_secret'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $record->update([
                    'access_token'  => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at'    => now()->addSeconds($data['expires_in']),
                ]);
                return $data['access_token'];
            }

            Log::warning('Zoom OAuth refresh failed', ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoom OAuth refresh exception', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get a Server-to-Server account credentials token.
     */
    private function getS2SToken(): ?string
    {
        $accountId    = config('zoom.s2s.account_id');
        $clientId     = config('zoom.s2s.client_id');
        $clientSecret = config('zoom.s2s.client_secret');

        if (! $accountId || ! $clientId || ! $clientSecret) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::warning('Zoom S2S token failed', ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoom S2S token exception', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
