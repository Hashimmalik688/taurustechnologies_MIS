<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ZoomPhoneLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    protected $zoomPhone;

    public function __construct(ZoomPhoneLogService $zoomPhone)
    {
        $this->zoomPhone = $zoomPhone;
    }

    /**
     * Display call logs directly from Zoom API
     */
    public function index(Request $request)
    {
        try {
            // Build filters from request
            $filters = [
                'user_id' => $request->get('user_id'),
                'start_date' => $request->get('start_date', Carbon::now()->subDays(7)->toDateString()),
                'end_date' => $request->get('end_date', Carbon::now()->toDateString()),
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 50),
                'call_type' => $request->get('call_type'),
                'path' => $request->get('direction'), // inbound/outbound
            ];

            // Get call logs from Zoom API
            $response = $this->zoomPhone->getFilteredCallLogs($filters);
            $callLogs = $response['call_logs'] ?? [];

            // Manual pagination since we're not using database
            $currentPage = $filters['page'];
            $perPage = $filters['per_page'];
            $total = $response['total_records'] ?? count($callLogs);

            // Create pagination info
            $pagination = [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => (($currentPage - 1) * $perPage) + 1,
                'to' => min($currentPage * $perPage, $total),
            ];

            $users = User::all();

            return view('admin.call-logs.index', compact('callLogs', 'users', 'filters', 'pagination'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to fetch call logs: '.$e->getMessage());
        }
    }

    /**
     * Show detailed view of a call log
     */
    public function show($callLogId)
    {
        try {
            $callLog = $this->zoomPhone->getCallLogDetail($callLogId);

            // Try to get recordings
            try {
                $recordings = $this->zoomPhone->getCallRecordings($callLogId);
            } catch (\Exception $e) {
                $recordings = null;
            }

            return view('admin.call-logs.show', compact('callLog', 'recordings'));

        } catch (\Exception $e) {
            return redirect()->route('call-logs.index')
                ->with('error', 'Failed to fetch call log details: '.$e->getMessage());
        }
    }

    /**
     * Search call logs by phone number
     */
    public function search(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|min:3',
        ]);

        try {
            $phoneNumber = $request->get('phone_number');
            $options = [
                'from' => $request->get('start_date', Carbon::now()->subDays(30)->toDateString()),
                'to' => $request->get('end_date', Carbon::now()->toDateString()),
            ];

            $response = $this->zoomPhone->searchByPhoneNumber($phoneNumber, $options);
            $callLogs = $response['call_logs'] ?? [];

            return response()->json([
                'success' => true,
                'call_logs' => $callLogs,
                'total' => count($callLogs),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get call statistics
     */
    public function statistics(Request $request)
    {
        try {
            $userId = $request->get('user_id');
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->toDateString());

            $stats = $this->zoomPhone->getCallStatistics($userId, $startDate, $endDate);

            if ($request->expectsJson()) {
                return response()->json($stats);
            }

            return view('admin.call-logs-direct.statistics', compact('stats'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to fetch statistics: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to fetch statistics: '.$e->getMessage());
        }
    }

    /**
     * Export call logs to CSV
     */
    public function export(Request $request)
    {
        try {
            $filters = [
                'user_id' => $request->get('user_id'),
                'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->toDateString()),
                'end_date' => $request->get('end_date', Carbon::now()->toDateString()),
                'call_type' => $request->get('call_type'),
                'direction' => $request->get('direction'),
            ];

            $csvData = $this->zoomPhone->exportToCsv($filters);

            $filename = 'call-logs-'.$filters['start_date'].'-to-'.$filters['end_date'].'.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            $callback = function () use ($csvData) {
                $file = fopen('php://output', 'w');

                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    /**
     * Refresh/clear cache
     */
    public function refreshCache(Request $request)
    {
        try {
            $userId = $request->get('user_id');
            $this->zoomPhone->clearCache($userId);

            return redirect()->back()->with('success', 'Cache cleared successfully. Fresh data will be loaded.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: '.$e->getMessage());
        }
    }

    /**
     * Load more call logs (for AJAX pagination)
     */
    public function loadMore(Request $request)
    {
        try {
            $filters = [
                'user_id' => $request->get('user_id'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 20),
            ];

            $response = $this->zoomPhone->getFilteredCallLogs($filters);

            return response()->json([
                'success' => true,
                'call_logs' => $response['call_logs'] ?? [],
                'has_more' => count($response['call_logs'] ?? []) === $filters['per_page'],
                'next_page' => $filters['page'] + 1,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load more: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's recent calls for dashboard widget
     */
    public function recentCalls(Request $request, $userId)
    {
        try {
            $options = [
                'page_size' => $request->get('limit', 10),
                'from' => Carbon::now()->subDays(7)->toDateString(),
                'to' => Carbon::now()->toDateString(),
            ];

            $response = $this->zoomPhone->getUserCallLogs($userId, $options);
            $callLogs = $response['call_logs'] ?? [];

            if ($request->expectsJson()) {
                return response()->json($callLogs);
            }

            return view('admin.call-logs-direct.widget', compact('callLogs'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to fetch recent calls: '.$e->getMessage());
        }
    }

    public function testConnection()
    {
        $zoomPhone = new ZoomPhoneLogService;
        $result = $zoomPhone->testConnection();

        if ($result['success']) {
            return response()->json(['message' => 'Zoom API connection successful!']);
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }
}
