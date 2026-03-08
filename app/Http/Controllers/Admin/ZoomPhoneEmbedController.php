<?php

namespace App\Http\Controllers\Admin;

use App\Models\CallLog;
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
     * Zoom Phone Embed page — full dialer + per-user call history.
     */
    public function index()
    {
        $hasToken = $this->hasValidToken();
        $dids     = $this->getCompanyDids();
        return view('admin.zoom.phone-embed', compact('hasToken', 'dids'));
    }

    /**
     * Return list of company DIDs from settings (auto-seeds main line if none exist).
     */
    private function getCompanyDids(): array
    {
        $setting = \DB::table('settings')->where('key', 'zoom_dids')->first();
        if ($setting) {
            return json_decode($setting->value, true) ?? [];
        }
        $default = [
            ['number' => '+18884278933', 'label' => 'Main Line'],
            ['number' => '+12393871921', 'label' => 'Direct Number'],
        ];
        \DB::table('settings')->insert([
            'key'         => 'zoom_dids',
            'value'       => json_encode($default),
            'type'        => 'json',
            'description' => 'Company Zoom Phone DIDs — outbound caller IDs for agents',
            'group'       => 'zoom',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return $default;
    }

    /**
     * Return JSON of the current user's call logs (for the sidebar history panel).
     */
    public function myCallLogs(Request $request)
    {
        $period = $request->get('period', 'today');
        $userId = Auth::id();

        $query = CallLog::where('agent_id', $userId)
            ->with('lead:id,cn_name,phone_number')
            ->orderByDesc('call_start_time')
            ->orderByDesc('created_at');

        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $query->where('created_at', '>=', now()->startOfWeek());
        }

        $logs = $query->limit(200)->get()->map(function ($log) {
            $hasRecording = $log->recording_url && str_starts_with($log->recording_url, 'https://');
            return [
                'id'            => $log->id,
                'lead_name'     => $log->lead?->cn_name ?? 'Unknown Contact',
                'lead_id'       => $log->lead_id,
                'phone'         => $log->lead?->phone_number ?? $log->phone_number,
                'status'        => $log->call_status ?? 'completed',
                'duration'      => $log->duration_seconds,
                'time'          => ($log->call_start_time ?? $log->created_at)->diffForHumans(),
                'time_full'     => ($log->call_start_time ?? $log->created_at)->format('M j, g:i A'),
                'has_recording' => $hasRecording,
                'zoom_call_id'  => $log->zoom_call_id,
                'recording_id'  => $log->id,
            ];
        });

        return response()->json($logs);
    }

    /**
     * Fetch and redirect to the recording for a specific CallLog.
     * Checks CallLog.recording_url first, then ZoomWebhookLog by zoom_call_id.
     */
    public function getCallLogRecording($id)
    {
        $callLog = CallLog::where('id', $id)
            ->where('agent_id', Auth::id())
            ->firstOrFail();

        // Case 1: CallLog already has a valid direct URL
        if ($callLog->recording_url && str_starts_with($callLog->recording_url, 'https://')) {
            return redirect($callLog->recording_url);
        }

        // Case 2: Find via ZoomWebhookLog using the shared zoom_call_id
        if ($callLog->zoom_call_id) {
            $webhookLog = ZoomWebhookLog::where('zoom_call_id', $callLog->zoom_call_id)->first();
            if ($webhookLog) {
                if ($webhookLog->recording_url && str_starts_with($webhookLog->recording_url, 'https://')) {
                    return redirect($webhookLog->recording_url);
                }
                // Delegate to existing recording.play which calls Zoom API
                return redirect()->route('recording.play', $webhookLog->id);
            }
        }

        return back()->with('error', 'Recording not available — no Zoom call log found for this call.');
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

    /**
     * Match an array of phone numbers to CRM leads (for Smart Embed contact matching).
     * POST /zoom/phone/match-contacts
     */
    public function matchContacts(Request $request)
    {
        $numbers = $request->input('numbers', []);
        if (empty($numbers)) {
            return response()->json([]);
        }

        $contacts = [];
        foreach ($numbers as $number) {
            $clean = preg_replace('/[^\d+]/', '', $number);
            $lead = Lead::where('phone_number', 'like', "%{$clean}%")
                ->orWhere('secondary_phone_number', 'like', "%{$clean}%")
                ->select('id', 'cn_name')
                ->first();
            if ($lead) {
                $contacts[$number] = [
                    'id'   => (string) $lead->id,
                    'name' => $lead->cn_name ?? 'Unknown',
                ];
            }
        }

        return response()->json($contacts);
    }

    /**
     * Auto-log a completed Zoom call to the CRM call_logs table.
     * POST /zoom/phone/auto-log
     */
    public function autoLog(Request $request)
    {
        $data = $request->all();
        try {
            $callerNum = $data['caller']['number'] ?? null;
            $calleeNum = $data['callee']['number'] ?? null;
            $direction = $data['direction'] ?? 'outbound';
            $phoneNum  = $direction === 'inbound' ? $callerNum : $calleeNum;

            // Try to find matching lead
            $leadId = null;
            if ($phoneNum) {
                $clean = preg_replace('/[^\d+]/', '', $phoneNum);
                $lead = Lead::where('phone_number', 'like', "%{$clean}%")
                    ->orWhere('secondary_phone_number', 'like', "%{$clean}%")
                    ->first();
                $leadId = $lead?->id;
            }

            CallLog::create([
                'agent_id'        => Auth::id(),
                'lead_id'         => $leadId,
                'phone_number'    => $phoneNum,
                'direction'       => $direction,
                'status'          => strtolower($data['result'] ?? 'completed'),
                'duration'        => $data['duration'] ?? 0,
                'zoom_call_id'    => $data['callLogId'] ?? null,
                'call_start_time' => isset($data['dateTime']) ? \Carbon\Carbon::parse($data['dateTime']) : now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('zoom auto-log failed: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
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
