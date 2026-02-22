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
        
        // Get validator form metrics
        $validatorFormMetrics = $this->analyticsService->getValidatorFormMetrics($startDate, $endDate);
        $validatorBreakdown = $this->analyticsService->getValidatorBreakdown($startDate, $endDate);
        
        // Verifier pipeline (Peregrine only)
        $verifierPipeline = $this->analyticsService->getVerifierPipelineBreakdown($startDate, $endDate);
        $verifierSubmissions = $this->analyticsService->getVerifierSubmissionLog($startDate, $endDate);
        
        // Closer breakdowns (separate)
        $peregrineCloserBreakdown = $this->analyticsService->getPeregrineCloserBreakdown($startDate, $endDate);
        $ravensCloserBreakdown = $this->analyticsService->getRavensCloserBreakdown($startDate, $endDate);
        
        // Manager breakdowns (per team)
        $peregrineManagerBreakdown = $this->analyticsService->getManagerApprovalBreakdown($startDate, $endDate, 'peregrine');
        $ravensManagerBreakdown = $this->analyticsService->getManagerApprovalBreakdown($startDate, $endDate, 'ravens');
        
        // QA breakdowns (per team)
        $peregrineQABreakdown = $this->analyticsService->getQABreakdown($startDate, $endDate, 'peregrine');
        $ravensQABreakdown = $this->analyticsService->getQABreakdown($startDate, $endDate, 'ravens');
        
        return view('analytics.live', compact(
            'metrics', 'filter', 'startDate', 'endDate',
            'validatorFormMetrics', 'validatorBreakdown',
            'verifierPipeline', 'verifierSubmissions',
            'peregrineCloserBreakdown', 'ravensCloserBreakdown',
            'peregrineManagerBreakdown', 'ravensManagerBreakdown',
            'peregrineQABreakdown', 'ravensQABreakdown'
        ));
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
        $validatorFormMetrics = $this->analyticsService->getValidatorFormMetrics($startDate, $endDate);
        $validatorBreakdown = $this->analyticsService->getValidatorBreakdown($startDate, $endDate);
        $verifierPipeline = $this->analyticsService->getVerifierPipelineBreakdown($startDate, $endDate);
        $verifierSubmissions = $this->analyticsService->getVerifierSubmissionLog($startDate, $endDate);
        $peregrineCloserBreakdown = $this->analyticsService->getPeregrineCloserBreakdown($startDate, $endDate);
        $ravensCloserBreakdown = $this->analyticsService->getRavensCloserBreakdown($startDate, $endDate);
        $peregrineManagerBreakdown = $this->analyticsService->getManagerApprovalBreakdown($startDate, $endDate, 'peregrine');
        $ravensManagerBreakdown = $this->analyticsService->getManagerApprovalBreakdown($startDate, $endDate, 'ravens');
        $peregrineQABreakdown = $this->analyticsService->getQABreakdown($startDate, $endDate, 'peregrine');
        $ravensQABreakdown = $this->analyticsService->getQABreakdown($startDate, $endDate, 'ravens');
        
        return response()->json([
            'metrics' => $metrics,
            'validatorFormMetrics' => $validatorFormMetrics,
            'validatorBreakdown' => $validatorBreakdown,
            'verifierPipeline' => $verifierPipeline,
            'verifierSubmissions' => $verifierSubmissions,
            'peregrineCloserBreakdown' => $peregrineCloserBreakdown,
            'ravensCloserBreakdown' => $ravensCloserBreakdown,
            'peregrineManagerBreakdown' => $peregrineManagerBreakdown,
            'ravensManagerBreakdown' => $ravensManagerBreakdown,
            'peregrineQABreakdown' => $peregrineQABreakdown,
            'ravensQABreakdown' => $ravensQABreakdown,
            'timestamp' => now('America/Denver')->format('M d, Y h:i A') . ' MT',
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
                // Today's business day: 7am MT to current time
                // If before 7am MT, show previous business day (yesterday 7am to now)
                $now = Carbon::now($timezone);
                if ($now->hour < 7) {
                    $start = Carbon::yesterday($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                } else {
                    $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                }
                $end = $now->copy()->setTimezone('UTC');
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        // Parse dates with Mountain timezone - start at midnight, end at end of day
                        $start = Carbon::parse($customStart, $timezone)->startOfDay()->setTimezone('UTC');
                        $end = Carbon::parse($customEnd, $timezone)->endOfDay()->setTimezone('UTC');
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
