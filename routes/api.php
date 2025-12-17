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

// Poll for active call events (local polling system)
Route::get('/call-events/poll', function (Request $request) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $userId = auth()->id();

    // Get unread connected call events for this user
    $callEvent = \App\Models\CallEvent::where('user_id', $userId)
        ->where('status', 'connected')
        ->where('is_read', false)
        ->orderBy('event_time', 'desc')
        ->first();

    if ($callEvent) {
        return response()->json([
            'has_call' => true,
            'lead_id' => $callEvent->lead_id,
            'status' => $callEvent->status,
            'lead_data' => $callEvent->lead_data,
            'caller_number' => $callEvent->caller_number,
            'callee_number' => $callEvent->callee_number,
            'event_id' => $callEvent->id,
        ]);
    }

    return response()->json(['has_call' => false]);
});

// Mark call event as read
Route::post('/call-events/{id}/mark-read', function (Request $request, $id) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $callEvent = \App\Models\CallEvent::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if ($callEvent) {
        $callEvent->is_read = true;
        $callEvent->save();

        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Call event not found'], 404);
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