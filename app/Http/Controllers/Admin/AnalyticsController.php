<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    /**
     * Create a new controller instance.
     *
     * @param AnalyticsService $analyticsService
     */
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the live analytics dashboard
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function live(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Apply filter presets
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        $metrics = $this->analyticsService->getLiveMetrics($startDate, $endDate);
        $topPerformers = $this->analyticsService->getTopPerformers(5, $startDate, $endDate);
        
        // Get validator form metrics
        $validatorFormMetrics = $this->analyticsService->getValidatorFormMetrics($startDate, $endDate);
        $validatorBreakdown = $this->analyticsService->getValidatorBreakdown($startDate, $endDate);
        
        // Get verifier form metrics
        $verifierBreakdown = $this->analyticsService->getVerifierBreakdown($startDate, $endDate);
        
        // Get Peregrine Closer breakdown
        $peregrineCloserBreakdown = $this->analyticsService->getPeregrineCloserBreakdown($startDate, $endDate);
        
        // Get QA breakdown
        $qaBreakdown = $this->analyticsService->getQABreakdown($startDate, $endDate);
        
        return view('analytics.live', compact('metrics', 'topPerformers', 'filter', 'startDate', 'endDate', 'validatorFormMetrics', 'validatorBreakdown', 'verifierBreakdown', 'peregrineCloserBreakdown', 'qaBreakdown'));
    }

    /**
     * Get live metrics data for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveData(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Apply filter presets
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        $metrics = $this->analyticsService->getLiveMetrics($startDate, $endDate);
        $topPerformers = $this->analyticsService->getTopPerformers(5, $startDate, $endDate);
        $validatorFormMetrics = $this->analyticsService->getValidatorFormMetrics($startDate, $endDate);
        
        return response()->json([
            'metrics' => $metrics,
            'topPerformers' => $topPerformers,
            'validatorFormMetrics' => $validatorFormMetrics,
            'timestamp' => now()->toDateTimeString(),
            'filter' => $filter,
        ]);
    }

    /**
     * Get historical trend data for charts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoricalData(Request $request)
    {
        $days = $request->get('days', 30);
        $trends = $this->analyticsService->getHistoricalTrends($days);
        
        return response()->json($trends);
    }

    /**
     * Get drill-down detailed data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDrillDown(Request $request)
    {
        $type = $request->get('type');
        $filter = $request->get('filter', 'today');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');
        $validatorId = $request->get('validator_id');

        // Apply filter presets
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        // Handle validator-specific drill-down
        if ($type === 'validator_breakdown' && $validatorId) {
            $data = $this->analyticsService->getValidatorDetailedBreakdown($validatorId, $startDate, $endDate);
            return response()->json([
                'type' => $type,
                'data' => $data,
                'count' => $data->count(),
            ]);
        }

        $data = $this->analyticsService->getDrillDownData($type, $startDate, $endDate);
        
        return response()->json([
            'type' => $type,
            'data' => $data,
            'count' => $data->count(),
        ]);
    }

    /**
     * Helper method to get date range based on filter
     * Office hours: 7pm PKT to 5am PKT = 7am MT to 5pm MT
     * So "Jan 1" business day = Jan 1 7am MT to Jan 1 5pm MT
     *
     * @param string $filter
     * @param string|null $customStart
     * @param string|null $customEnd
     * @return array
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        // Use Mountain timezone - office shift is 7pm PKT to 5am PKT (7am MT to 5pm MT)
        $timezone = 'America/Denver';
        
        switch ($filter) {
            case 'today':
                // Today's business day: 7am MT to current time (or end of day if past midnight)
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $now = Carbon::now($timezone);
                
                // Use current time as end, not hardcoded 5pm
                // This ensures all sales made today are included
                $end = $now->setTimezone('UTC');
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        // Parse dates with Mountain timezone - use business hours (7am to 5pm)
                        $start = Carbon::parse($customStart, $timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::parse($customEnd, $timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    } catch (\Exception $e) {
                        \Log::warning('Invalid custom date range provided', [
                            'start' => $customStart,
                            'end' => $customEnd,
                            'error' => $e->getMessage()
                        ]);
                        // Fallback to today if dates are invalid
                        $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    }
                }
                // If custom selected but no dates provided, fallback to today
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            default:
                // Default to today's business hours
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
        }
    }
}
