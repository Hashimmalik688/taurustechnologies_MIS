<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Quick add insurance carrier
Route::middleware(['auth'])->post('/carriers/quick-add', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:insurance_carriers,name',
        'base_commission_percentage' => 'nullable|numeric|min:0|max:100',
        'age_min' => 'nullable|integer|min:0',
        'age_max' => 'nullable|integer|min:0',
        'plan_types' => 'nullable|array',
        'is_active' => 'boolean'
    ]);
    
    $carrier = \App\Models\InsuranceCarrier::create([
        'name' => $validated['name'],
        'base_commission_percentage' => $validated['base_commission_percentage'] ?? 85.00,
        'age_min' => $validated['age_min'] ?? 18,
        'age_max' => $validated['age_max'] ?? 80,
        'plan_types' => $validated['plan_types'] ?? ['Term', 'Whole Life'],
        'is_active' => $validated['is_active'] ?? true,
    ]);
    
    return response()->json([
        'success' => true,
        'carrier' => $carrier,
        'message' => 'Carrier added successfully'
    ]);
});

// Get lead data for Ravens form (using web session auth)
Route::middleware(['web', 'auth'])->get('/leads/{id}', function (Request $request, $id) {
    $lead = \App\Models\Lead::with(['carriers'])->find($id);
    
    if (!$lead) {
        return response()->json(['error' => 'Lead not found'], 404);
    }
    
    return response()->json($lead);
});

// Check call connection status based on multiple factors
Route::middleware(['web', 'auth'])->post('/call-status/check', function (Request $request) {
    $leadId = $request->input('lead_id');
    $callDuration = $request->input('call_duration'); // in milliseconds
    $userInteracted = $request->input('user_interacted', false); // did user return focus?
    
    // More sophisticated detection logic
    $isConnected = false;
    $confidence = 0;
    
    // Factor 1: Duration (weak signal)
    if ($callDuration > 10000) { // 10+ seconds
        $confidence += 30;
    }
    if ($callDuration > 30000) { // 30+ seconds = very likely connected
        $confidence += 50;
    }
    
    // Factor 2: User interaction (strong signal)
    if ($userInteracted) {
        $confidence += 40;
    }
    
    // Factor 3: Check if there are any system call events
    $existingCallEvent = \App\Models\CallEvent::where('lead_id', $leadId)
        ->where('user_id', auth()->id())
        ->where('status', 'connected')
        ->where('created_at', '>', now()->subMinutes(5))
        ->exists();
    
    if ($existingCallEvent) {
        $confidence += 60; // Strong indicator
    }
    
    // Determine if connected based on confidence score
    $isConnected = $confidence >= 70;
    
    return response()->json([
        'is_connected' => $isConnected,
        'confidence' => $confidence,
        'factors' => [
            'duration_ms' => $callDuration,
            'user_interacted' => $userInteracted,
            'existing_event' => $existingCallEvent
        ]
    ]);
});

Route::get('/zoom-webhook', [App\Http\Controllers\Admin\ZoomWebhookController::class, 'handleWebhook']);
Route::post('/zoom-webhook', [App\Http\Controllers\Admin\ZoomWebhookController::class, 'handleWebhook']);

use App\Http\Controllers\Api\DashboardApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/metrics', [DashboardApiController::class, 'getMetrics']);
    Route::post('/dashboard/refresh', [DashboardApiController::class, 'refresh']);
});

// Call logging API (web-based authentication)
Route::post('/call-logs', function (Request $request) {
    // Use web auth guard for CSRF-protected requests
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $validated = $request->validate([
        'lead_id' => 'required|exists:leads,id',
        'phone_number' => 'required|string',
        'status' => 'required|string',
    ]);

    $callLog = \App\Models\CallLog::create([
        'agent_id' => auth()->id(),
        'lead_id' => $validated['lead_id'],
        'phone_number' => $validated['phone_number'],
        'call_type' => 'outbound',
        'call_status' => 'completed',
        'call_start_time' => now(),
        'duration_seconds' => 0,
    ]);

    return response()->json(['success' => true, 'call_log_id' => $callLog->id]);
});

// Note: Chat API routes moved to web.php to use web session authentication
// instead of Sanctum API authentication