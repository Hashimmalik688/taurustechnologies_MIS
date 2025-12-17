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
        // Get ALL webhook data
        $allWebhookData = $request->all();

        // Extract event type and data
        $event = $request->input('event');
        $payload = $request->input('payload.object', []);

        // Extract phone numbers based on the actual webhook structure
        $callerNumber = null;
        $calleeNumber = null;

        // For call history events, extract from call_logs
        if (isset($payload['call_logs']) && is_array($payload['call_logs']) && count($payload['call_logs']) > 0) {
            $callLog = $payload['call_logs'][0]; // Get the first call log
            $callerNumber = $callLog['caller_did_number'] ?? null;
            $calleeNumber = $callLog['callee_did_number'] ?? null;
        }

        // Fallback: For real-time events, extract from caller/callee objects
        if (! $callerNumber && ! $calleeNumber) {
            $callerNumber = $payload['caller']['phone_number'] ?? null;
            $calleeNumber = $payload['callee']['phone_number'] ?? null;
        }

        // Process different call events
        switch ($event) {
            case 'phone.caller_connected':
            case 'phone.callin.started':
            case 'phone.callout.started':
                $this->handleCallConnected($callerNumber, $calleeNumber, $allWebhookData);
                break;

            case 'phone.call_ended':
            case 'phone.call_disconnected':
            case 'phone.caller_ended':
            case 'phone.callin.ended':
            case 'phone.callout.ended':
                $this->handleCallEnded($callerNumber, $allWebhookData);
                break;

            case 'phone.caller_call_history_completed':
                // This is a call history event - process it
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
        if (! $calleeNumber) {
            Log::warning('No callee number found for call connected event');
            $this->broadcastWebhookData('call_connected_no_number', $rawWebhookData);

            return;
        }

        $lead = Lead::with('carriers')->where('phone_number', 'like', '%'.substr($calleeNumber, -10).'%')->first();

        Log::info('Call connected - Lead search', [
            'lead' => $lead,
            'phone_number' => $calleeNumber,
            'sanitized_phone' => $this->sanitizePhoneForChannel($calleeNumber),
            'search_pattern' => '%'.substr($calleeNumber, -10).'%',
        ]);

        if ($lead) {
            // Find the user by their zoom number (caller number)
            $user = User::where('zoom_number', 'like', '%'.substr($callerNumber, -10).'%')->first();

            if ($user) {
                // Store call event in database for local polling
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

                Log::info('Call event stored in database', [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'caller' => $callerNumber,
                    'callee' => $calleeNumber,
                ]);
            } else {
                Log::warning('No user found for caller number', ['caller' => $callerNumber]);
            }
        } else {
            Log::warning('No lead found for call', ['callee' => $calleeNumber]);
        }
    }

    private function handleCallEnded($phoneNumber, $rawWebhookData)
    {
        Log::info('Call ended', [
            'phoneNumber' => $phoneNumber,
            'sanitized_phone' => $this->sanitizePhoneForChannel($phoneNumber),
        ]);

        // Find the user by phone number
        $user = User::where('zoom_number', 'like', '%'.substr($phoneNumber, -10).'%')->first();

        if ($user) {
            // Mark any connected call events as ended
            CallEvent::where('user_id', $user->id)
                ->where('status', 'connected')
                ->where('is_read', false)
                ->update(['status' => 'ended']);

            Log::info('Call events marked as ended', ['user_id' => $user->id]);
        }
    }

    private function handleCallHistoryCompleted($callerNumber, $calleeNumber, $rawWebhookData, $payload)
    {
        Log::info('Call history completed', [
            'callerNumber' => $callerNumber,
            'calleeNumber' => $calleeNumber,
            'call_logs_count' => count($payload['call_logs'] ?? []),
        ]);

        // Extract additional call information
        if (isset($payload['call_logs'][0])) {
            $callLog = $payload['call_logs'][0];

            $callInfo = [
                'direction' => $callLog['direction'] ?? 'unknown',
                'result' => $callLog['result'] ?? 'unknown',
                'talk_time' => $callLog['talk_time'] ?? 0,
                'start_time' => $callLog['start_time'] ?? null,
                'end_time' => $callLog['end_time'] ?? null,
                'caller_name' => $callLog['caller_name'] ?? 'Unknown',
            ];

            Log::info('Call details', $callInfo);

            // If it's an outbound call and was connected, treat it as a completed call
            if ($callLog['direction'] === 'outbound' && $callLog['result'] === 'connected') {
                $this->handleCallConnected($callerNumber, $calleeNumber, $rawWebhookData);
            }
        }

        // Always broadcast the call history event for debugging
        $this->broadcastWebhookData('call_history_completed', $rawWebhookData, $callerNumber);
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
