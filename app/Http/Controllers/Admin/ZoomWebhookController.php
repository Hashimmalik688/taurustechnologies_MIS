<?php

namespace App\Http\Controllers\Admin;

use App\Events\CallStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\CallEvent;
use App\Models\User;
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
                        'call_status' => 'ended',
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
