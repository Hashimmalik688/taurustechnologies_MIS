<?php

namespace App\Http\Controllers\Admin;

use App\Events\CallStatusChanged;
use App\Http\Controllers\Controller;
use App\Jobs\QA\DownloadAndProcessRecording;
use App\Models\Lead;
use App\Models\CallEvent;
use App\Models\CallLog;
use App\Models\QA\QaCall;
use App\Models\User;
use App\Models\ZoomWebhookLog;
use App\Traits\SanitizesPhoneNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    use SanitizesPhoneNumbers;

    public function handleWebhook(Request $request)
    {
        // Log ALL incoming webhook data for debugging
        Log::info('🔔 ZOOM WEBHOOK RECEIVED', [
            'event' => $request->input('event'),
            'full_payload' => $request->all()
        ]);
        
        // 1. ZOOM URL VERIFICATION CHALLENGE
        // Zoom sends this when you first configure the webhook URL
        if ($request->input('event') === 'endpoint.url_validation') {
            $plainToken = $request->input('payload.plainToken');
            $secret = env('ZOOM_WEBHOOK_SECRET_TOKEN', config('zoom.webhook_secret_token', ''));
            
            if (!$secret) {
                Log::warning('⚠️ ZOOM WEBHOOK: No secret token configured for URL validation');
                // If no secret, just echo back the plain token (some Zoom apps allow this)
                return response()->json([
                    'plainToken' => $plainToken,
                    'encryptedToken' => hash('sha256', $plainToken)
                ], 200);
            }
            
            $encryptedToken = hash_hmac('sha256', $plainToken, $secret);
            
            Log::info('✅ ZOOM WEBHOOK: URL Verification Challenge', [
                'plainToken' => $plainToken,
                'encryptedToken' => $encryptedToken
            ]);
            
            return response()->json([
                'plainToken' => $plainToken,
                'encryptedToken' => $encryptedToken
            ], 200);
        }
        
        // Get ALL webhook data
        $allWebhookData = $request->all();

        // Extract event type and data
        $event = $request->input('event');
        $payload = $request->input('payload.object', []);

        // Extract phone numbers - try multiple locations where Zoom might put them
        $callerNumber = null;
        $calleeNumber = null;

        // Try call_logs array first (for history/log completed events)
        if (isset($payload['call_logs']) && is_array($payload['call_logs']) && count($payload['call_logs']) > 0) {
            $callLog = $payload['call_logs'][0];
            $callerNumber = $callLog['caller_did_number'] ?? $callLog['caller_number'] ?? null;
            $calleeNumber = $callLog['callee_did_number'] ?? $callLog['callee_number'] ?? null;
            
            Log::info('📋 Extracted from call_logs array', [
                'caller' => $callerNumber,
                'callee' => $calleeNumber,
                'result' => $callLog['result'] ?? 'unknown'
            ]);
        }

        // Try caller/callee objects (for real-time events)
        if (!$calleeNumber) {
            $calleeNumber = $payload['callee']['phone_number'] ?? 
                           $payload['callee_number'] ?? 
                           $payload['callee_did_number'] ??
                           $request->input('payload.object.callee.phone_number') ??
                           $request->input('payload.object.callee_number') ?? null;
        }
        
        if (!$callerNumber) {
            $callerNumber = $payload['caller']['phone_number'] ?? 
                           $payload['caller_number'] ?? 
                           $payload['caller_did_number'] ??
                           $request->input('payload.object.caller.phone_number') ??
                           $request->input('payload.object.caller_number') ?? null;
        }

        Log::info('📞 WEBHOOK: Extracted phone numbers', [
            'event' => $event,
            'callerNumber' => $callerNumber,
            'calleeNumber' => $calleeNumber
        ]);

        // === SAVE TO ZOOM WEBHOOK LOGS TABLE (ALL EVENTS) ===
        $this->saveWebhookLog($event, $payload, $callerNumber, $calleeNumber, $allWebhookData);

        // Process different call events
        switch ($event) {
            case 'phone.caller_connected':
            case 'phone.callee_answered':
            case 'phone.callin.started':
            case 'phone.callout.started':
                $this->handleCallConnected($callerNumber, $calleeNumber, $allWebhookData);
                break;

            case 'phone.call_ended':
            case 'phone.call_disconnected':
            case 'phone.caller_ended':
            case 'phone.callin.ended':
            case 'phone.callout.ended':
                $this->handleCallEnded($calleeNumber ?? $callerNumber, $allWebhookData);
                break;

            case 'phone.caller_call_log_completed':
            case 'phone.callee_call_log_completed':
            case 'phone.caller_call_history_completed':
            case 'phone.callee_call_history_completed':
            case 'phone.call_log_completed':
                // These fire when a call completes - check if it was connected
                $this->handleCallHistoryCompleted($callerNumber, $calleeNumber, $allWebhookData, $payload);
                break;

            case 'phone.recording_completed':
                $this->handleRecordingCompleted($allWebhookData, $payload);
                break;

            default:
                Log::info('Unhandled event type', ['event' => $event, 'data' => $allWebhookData]);
                // Still broadcast unknown events for debugging
                $this->broadcastWebhookData($event, $allWebhookData, $callerNumber);
        }

        // Always return 200 OK for webhooks
        return response()->json(['status' => 'success']);
    }

    private function handleCallConnected($callerNumber, $calleeNumber, $rawWebhookData)
    {
        Log::info('🔔 HANDLE CALL CONNECTED', [
            'callerNumber' => $callerNumber,
            'calleeNumber' => $calleeNumber,
        ]);
        
        // Extract Zoom user info for fallback matching (internal calls)
        $payload = $rawWebhookData['payload']['object'] ?? [];
        $zoomUserEmail = $payload['caller']['email'] ?? $payload['callee']['email'] ?? null;
        $zoomUserId = $payload['caller']['user_id'] ?? $payload['callee']['user_id'] ?? null;
        $zoomExtNumber = $payload['caller']['extension_number'] ?? $payload['callee']['extension_number'] ?? null;
        
        Log::info('🔍 Zoom user info', [
            'email' => $zoomUserEmail,
            'user_id' => $zoomUserId,
            'extension' => $zoomExtNumber,
        ]);
        
        // Try to find lead by either caller or callee number
        $phoneToSearch = $calleeNumber ?? $callerNumber;
        $lead = null;
        
        if ($phoneToSearch) {
            // Strip '+' and any non-digit characters, get last 10 digits
            $cleanPhone = preg_replace('/[^\d]/', '', $phoneToSearch);
            $last10Digits = substr($cleanPhone, -10);
            
            Log::info('🔍 Searching for lead by phone', [
                'original_phone' => $phoneToSearch,
                'clean_phone' => $cleanPhone,
                'last_10' => $last10Digits,
                'search_pattern' => '%' . $last10Digits . '%'
            ]);

            // Search for lead by phone number (last 10 digits)
            $lead = Lead::where('phone_number', 'like', '%' . $last10Digits . '%')->first();
            
            // Also try caller number if callee didn't match
            if (!$lead && $callerNumber && $callerNumber !== $calleeNumber) {
                $cleanCaller = preg_replace('/[^\d]/', '', $callerNumber);
                $last10Caller = substr($cleanCaller, -10);
                $lead = Lead::where('phone_number', 'like', '%' . $last10Caller . '%')->first();
                Log::info('🔍 Tried caller number', ['last_10' => $last10Caller, 'found' => $lead ? 'yes' : 'no']);
            }
        }
        
        // FALLBACK: If no lead found by phone, find most recent pending CallLog from the Zoom user
        if (!$lead) {
            $user = null;
            
            // Try to find user by email first
            if ($zoomUserEmail) {
                $user = User::where('email', $zoomUserEmail)->first();
            }
            
            // If no email or user not found, try by caller's zoom_number
            if (!$user && $callerNumber) {
                $cleanCaller = preg_replace('/[^\d]/', '', $callerNumber);
                $last10Caller = substr($cleanCaller, -10);
                $user = User::where('zoom_number', 'like', '%' . $last10Caller . '%')->first();
                Log::info('🔍 FALLBACK: Searching user by zoom_number', [
                    'caller' => $callerNumber,
                    'last_10' => $last10Caller,
                    'found' => $user ? 'yes' : 'no'
                ]);
            }
            
            if ($user) {
                // Find most recent pending call log from this user (within last 5 minutes)
                $recentCallLog = \App\Models\CallLog::where('agent_id', $user->id)
                    ->where('call_status', 'no_answer')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($recentCallLog) {
                    $lead = Lead::find($recentCallLog->lead_id);
                    Log::info('🔍 FALLBACK: Found lead via Zoom user\'s recent call', [
                        'user_email' => $zoomUserEmail,
                        'user_id' => $user->id,
                        'call_log_id' => $recentCallLog->id,
                        'lead_id' => $lead?->id,
                    ]);
                }
            }
        }
        
        if (!$lead && !$phoneToSearch) {
            Log::warning('❌ No phone number or user match for call connected event', [
                'raw_data' => $rawWebhookData
            ]);
            return;
        }

        if ($lead) {
            Log::info('✅ Lead found!', [
                'lead_id' => $lead->id,
                'lead_name' => $lead->cn_name,
                'lead_phone' => $lead->phone_number
            ]);
            
            // Find the most recent call log for this lead (within last 10 minutes)
            $callLog = \App\Models\CallLog::where('lead_id', $lead->id)
                ->where('call_status', '!=', 'connected')
                ->where('created_at', '>=', now()->subMinutes(10))
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($callLog) {
                // UPDATE THE CALL LOG STATUS TO CONNECTED
                $callLog->update([
                    'call_status' => 'connected',
                    'call_start_time' => now(),
                ]);
                
                Log::info('✅✅ CALL LOG UPDATED TO CONNECTED ✅✅', [
                    'call_log_id' => $callLog->id,
                    'lead_id' => $lead->id,
                    'lead_name' => $lead->cn_name,
                    'phone_number' => $lead->phone_number,
                    'new_status' => 'connected',
                ]);
            } else {
                // No existing call log - create one
                $callLog = \App\Models\CallLog::create([
                    'lead_id' => $lead->id,
                    'agent_id' => auth()->id() ?? 1,
                    'phone_number' => $lead->phone_number,
                    'call_type' => 'outbound',
                    'call_status' => 'connected',
                    'call_start_time' => now(),
                ]);
                
                Log::info('✅ Created new CallLog with connected status', [
                    'call_log_id' => $callLog->id,
                    'lead_id' => $lead->id
                ]);
            }
            
            // Also store in CallEvent for legacy support
            $user = null;
            if ($callerNumber) {
                $cleanCaller = preg_replace('/[^\d]/', '', $callerNumber);
                $user = User::where('zoom_number', 'like', '%' . substr($cleanCaller, -10) . '%')->first();
            }
            
            if (!$user && $callLog) {
                $user = User::find($callLog->agent_id);
            }
            
            if ($user) {
                CallEvent::create([
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'caller_number' => $callerNumber,
                    'callee_number' => $calleeNumber,
                    'status' => 'connected',
                    'lead_data' => $lead->toArray(),
                    'webhook_data' => $rawWebhookData,
                    'is_read' => false,
                    'event_time' => now(),
                ]);
            }
        } else {
            Log::warning('❌ No lead found for phone number', [
                'callee' => $calleeNumber,
                'caller' => $callerNumber,
                'searched_last_10' => $phoneToSearch ? substr(preg_replace('/[^\d]/', '', $phoneToSearch), -10) : 'N/A'
            ]);
        }
    }

    private function handleCallEnded($phoneNumber, $rawWebhookData)
    {
        Log::info('📞 Call ended webhook received', [
            'phoneNumber' => $phoneNumber,
            'sanitized_phone' => $this->sanitizePhoneForChannel($phoneNumber),
        ]);

        // Find the lead by phone number
        $lead = Lead::where('phone_number', 'like', '%'.substr($phoneNumber, -10).'%')->first();
        
        if ($lead) {
            // Find and update active call logs for this lead
            // IMPORTANT: Only mark as ended if call is at least 5 seconds old to avoid marking brand new calls
            $callLog = \App\Models\CallLog::where('lead_id', $lead->id)
                ->whereIn('call_status', ['connected', 'no_answer', 'ringing'])
                ->where('created_at', '>=', now()->subHours(1))
                ->where('created_at', '<=', now()->subSeconds(5)) // Must be at least 5 seconds old
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($callLog) {
                $callLog->update([
                    'call_status' => 'completed',
                    'call_end_time' => now(),
                    'duration_seconds' => now()->diffInSeconds($callLog->call_start_time),
                ]);
                
                Log::info('✅ WEBHOOK: CallLog updated to ENDED', [
                    'call_log_id' => $callLog->id,
                    'lead_id' => $lead->id,
                    'duration' => $callLog->duration_seconds,
                ]);
            } else {
                Log::warning('⚠️ No active CallLog found to mark as ended (or too recent)', ['lead_id' => $lead->id]);
            }
        } else {
            // Try to find by caller's zoom number
            $user = User::where('zoom_number', 'like', '%'.substr($phoneNumber, -10).'%')->first();
            if ($user) {
                $callLog = \App\Models\CallLog::where('agent_id', $user->id)
                    ->whereIn('call_status', ['connected', 'no_answer', 'ringing'])
                    ->where('created_at', '>=', now()->subHours(1))
                    ->where('created_at', '<=', now()->subSeconds(5)) // Must be at least 5 seconds old
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($callLog) {
                    $callLog->update([
                        'call_status' => 'completed',
                        'call_end_time' => now(),
                        'duration_seconds' => now()->diffInSeconds($callLog->call_start_time),
                    ]);
                    
                    Log::info('✅ WEBHOOK: CallLog updated to ENDED (matched by agent)', [
                        'call_log_id' => $callLog->id,
                        'agent_id' => $user->id,
                    ]);
                }
            }
        }

        // Also mark any connected call events as ended
        if (isset($user)) {
            CallEvent::where('user_id', $user->id)
                ->where('status', 'connected')
                ->where('is_read', false)
                ->update(['status' => 'ended']);

            Log::info('Call events marked as ended', ['user_id' => $user->id]);
        }
    }

    private function handleCallHistoryCompleted($callerNumber, $calleeNumber, $rawWebhookData, $payload)
    {
        Log::info('📞 CALL LOG/HISTORY COMPLETED EVENT', [
            'callerNumber' => $callerNumber,
            'calleeNumber' => $calleeNumber,
            'call_logs_count' => count($payload['call_logs'] ?? []),
        ]);

        // Extract call information from the call_logs array
        if (isset($payload['call_logs'][0])) {
            $zoomCallLog = $payload['call_logs'][0];

            $result = $zoomCallLog['result'] ?? 'unknown';
            $duration = $zoomCallLog['duration'] ?? 0;
            $calleeDidNumber = $zoomCallLog['callee_did_number'] ?? $calleeNumber;
            $callerDidNumber = $zoomCallLog['caller_did_number'] ?? $callerNumber;

            Log::info('📋 CALL RESULT', [
                'result' => $result,
                'duration' => $duration,
                'callee_did' => $calleeDidNumber,
                'caller_did' => $callerDidNumber,
            ]);

            // If the call was connected, update our database
            if ($result === 'Call connected' || str_contains(strtolower($result), 'connected')) {
                Log::info('✅ CALL WAS CONNECTED - Updating CallLog');
                
                // Use the callee number (external phone) to find the lead
                $phoneToSearch = $calleeDidNumber ?? $callerDidNumber;
                $cleanPhone = preg_replace('/[^\d]/', '', $phoneToSearch);
                $last10Digits = substr($cleanPhone, -10);
                
                $lead = Lead::where('phone_number', 'like', '%' . $last10Digits . '%')->first();
                
                if ($lead) {
                    // Find the most recent call log for this lead
                    $callLog = \App\Models\CallLog::where('lead_id', $lead->id)
                        ->where('created_at', '>=', now()->subMinutes(30))
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($callLog && $callLog->call_status !== 'connected') {
                        $callLog->update([
                            'call_status' => 'connected',
                            'duration_seconds' => $duration,
                        ]);
                        
                        Log::info('✅✅ CALL LOG UPDATED TO CONNECTED ✅✅', [
                            'call_log_id' => $callLog->id,
                            'lead_id' => $lead->id,
                            'lead_name' => $lead->cn_name,
                            'duration' => $duration,
                        ]);
                    }
                } else {
                    Log::warning('❌ No lead found for connected call', [
                        'phone_searched' => $last10Digits
                    ]);
                }
            } else {
                // Call was NOT connected (missed, no answer, etc.) - mark as completed
                Log::info('❌ Call was not connected - marking as completed', ['result' => $result]);
                
                // Try to find the CallLog by either number
                $phoneToSearch = $calleeDidNumber ?? $callerDidNumber ?? $callerNumber ?? $calleeNumber;
                if ($phoneToSearch) {
                    $cleanPhone = preg_replace('/[^\d]/', '', $phoneToSearch);
                    $last10Digits = substr($cleanPhone, -10);
                    
                    $lead = Lead::where('phone_number', 'like', '%' . $last10Digits . '%')->first();
                    
                    if ($lead) {
                        $callLog = \App\Models\CallLog::where('lead_id', $lead->id)
                            ->whereIn('call_status', ['no_answer', 'ringing', 'connected'])
                            ->where('created_at', '>=', now()->subMinutes(30))
                            ->orderBy('created_at', 'desc')
                            ->first();
                            
                        if ($callLog) {
                            // Map Zoom result to our status
                            $finalStatus = 'completed';
                            if (str_contains(strtolower($result), 'no answer')) {
                                $finalStatus = 'no_answer';
                            } elseif (str_contains(strtolower($result), 'voicemail')) {
                                $finalStatus = 'voicemail';
                            } elseif (str_contains(strtolower($result), 'missed')) {
                                $finalStatus = 'missed';
                            } elseif (str_contains(strtolower($result), 'rejected') || str_contains(strtolower($result), 'declined')) {
                                $finalStatus = 'rejected';
                            } elseif (str_contains(strtolower($result), 'busy')) {
                                $finalStatus = 'busy';
                            }
                            
                            $callLog->update([
                                'call_status' => $finalStatus,
                                'call_end_time' => now(),
                                'duration_seconds' => $duration,
                            ]);
                            
                            Log::info('✅ CallLog marked as ' . $finalStatus, [
                                'call_log_id' => $callLog->id,
                                'lead_id' => $lead->id,
                                'result' => $result,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle phone.recording_completed webhook — triggers QA pipeline.
     */
    private function handleRecordingCompleted(array $rawWebhookData, array $payload): void
    {
        Log::info('[QA:Webhook] phone.recording_completed received', [
            'payload_keys' => array_keys($payload),
        ]);

        // Extract recording data from payload
        // Zoom Phone v2 sends recordings in a "recordings" array
        if (isset($payload['recordings']) && is_array($payload['recordings']) && !empty($payload['recordings'])) {
            $recording = $payload['recordings'][0];
            $zoomCallId = $recording['call_id'] ?? $recording['id'] ?? null;
            $zoomUserId = $recording['owner']['id'] ?? $recording['caller']['user_id'] ?? null;
            $zoomCallLogId = $recording['call_log_id'] ?? $recording['id'] ?? null;
            $duration = intval($recording['duration'] ?? 0);
            $downloadUrl = $recording['download_url'] ?? null;
            $callerNumber = $recording['caller_number'] ?? null;
            $calleeNumber = $recording['callee_number'] ?? null;
            $startTime = $recording['date_time'] ?? $recording['start_time'] ?? null;
            $zoomTranscriptUrl = $recording['transcript_download_url'] ?? null;
        } else {
            // Fallback to old structure
            $zoomCallId = $payload['call_id'] ?? $payload['id'] ?? null;
            $zoomUserId = $payload['caller']['user_id'] ?? $payload['owner']['id'] ?? null;
            $zoomCallLogId = $payload['call_log_id'] ?? $payload['id'] ?? null;
            $duration = intval($payload['duration'] ?? $payload['recording_duration'] ?? 0);
            $downloadUrl = $payload['download_url'] ?? $payload['recording_url'] ?? null;
            $callerNumber = $payload['caller']['phone_number'] ?? $payload['caller_number'] ?? null;
            $calleeNumber = $payload['callee']['phone_number'] ?? $payload['callee_number'] ?? null;
            $startTime = $payload['date_time'] ?? $payload['start_time'] ?? $payload['call_start_time'] ?? null;
            $zoomTranscriptUrl = $payload['transcript_download_url'] ?? $payload['recording_transcript_url'] ?? null;
        }
        
        // Check if Zoom provided transcription (if enabled in Zoom settings)
        if ($zoomTranscriptUrl) {
            Log::info('[QA:Webhook] Zoom transcription available', ['transcript_url' => $zoomTranscriptUrl]);
        }

        // Also check for recording files array (Zoom sometimes nests recordings further)
        if (!$downloadUrl && isset($payload['recording_files']) && is_array($payload['recording_files'])) {
            foreach ($payload['recording_files'] as $file) {
                if (($file['file_type'] ?? '') === 'MP3' || ($file['recording_type'] ?? '') === 'audio_only') {
                    $downloadUrl = $file['download_url'] ?? null;
                    $duration = $duration ?: intval($file['recording_end'] ?? 0);
                    break;
                }
            }
            // Fall back to first file if no MP3 found
            if (!$downloadUrl && !empty($payload['recording_files'])) {
                $downloadUrl = $payload['recording_files'][0]['download_url'] ?? null;
            }
        }

        if (!$zoomCallId || !$downloadUrl) {
            Log::warning('[QA:Webhook] Missing call_id or download_url', [
                'zoom_call_id' => $zoomCallId,
                'download_url' => $downloadUrl,
                'payload' => $payload,
            ]);
            return;
        }

        // Skip short calls (< 8 minutes) — not meaningful sales conversations
        if ($duration > 0 && $duration < 480) {
            Log::info('[QA:Webhook] Skipping short call (< 8 min)', [
                'zoom_call_id' => $zoomCallId,
                'duration' => $duration,
            ]);
            return;
        }

        // Duplicate protection — skip if already processed
        if (QaCall::where('zoom_call_id', $zoomCallId)->exists()) {
            Log::info('[QA:Webhook] Duplicate — already processed', [
                'zoom_call_id' => $zoomCallId,
            ]);
            return;
        }

        // Try to match agent (Ravens Closer) by Zoom user ID, extension, name, or email
        $agentUser = null;
        $agentName = $payload['caller']['name'] ?? $payload['owner']['name'] ?? null;
        $agentEmail = $payload['caller']['email'] ?? $payload['owner']['email'] ?? null;
        $zoomExtension = $payload['caller']['extension_number']
            ?? $payload['owner']['extension_number']
            ?? (isset($payload['recordings'][0]) ? ($payload['recordings'][0]['owner']['extension_number'] ?? null) : null);

        // Priority 1: Match by Zoom user ID (most reliable)
        if ($zoomUserId) {
            $agentUser = User::where('zoom_user_id', $zoomUserId)->first();
        }

        // Priority 2: Match by Zoom extension number
        if (!$agentUser && $zoomExtension) {
            $agentUser = User::where('zoom_extension', (string) $zoomExtension)->first();
        }

        // Priority 3: Match by name (fuzzy — handles "37524 Abdullah Ayub" → "Abdullah Ayub")
        if (!$agentUser && $agentName) {
            $cleanName = preg_replace('/^\d+\s*/', '', $agentName); // Strip leading digits
            $agentUser = User::where('name', 'like', '%' . $cleanName . '%')->first();
        }

        // Priority 4: Match by email
        if (!$agentUser && $agentEmail) {
            $agentUser = User::where('email', $agentEmail)->first();
        }

        // Priority 5: Match by phone/zoom_number
        if (!$agentUser && $callerNumber) {
            $cleanNumber = preg_replace('/[^\d]/', '', $callerNumber);
            $last10 = substr($cleanNumber, -10);
            if (strlen($last10) === 10) {
                $agentUser = User::where('zoom_number', 'like', '%' . $last10 . '%')->first();
            }
        }

        Log::info('[QA:Webhook] Agent matching result', [
            'zoom_user_id' => $zoomUserId,
            'zoom_extension' => $zoomExtension,
            'agent_name_from_zoom' => $agentName,
            'matched_user' => $agentUser ? $agentUser->name . ' (ID ' . $agentUser->id . ')' : 'NONE',
        ]);

        // Try to match existing CallLog
        $callLogId = null;
        if ($zoomCallId) {
            $callLog = CallLog::where('zoom_call_id', $zoomCallId)->first();
            $callLogId = $callLog?->id;

            // If no agent yet, try from CallLog
            if (!$agentUser && $callLog) {
                $agentUser = User::find($callLog->agent_id);
            }
        }

        // Create qa_call record
        $qaCall = QaCall::create([
            'zoom_call_id' => $zoomCallId,
            'call_log_id' => $callLogId,
            'agent_user_id' => $agentUser?->id,
            'agent_name' => $agentUser?->name ?? $agentName ?? 'Unknown Agent',
            'agent_email' => $agentUser?->email ?? $agentEmail,
            'zoom_user_id' => $zoomUserId,
            'zoom_call_log_id' => $zoomCallLogId ?? null,
            'caller_number' => $callerNumber,
            'callee_number' => $calleeNumber,
            'duration_seconds' => $duration,
            'call_start_time' => $startTime ? \Carbon\Carbon::parse($startTime) : now(),
            'recording_url' => $downloadUrl,
            'processing_status' => 'pending',
        ]);

        Log::info('[QA:Webhook] QaCall created, dispatching job', [
            'qa_call_id' => $qaCall->id,
            'zoom_call_id' => $zoomCallId,
            'agent' => $agentUser?->name ?? 'unknown',
            'duration' => $duration,
        ]);

        // Dispatch processing job to qa-processing queue
        DownloadAndProcessRecording::dispatch($qaCall->id);
    }

    /**
     * Save all webhook events to zoom_webhook_logs table for standalone reporting.
     * This captures EVERY call from Zoom Phone, not just MIS-tracked calls.
     */
    private function saveWebhookLog($event, $payload, $callerNumber, $calleeNumber, $rawWebhookData)
    {
        try {
            // Extract call logs array (used in history_completed events)
            $callLogData = null;
            if (isset($payload['call_logs']) && is_array($payload['call_logs']) && count($payload['call_logs']) > 0) {
                $callLogData = $payload['call_logs'][0];
            }

            // Extract recording data if present
            $recordingData = null;
            if (isset($payload['recordings']) && is_array($payload['recordings']) && !empty($payload['recordings'])) {
                $recordingData = $payload['recordings'][0];
            } elseif (isset($payload['recording_files']) && is_array($payload['recording_files']) && !empty($payload['recording_files'])) {
                $recordingData = $payload['recording_files'][0];
            }

            // Determine call ID (try multiple locations where Zoom might put it)
            $zoomCallId = $callLogData['id'] ?? 
                         $payload['id'] ?? 
                         $payload['call_id'] ?? 
                         $recordingData['call_id'] ?? 
                         null;

            $callSessionId = $payload['call_session_id'] ?? 
                            $callLogData['call_session_id'] ?? 
                            null;

            // Extract caller information
            $callerInfo = $payload['caller'] ?? [];
            $callerData = [
                'caller_number' => $callerNumber ?? $callerInfo['phone_number'] ?? $callLogData['caller_number'] ?? null,
                'caller_did_number' => $callerInfo['did_number'] ?? $callLogData['caller_did_number'] ?? null,
                'caller_name' => $callerInfo['name'] ?? $callLogData['caller_name'] ?? null,
                'caller_email' => $callerInfo['email'] ?? null,
                'caller_user_id' => $callerInfo['user_id'] ?? $callerInfo['id'] ?? null,
                'caller_extension' => $callerInfo['extension_number'] ?? $callerInfo['extension'] ?? null,
            ];

            // Extract callee information
            $calleeInfo = $payload['callee'] ?? [];
            // For voicemail_received, Zoom puts callee fields at the top level of the payload object
            // (not nested under 'callee'), so we fall back to those directly.
            $calleeData = [
                'callee_number' => $calleeNumber ?? $calleeInfo['phone_number'] ?? $callLogData['callee_number'] ?? $payload['callee_number'] ?? null,
                'callee_did_number' => $calleeInfo['did_number'] ?? $callLogData['callee_did_number'] ?? null,
                'callee_name' => $calleeInfo['name'] ?? $callLogData['callee_name'] ?? $payload['callee_name'] ?? null,
                'callee_email' => $calleeInfo['email'] ?? null,
                'callee_user_id' => $calleeInfo['user_id'] ?? $calleeInfo['id'] ?? $payload['callee_user_id'] ?? null,
                'callee_extension' => $calleeInfo['extension_number'] ?? $calleeInfo['extension'] ?? $payload['callee_number'] ?? null,
            ];

            // Determine call type: event name is the most reliable indicator.
            // call_logs[].direction can be wrong (e.g. voicemail shows 'outbound').
            $callType = null;
            
            // Event name takes priority — it's always correct
            if (str_contains($event, 'callee') || str_contains($event, 'voicemail')) {
                $callType = 'inbound'; // callee events = our user received the call
            } elseif (str_contains($event, 'caller') || str_contains($event, 'callout')) {
                $callType = 'outbound'; // caller events = our user made the call
            }
            
            // Fallback to call_logs direction only for ambiguous events
            if (!$callType && $callLogData && isset($callLogData['direction'])) {
                $callType = strtolower($callLogData['direction']);
            }
            
            // Final fallback
            if (!$callType) {
                $callType = 'outbound';
            }

            // Extract call status/result
            $callStatus = $callLogData['status'] ?? $payload['status'] ?? null;
            $callResult = $callLogData['result'] ?? $payload['result'] ?? null;

            // Extract timestamps — order: call_logs date_time → payload fields → ringing_start → call_end → event_ts (ms epoch)
            $callStartTime = $callLogData['date_time'] ?? 
                            $callLogData['start_time'] ?? 
                            $payload['date_time'] ?? 
                            $payload['start_time'] ?? 
                            $payload['call_start_time'] ?? 
                            null;

            $callEndTime = $callLogData['end_time'] ?? 
                          $payload['end_time'] ?? 
                          $payload['call_end_time'] ?? 
                          null;

            $answerTime = $callLogData['answer_time'] ?? $payload['answer_time'] ?? null;
            $ringingStartTime = $callLogData['ringing_start_time'] ?? $payload['ringing_start_time'] ?? null;

            // Fallback chain for call_start_time: use ringing_start or call_end if available
            if (!$callStartTime && $ringingStartTime) {
                $callStartTime = $ringingStartTime;
            }
            if (!$callStartTime && $callEndTime) {
                $callStartTime = $callEndTime;
            }

            // Last resort: Zoom's event_ts is MILLISECOND epoch — must use createFromTimestampMs
            if (!$callStartTime && isset($rawWebhookData['event_ts'])) {
                $eventTs = $rawWebhookData['event_ts'];
                try {
                    $callStartTime = \Carbon\Carbon::createFromTimestampMs($eventTs)->utc();
                } catch (\Exception $e) {
                    Log::warning('Failed to parse event_ts', ['event_ts' => $eventTs, 'error' => $e->getMessage()]);
                    $callStartTime = now()->utc();
                }
            }
            
            // Final fallback: use current UTC time
            if (!$callStartTime) {
                $callStartTime = now()->utc();
            }

            // Extract duration
            $duration = $callLogData['duration'] ?? 
                       $payload['duration'] ?? 
                       $recordingData['duration'] ?? 
                       0;

            // Extract recording information
            $recordingUrl = $recordingData['download_url'] ?? 
                           $payload['download_url'] ?? 
                           $payload['recording_url'] ?? 
                           null;

            $recordingId = $recordingData['id'] ?? $payload['recording_id'] ?? null;
            $recordingFilePath = $recordingData['file_path'] ?? null;
            $recordingFileSize = $recordingData['file_size'] ?? null;
            $recordingType = $recordingData['recording_type'] ?? $payload['recording_type'] ?? null;
            $recordingStartTime = $recordingData['start_time'] ?? $recordingData['date_time'] ?? null;
            $recordingEndTime = $recordingData['end_time'] ?? null;

            // Extract transcript information
            $transcriptText = $recordingData['transcript'] ?? 
                             $payload['transcript'] ?? 
                             $payload['transcript_text'] ?? 
                             null;
            $transcriptUrl = $recordingData['transcript_download_url'] ?? 
                            $payload['transcript_download_url'] ?? 
                            null;
            $transcriptFilePath = $recordingData['transcript_file_path'] ?? null;

            // Extract cost/billing (if available)
            $callCost = $callLogData['cost'] ?? $payload['cost'] ?? null;
            $callRate = $callLogData['rate'] ?? $payload['rate'] ?? null;

            // Try to match with MIS data
            $leadId = null;
            $agentId = null;
            $matchedCallLogId = null;

            // Try to find lead by phone number
            $phoneToMatch = $calleeData['callee_number'] ?? $callerData['caller_number'];
            if ($phoneToMatch) {
                $cleanPhone = preg_replace('/[^\d]/', '', $phoneToMatch);
                $last10Digits = substr($cleanPhone, -10);
                $lead = Lead::where('phone_number', 'like', '%' . $last10Digits . '%')->first();
                if ($lead) {
                    $leadId = $lead->id;
                }
            }

            // Try to find agent by email or zoom number
            $agentEmail = $callerData['caller_email'] ?? $calleeData['callee_email'];
            if ($agentEmail) {
                $agent = User::where('email', $agentEmail)->first();
                if ($agent) {
                    $agentId = $agent->id;
                }
            }

            if (!$agentId && $callerData['caller_number']) {
                $cleanCaller = preg_replace('/[^\d]/', '', $callerData['caller_number']);
                $last10Caller = substr($cleanCaller, -10);
                $agent = User::where('zoom_number', 'like', '%' . $last10Caller . '%')->first();
                if ($agent) {
                    $agentId = $agent->id;
                }
            }

            // Try to match with existing CallLog
            if ($zoomCallId) {
                $matchedCallLog = CallLog::where('zoom_call_id', $zoomCallId)->first();
                if ($matchedCallLog) {
                    $matchedCallLogId = $matchedCallLog->id;
                    if (!$leadId) $leadId = $matchedCallLog->lead_id;
                    if (!$agentId) $agentId = $matchedCallLog->agent_id;
                }
            }

            // Create the webhook log record
            $webhookLog = ZoomWebhookLog::create([
                'event_type' => $event,
                'zoom_call_id' => $zoomCallId,
                'call_session_id' => $callSessionId,
                
                // Caller data
                'caller_number' => $callerData['caller_number'],
                'caller_did_number' => $callerData['caller_did_number'],
                'caller_name' => $callerData['caller_name'],
                'caller_email' => $callerData['caller_email'],
                'caller_user_id' => $callerData['caller_user_id'],
                'caller_extension' => $callerData['caller_extension'],
                
                // Callee data
                'callee_number' => $calleeData['callee_number'],
                'callee_did_number' => $calleeData['callee_did_number'],
                'callee_name' => $calleeData['callee_name'],
                'callee_email' => $calleeData['callee_email'],
                'callee_user_id' => $calleeData['callee_user_id'],
                'callee_extension' => $calleeData['callee_extension'],
                
                // Call details
                'call_type' => $callType,
                'call_status' => $callStatus,
                'call_result' => $callResult,
                // Zoom sends timestamps in UTC (Z suffix). Store as UTC consistently.
                'call_start_time' => $callStartTime ? \Carbon\Carbon::parse($callStartTime)->utc() : null,
                'call_end_time' => $callEndTime ? \Carbon\Carbon::parse($callEndTime)->utc() : null,
                'duration_seconds' => intval($duration),
                'answer_time' => $answerTime ? \Carbon\Carbon::parse($answerTime)->utc() : null,
                'ringing_start_time' => $ringingStartTime ? \Carbon\Carbon::parse($ringingStartTime)->utc() : null,
                
                // Recording
                'recording_url' => $recordingUrl,
                'recording_id' => $recordingId,
                'recording_file_path' => $recordingFilePath,
                'recording_file_size' => $recordingFileSize,
                'recording_type' => $recordingType,
                'recording_start_time' => $recordingStartTime ? \Carbon\Carbon::parse($recordingStartTime)->utc() : null,
                'recording_end_time' => $recordingEndTime ? \Carbon\Carbon::parse($recordingEndTime)->utc() : null,
                
                // Transcript
                'transcript_text' => $transcriptText,
                'transcript_url' => $transcriptUrl,
                'transcript_file_path' => $transcriptFilePath,
                
                // Cost
                'call_cost' => $callCost,
                'call_rate' => $callRate,
                
                // MIS integration
                'lead_id' => $leadId,
                'agent_id' => $agentId,
                'matched_call_log_id' => $matchedCallLogId,
                
                // Raw payload
                'raw_payload' => $rawWebhookData,
                
                // Processing
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            Log::info('✅ Webhook event saved to zoom_webhook_logs', [
                'log_id' => $webhookLog->id,
                'event' => $event,
                'zoom_call_id' => $zoomCallId,
                'linked_to_lead' => $leadId ? 'yes' : 'no',
                'linked_to_agent' => $agentId ? 'yes' : 'no',
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to save webhook log', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw - we don't want to fail the webhook processing
        }
    }

    private function broadcastWebhookData($eventType, $rawWebhookData, $phoneNumber = null)
    {
        // If no phone number provided, try to extract from webhook data
        if (! $phoneNumber) {
            $payload = $rawWebhookData['payload']['object'] ?? [];

            // Try call_logs first
            if (isset($payload['call_logs'][0])) {
                $phoneNumber = $payload['call_logs'][0]['caller_did_number'] ??
                              $payload['call_logs'][0]['callee_did_number'] ?? null;
            }

            // Fallback to caller/callee objects
            if (! $phoneNumber) {
                $phoneNumber = $payload['caller']['phone_number'] ??
                              $payload['callee']['phone_number'] ??
                              'unknown';
            }
        }

        Log::info('Broadcasting webhook data', [
            'eventType' => $eventType,
            'phoneNumber' => $phoneNumber,
            'sanitizedPhone' => $this->sanitizePhoneForChannel($phoneNumber),
        ]);

        broadcast(new CallStatusChanged(
            null, // no lead ID
            $eventType,
            $phoneNumber,
            null, // no lead data
            $rawWebhookData
        ))->toOthers();
    }
}
