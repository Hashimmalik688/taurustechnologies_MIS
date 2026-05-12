<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Closer Performance Report
 * 
 * Shows sales metrics for each closer (and retention manager if they made sales):
 * - Sales count
 * - Approved count (submission_status = approved)
 * - Declined count (submission_status = declined)
 * - Paid count (paid_at IS NOT NULL)
 * - Chargeback count (status = chargeback)
 * 
 * Clicking on a closer name shows detailed leads with filters for date and status.
 */
class CloserReportController extends Controller
{
    /**
     * Main report view - shows aggregate counts for each closer
     */
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $team = $request->get('team');
        if (!in_array($team, \App\Support\Teams::ALL)) $team = null;

        // Default to current month if no dates provided
        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        }

        // Build base query for sales leads
        $baseQuery = Lead::query()
            ->whereNotNull('sale_at')
            ->whereNotNull('closer_name')
            ->where('closer_name', '!=', '');

        // Apply date filters on sale_date
        if ($dateFrom) {
            $baseQuery->where('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->where('sale_date', '<=', $dateTo);
        }
        if ($team) {
            $baseQuery->where('team', $team);
        }

        // Get all closers who have made sales
        $closerStats = (clone $baseQuery)
            ->select('closer_name', 'team')
            ->groupBy('closer_name', 'team')
            ->get()
            ->groupBy('closer_name')
            ->map(function ($items) use ($dateFrom, $dateTo, $team) {
                $closerName = $items->first()->closer_name;
                // Determine the closer's team: pick most-common non-null team value
                $closerTeam = $items->pluck('team')->filter()->countBy()->sortDesc()->keys()->first();

                // Base query for this closer
                $closerQuery = Lead::where('closer_name', $closerName)
                    ->whereNotNull('sale_at');

                if ($dateFrom) {
                    $closerQuery->where('sale_date', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $closerQuery->where('sale_date', '<=', $dateTo);
                }
                if ($team) {
                    $closerQuery->where('team', $team);
                }

                // Total sales
                $salesCount = (clone $closerQuery)->count();

                // Approved (submission_status = approved)
                $approvedCount = (clone $closerQuery)
                    ->where('submission_status', Statuses::SUB_APPROVED)
                    ->count();

                // Declined (submission_status = declined)
                $declinedCount = (clone $closerQuery)
                    ->where('submission_status', Statuses::SUB_DECLINED)
                    ->count();

                // Paid (paid_at IS NOT NULL)
                $paidCount = (clone $closerQuery)
                    ->whereNotNull('paid_at')
                    ->count();

                // Chargeback (status = chargeback)
                $chargebackCount = (clone $closerQuery)
                    ->where('status', Statuses::LEAD_CHARGEBACK)
                    ->count();

                // Calculate percentages
                $approvedPercentage = $salesCount > 0 ? round(($approvedCount / $salesCount) * 100, 1) : 0;
                $paidPercentage = $approvedCount > 0 ? round(($paidCount / $approvedCount) * 100, 1) : 0;

                return [
                    'closer_name' => $closerName,
                    'team' => $closerTeam,
                    'sales_count' => $salesCount,
                    'approved_count' => $approvedCount,
                    'declined_count' => $declinedCount,
                    'paid_count' => $paidCount,
                    'chargeback_count' => $chargebackCount,
                    'approved_percentage' => $approvedPercentage,
                    'paid_percentage' => $paidPercentage,
                ];
            })
            // Sort by sales count descending
            ->sortByDesc('sales_count')
            ->values();

        // Calculate totals
        $totalSales = $closerStats->sum('sales_count');
        $totalApproved = $closerStats->sum('approved_count');
        $totalPaid = $closerStats->sum('paid_count');
        
        $totals = [
            'sales_count' => $totalSales,
            'approved_count' => $totalApproved,
            'declined_count' => $closerStats->sum('declined_count'),
            'paid_count' => $totalPaid,
            'chargeback_count' => $closerStats->sum('chargeback_count'),
            'approved_percentage' => $totalSales > 0 ? round(($totalApproved / $totalSales) * 100, 1) : 0,
            'paid_percentage' => $totalApproved > 0 ? round(($totalPaid / $totalApproved) * 100, 1) : 0,
        ];

        return view('admin.reports.closer-report', compact('closerStats', 'totals', 'dateFrom', 'dateTo', 'team'));
    }

    /**
     * Drilldown view - shows individual leads for a specific closer
     */
    public function drilldown(Request $request)
    {
        $closerName = $request->get('closer_name');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $statusFilter = $request->get('status'); // sales, approved, declined, paid, chargeback
        $team = $request->get('team');
        if (!in_array($team, \App\Support\Teams::ALL)) $team = null;

        if (!$closerName) {
            return redirect()->route('settings.reports.closer-report')
                ->with('error', 'Closer name is required');
        }

        // Build base query
        $query = Lead::where('closer_name', $closerName)
            ->whereNotNull('sale_at')
            ->with(['carriers', 'managedBy']);

        // Apply date filters
        if ($dateFrom) {
            $query->where('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('sale_date', '<=', $dateTo);
        }
        if ($team) {
            $query->where('team', $team);
        }

        // Apply status filter
        if ($statusFilter) {
            switch ($statusFilter) {
                case 'approved':
                    $query->where('submission_status', Statuses::SUB_APPROVED);
                    break;
                case 'declined':
                    $query->where('submission_status', Statuses::SUB_DECLINED);
                    break;
                case 'paid':
                    $query->whereNotNull('paid_at');
                    break;
                case 'chargeback':
                    $query->where('status', Statuses::LEAD_CHARGEBACK);
                    break;
                case 'sales':
                default:
                    // No additional filter - show all sales
                    break;
            }
        }

        // Order by most recent first
        $leads = $query->orderBy('sale_date', 'desc')
            ->orderBy('sale_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        // Calculate stats for this closer (for summary at top)
        $statsQuery = Lead::where('closer_name', $closerName)
            ->whereNotNull('sale_at');
        
        if ($dateFrom) {
            $statsQuery->where('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $statsQuery->where('sale_date', '<=', $dateTo);
        }

        $stats = [
            'closer_name' => $closerName,
            'sales_count' => (clone $statsQuery)->count(),
            'approved_count' => (clone $statsQuery)->where('submission_status', Statuses::SUB_APPROVED)->count(),
            'declined_count' => (clone $statsQuery)->where('submission_status', Statuses::SUB_DECLINED)->count(),
            'paid_count' => (clone $statsQuery)->whereNotNull('paid_at')->count(),
            'chargeback_count' => (clone $statsQuery)->where('status', Statuses::LEAD_CHARGEBACK)->count(),
        ];

        return view('admin.reports.closer-report-drilldown', compact(
            'leads',
            'closerName',
            'dateFrom',
            'dateTo',
            'statusFilter',
            'stats',
            'team'
        ));
    }
}
