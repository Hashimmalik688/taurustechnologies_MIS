<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\ZoomToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomController extends Controller
{
    private $baseUrl = 'https://api.zoom.us/v2';
    
    /**
     * Redirect to Zoom OAuth authorization
     */
    public function startAuthorization()
    {
        $clientId = config('zoom.oauth.client_id');
        $redirectUri = config('zoom.oauth.redirect_uri');
        
        $url = "https://zoom.us/oauth/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}";
        
        return redirect($url);
    }
    
    /**
     * Handle OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return redirect()->route('root')->with('error', 'Zoom authorization failed');
        }
        
        // Exchange code for token
        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('zoom.oauth.redirect_uri'),
            'client_id' => config('zoom.oauth.client_id'),
            'client_secret' => config('zoom.oauth.client_secret'),
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // Save token for this user
            ZoomToken::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                ]
            );
            
            return redirect()->route('root')->with('success', 'Zoom Phone connected successfully!');
        }
        
        Log::error('Zoom OAuth failed', ['response' => $response->body()]);
        return redirect()->route('root')->with('error', 'Failed to connect Zoom Phone');
    }
    
    /**
     * Make a call to a lead using desktop app (webhooks will fire if configured)
     */
    public function makeCall(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $token = $this->getValidToken();
        
        if (!$token) {
            return response()->json(['error' => 'Zoom not authorized. Please connect your Zoom account.'], 401);
        }
        
        try {
            // Get user's Zoom Phone info to verify they have access
            $phoneResponse = Http::withToken($token)->get($this->baseUrl . '/phone/users/me');
            
            if (!$phoneResponse->successful()) {
                Log::error('Failed to get Zoom phone info', ['response' => $phoneResponse->body()]);
                return response()->json(['error' => 'Zoom Phone not found or not authorized'], 500);
            }
            
            $phoneData = $phoneResponse->json();
            $zoomPhoneNumber = $phoneData['phone_numbers'][0]['number'] ?? null;
            
            // Clean phone number
            $cleanPhone = preg_replace('/[^\d+]/', '', $lead->phone_number);
            
            // Add +1 if it's a US number without country code
            if (strlen($cleanPhone) === 10) {
                $cleanPhone = '+1' . $cleanPhone;
            } elseif (!str_starts_with($cleanPhone, '+')) {
                $cleanPhone = '+' . $cleanPhone;
            }
            
            Log::info('📞 Making desktop call with webhook tracking', [
                'lead_id' => $lead->id,
                'phone_number' => $cleanPhone,
                'zoom_phone' => $zoomPhoneNumber,
                'lead_name' => $lead->cn_name
            ]);
            
            // Create a call record for webhook tracking
            // The webhook will find this record and update it when call connects
            $callRecord = \App\Models\CallLog::create([
                'lead_id' => $lead->id,
                'agent_id' => Auth::id() ?: 1,
                'phone_number' => $lead->phone_number,
                'call_type' => 'outbound',
                'call_status' => 'no_answer', // Webhook will update this
                'call_start_time' => now(),
                'duration_seconds' => 0,
                'created_by' => Auth::id() ?: 1,
                'notes' => 'Desktop call - waiting for webhook',
            ]);
            
            // Update user's zoom_number for webhook matching
            if ($zoomPhoneNumber && Auth::id()) {
                $user = User::find(Auth::id());
                if ($user && !$user->zoom_number) {
                    $user->update(['zoom_number' => $zoomPhoneNumber]);
                }
            }
            
            // Use desktop app protocol - webhooks WILL fire if configured in Zoom app
            $zoomUrl = 'zoomphonecall://' . urlencode($cleanPhone);
            
            return response()->json([
                'success' => true,
                'message' => 'Call initiated - webhooks enabled in Zoom app',
                'call_id' => $callRecord->id,
                'zoom_url' => $zoomUrl,
                'lead_name' => $lead->cn_name,
                'phone_number' => $cleanPhone,
                'webhooks_note' => 'Ensure Event Subscriptions are configured in Zoom Marketplace app',
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Call initiation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lead_id' => $leadId,
                'phone' => $lead->phone_number
            ]);
            
            return response()->json([
                'error' => 'Failed to initiate call',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get call status - PROFESSIONAL IMPLEMENTATION
     */
    public function getCallStatus($callId)
    {
        $callRecord = \App\Models\CallLog::find($callId);
        
        if (!$callRecord) {
            return response()->json(['error' => 'Call not found'], 404);
        }
        
        $token = $this->getValidToken();
        if (!$token) {
            return response()->json(['error' => 'Not authorized'], 401);
        }
        
        try {
            // If we have a Zoom call ID, check API status
            if ($callRecord->zoom_call_id) {
                $response = Http::withToken($token)
                    ->get($this->baseUrl . "/phone/calls/{$callRecord->zoom_call_id}");
                
                if ($response->successful()) {
                    $zoomCallData = $response->json();
                    
                    // Update our call record with Zoom data
                    $status = $zoomCallData['status'] ?? 'unknown';
                    $duration = $zoomCallData['duration'] ?? 0;
                    
                    $callRecord->update([
                        'call_status' => $status,
                        'duration_seconds' => $duration,
                        'call_end_time' => $status === 'completed' ? now() : null,
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'call_status' => $status,
                        'duration' => $duration,
                        'zoom_data' => $zoomCallData,
                        'show_ravens_form' => in_array($status, ['completed', 'ended'])
                    ]);
                }
            }
            
            // If no Zoom call ID or API failed, check recent call logs
            $phoneResponse = Http::withToken($token)->get($this->baseUrl . '/phone/users/me/call_logs', [
                'page_size' => 20,
                'from' => $callRecord->call_start_time->subMinutes(2)->toDateString(),
                'to' => now()->addMinutes(5)->toDateString()
            ]);
            
            if ($phoneResponse->successful()) {
                $logs = $phoneResponse->json()['call_logs'] ?? [];
                
                // Find matching call by phone number and time
                foreach ($logs as $log) {
                    $logPhone = preg_replace('/[^\d+]/', '', $log['callee'] ?? '');
                    $recordPhone = preg_replace('/[^\d+]/', '', $callRecord->phone_number);
                    
                    if ($logPhone === $recordPhone) {
                        $logTime = \Carbon\Carbon::parse($log['start_time']);
                        $timeDiff = abs($logTime->diffInMinutes($callRecord->call_start_time));
                        
                        if ($timeDiff <= 5) { // Within 5 minutes
                            $status = $log['status'] ?? 'unknown';
                            $duration = $log['duration'] ?? 0;
                            
                            $callRecord->update([
                                'call_status' => $status,
                                'duration_seconds' => $duration,
                                'call_end_time' => $status === 'completed' ? $logTime->addSeconds($duration) : null,
                                'zoom_call_id' => $log['id'] ?? null,
                            ]);
                            
                            return response()->json([
                                'success' => true,
                                'call_status' => $status,
                                'duration' => $duration,
                                'matched_log' => true,
                                'show_ravens_form' => in_array($status, ['completed', 'ended'])
                            ]);
                        }
                    }
                }
            }
            
            // No matching call found - return current status
            return response()->json([
                'success' => true,
                'call_status' => $callRecord->call_status,
                'duration' => $callRecord->duration_seconds,
                'show_ravens_form' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get call status', ['error' => $e->getMessage(), 'call_id' => $callId]);
            
            return response()->json([
                'error' => 'Failed to get call status',
                'call_status' => $callRecord->call_status,
                'show_ravens_form' => false
            ], 500);
        }
    }
    
    /**
     * Get call status by lead ID - matches frontend polling expectation
     */
    public function getCallStatusByLead($leadId)
    {
        // Find the most recent call record for this lead
        $callRecord = \App\Models\CallLog::where('lead_id', $leadId)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$callRecord) {
            \Log::info('📊 getCallStatusByLead: No call found', ['lead_id' => $leadId]);
            return response()->json(['error' => 'Call not found'], 404);
        }
        
        \Log::info('📊 getCallStatusByLead: Checking status', [
            'lead_id' => $leadId,
            'call_log_id' => $callRecord->id,
            'current_status' => $callRecord->call_status,
            'created_at' => $callRecord->created_at,
        ]);
        
        // Check if call was marked as connected by webhook
        if ($callRecord->call_status === 'connected') {
            \Log::info('✅ getCallStatusByLead: Returning CONNECTED!', [
                'lead_id' => $leadId,
                'call_log_id' => $callRecord->id,
            ]);
            return response()->json([
                'success' => true,
                'status' => 'connected',
                'show_ravens_form' => true,
                'call_id' => $callRecord->id,
                'duration' => $callRecord->duration_seconds
            ]);
        }
        
        // Try to get real-time status from Zoom API call logs
        $token = $this->getValidToken();
        if ($token && $callRecord->call_status === 'no_answer') {
            try {
                // Get recent call logs from Zoom (last 24 hours)
                $response = Http::withToken($token)->get($this->baseUrl . '/phone/users/me/call_logs', [
                    'page_size' => 50,
                    'from' => $callRecord->call_start_time->subHours(1)->toDateString(),
                    'to' => now()->addDay()->toDateString()
                ]);
                
                if ($response->successful()) {
                    $callLogs = $response->json()['call_logs'] ?? [];
                    
                    // Look for a matching call (by phone number and recent timing)
                    $callPhone = preg_replace('/[^\d+]/', '', $callRecord->phone_number);
                    
                    foreach ($callLogs as $log) {
                        // Check if this is the call we're looking for
                        // Try multiple fields where the external phone number might be
                        $logCallee = preg_replace('/[^\d+]/', '', $log['callee_did_number'] ?? $log['callee_number'] ?? $log['callee'] ?? '');
                        $logCaller = preg_replace('/[^\d+]/', '', $log['caller_did_number'] ?? $log['caller_number'] ?? $log['caller'] ?? '');
                        $logResult = $log['result'] ?? 'unknown';
                        
                        // Also check forward_to_phone and callee_number_source fields
                        $forwardToPhone = preg_replace('/[^\d+]/', '', $log['forward_to_phone'] ?? '');
                        $phoneNumber = preg_replace('/[^\d+]/', '', $log['phone_number'] ?? $log['callee_phone_number'] ?? '');
                        
                        // Match by phone number and recent timing
                        if ((str_ends_with($logCallee, substr($callPhone, -10)) || str_ends_with($logCaller, substr($callPhone, -10)))) {
                            $logTime = \Carbon\Carbon::parse($log['date_time'] ?? $log['start_time'] ?? now());
                            $timeDiff = abs($logTime->diffInMinutes($callRecord->call_start_time));
                            
                            Log::info('⏱️ Time difference check', [
                                'log_time' => $logTime,
                                'record_time' => $callRecord->call_start_time,
                                'diff_minutes' => $timeDiff,
                            ]);
                            
                            // If within 3 minutes and result shows connected
                            if ($timeDiff <= 3) {
                                // Check if call was connected
                                if (str_contains($logResult, 'connected') || $logResult === 'Call connected') {
                                    // Update database to mark as connected
                                    $callRecord->update([
                                        'call_status' => 'connected',
                                        'duration_seconds' => $log['duration'] ?? 0,
                                    ]);
                                    
                                    Log::info('✅ Real-time detection: Call connected', [
                                        'lead_id' => $leadId,
                                        'zoom_result' => $logResult,
                                        'duration' => $log['duration'] ?? 0
                                    ]);
                                    
                                    return response()->json([
                                        'success' => true,
                                        'status' => 'connected',
                                        'show_ravens_form' => true,
                                        'call_id' => $callRecord->id,
                                        'duration' => $log['duration'] ?? 0,
                                        'source' => 'zoom_api'
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check Zoom call logs', ['error' => $e->getMessage()]);
            }
        }
        
        // Check other statuses
        \Log::info('📊 getCallStatusByLead: Status NOT connected', [
            'lead_id' => $leadId,
            'call_log_id' => $callRecord->id,
            'current_status' => $callRecord->call_status,
        ]);
        
        return response()->json([
            'success' => true,
            'status' => $callRecord->call_status,
            'show_ravens_form' => in_array($callRecord->call_status, ['completed', 'connected']),
            'call_id' => $callRecord->id,
            'duration' => $callRecord->duration_seconds
        ]);
    }
    
    /**
     * Webhook receiver for call events
     */
    public function webhook(Request $request)
    {
        // Verify webhook challenge
        if ($request->has('challenge')) {
            return response()->json([
                'challenge' => $request->input('challenge')
            ]);
        }
        
        $event = $request->input('event');
        $payload = $request->input('payload');
        
        Log::info('Zoom webhook received', ['event' => $event]);
        
        // Handle different events
        switch ($event) {
            case 'phone.call_ended':
                $this->handleCallEnded($payload);
                break;
            case 'phone.call_log_completed':
                $this->handleCallLogCompleted($payload);
                break;
        }
        
        return response()->json(['status' => 'received']);
    }
    
    /**
     * Handle call ended event
     */
    private function handleCallEnded($payload)
    {
        $callId = $payload['object']['id'] ?? null;
        if (!$callId) return;
        
        $callLog = \App\Models\CallLog::where('zoom_call_id', $callId)->first();
        
        if ($callLog) {
            $callLog->update([
                'status' => 'completed',
                'duration' => $payload['object']['duration'] ?? 0,
                'ended_at' => now(),
            ]);
        }
    }
    
    /**
     * Handle call log completed (includes recording)
     */
    private function handleCallLogCompleted($payload)
    {
        $callId = $payload['object']['id'] ?? null;
        if (!$callId) return;
        
        $callLog = \App\Models\CallLog::where('zoom_call_id', $callId)->first();
        
        if ($callLog && isset($payload['object']['recording_url'])) {
            $callLog->update([
                'recording_url' => $payload['object']['recording_url'],
            ]);
        }
    }
    
    /**
     * Get valid access token (refresh if needed)
     */
    private function getValidToken($userId = null)
    {
        // If no specific user ID provided, use authenticated user or find any valid token
        if ($userId === null) {
            $userId = Auth::id();
            
            // If no authenticated user (e.g., in console commands), find any valid token
            if (!$userId) {
                $tokenRecord = ZoomToken::where('expires_at', '>', now())->first();
                if (!$tokenRecord) {
                    $tokenRecord = ZoomToken::orderBy('expires_at', 'desc')->first();
                }
            } else {
                $tokenRecord = ZoomToken::where('user_id', $userId)->first();
            }
        } else {
            $tokenRecord = ZoomToken::where('user_id', $userId)->first();
        }
        
        if (!$tokenRecord) {
            return null;
        }
        
        // Check if token expired
        if ($tokenRecord->expires_at->isPast()) {
            // Refresh token
            $response = Http::asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $tokenRecord->refresh_token,
                'client_id' => config('zoom.oauth.client_id'),
                'client_secret' => config('zoom.oauth.client_secret'),
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $tokenRecord->update([
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                ]);
            } else {
                Log::error('Token refresh failed', ['response' => $response->body()]);
                return null;
            }
        }
        
        return $tokenRecord->access_token;
    }
    
    /**
     * Test what Zoom API capabilities we have
     */
    public function testApiCapabilities()
    {
        $token = $this->getValidToken();
        
        if (!$token) {
            return response()->json(['error' => 'Not authorized with Zoom'], 401);
        }
        
        $results = [];
        
        try {
            // Test 1: Get user info
            $userResponse = Http::withToken($token)->get($this->baseUrl . '/users/me');
            $results['user_info'] = [
                'status' => $userResponse->status(),
                'success' => $userResponse->successful(),
                'data' => $userResponse->successful() ? $userResponse->json() : $userResponse->body()
            ];
            
            // Test 2: Check phone features
            if ($userResponse->successful()) {
                $phoneResponse = Http::withToken($token)->get($this->baseUrl . '/phone/users/me');
                $results['phone_info'] = [
                    'status' => $phoneResponse->status(),
                    'success' => $phoneResponse->successful(),
                    'data' => $phoneResponse->successful() ? $phoneResponse->json() : $phoneResponse->body()
                ];
            }
            
            // Test 3: Get call history (last 10 calls)
            $historyResponse = Http::withToken($token)->get($this->baseUrl . '/phone/users/me/call_logs', [
                'page_size' => 10,
                'from' => now()->subDays(7)->toDateString(),
                'to' => now()->toDateString()
            ]);
            $results['call_history'] = [
                'status' => $historyResponse->status(),
                'success' => $historyResponse->successful(),
                'data' => $historyResponse->successful() ? $historyResponse->json() : $historyResponse->body()
            ];
            
            return response()->json([
                'success' => true,
                'capabilities' => $results,
                'summary' => [
                    'can_get_user_info' => $results['user_info']['success'],
                    'has_phone_access' => isset($results['phone_info']) && $results['phone_info']['success'],
                    'can_get_call_logs' => $results['call_history']['success']
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API test failed',
                'message' => $e->getMessage(),
                'results' => $results
            ], 500);
        }
    }
    
    /**
     * Test if current user's phone is authorized
     */
    public function testPhoneAuth()
    {
        $token = $this->getValidToken();
        
        if (!$token) {
            return response()->json(['error' => 'Not authorized with Zoom'], 401);
        }
        
        try {
            // Get phone user details
            $response = Http::withToken($token)->get($this->baseUrl . '/phone/users/me');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'phone_authorized' => true,
                    'phone_number' => $data['phone_number'] ?? 'Not found',
                    'calling_plans' => $data['calling_plans'] ?? [],
                    'phone_user_id' => $data['id'] ?? null,
                    'extension_number' => $data['extension_number'] ?? null,
                    'full_data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'phone_authorized' => false,
                    'error' => 'Phone not authorized or not found',
                    'response' => $response->body()
                ], $response->status());
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Phone authorization test failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
