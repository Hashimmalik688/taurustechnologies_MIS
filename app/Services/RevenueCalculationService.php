<?php

namespace App\Services;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueCalculationService
{
    /**
     * Calculate revenue for a specific period
     * Formula: Premium × 9 × Commission Rate
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public function calculateMonthlyRevenue(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all approved sales
        $approvedSales = Lead::where('status', 'accepted')
            ->whereNotNull('sale_date')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        // Get all chargebacks
        $chargebacks = Lead::where('status', 'chargeback')
            ->whereNotNull('chargeback_marked_date')
            ->whereBetween('chargeback_marked_date', [$startDate, $endDate])
            ->get();

        // Calculate sales revenue
        $salesRevenue = $this->calculateLeadsRevenue($approvedSales);
        
        // Calculate chargeback revenue (negative)
        $chargebackRevenue = $this->calculateLeadsRevenue($chargebacks);

        // Net revenue
        $netRevenue = $salesRevenue['total'] - $chargebackRevenue['total'];

        return [
            'sales' => [
                'count' => $approvedSales->count(),
                'revenue' => $salesRevenue['total'],
                'details' => $salesRevenue['details'],
            ],
            'chargebacks' => [
                'count' => $chargebacks->count(),
                'revenue' => $chargebackRevenue['total'],
                'details' => $chargebackRevenue['details'],
            ],
            'net' => [
                'count' => $approvedSales->count() - $chargebacks->count(),
                'revenue' => $netRevenue,
            ],
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create($year, $month, 1)->format('F'),
            ],
        ];
    }

    /**
     * Calculate revenue from a collection of leads
     * Formula: Premium × 9 × Commission Rate
     * 
     * @param \Illuminate\Support\Collection $leads
     * @return array
     */
    protected function calculateLeadsRevenue($leads): array
    {
        $totalRevenue = 0;
        $details = [];

        foreach ($leads as $lead) {
            $premium = $lead->monthly_premium ?? 0;
            $commission = $lead->insuranceCarrier->commission ?? 0.10; // Default 10% if not set
            
            // Formula: Premium × 9 months × Commission Rate
            $revenue = $premium * 9 * $commission;
            
            $totalRevenue += $revenue;
            
            $details[] = [
                'lead_id' => $lead->id,
                'cn_name' => $lead->cn_name,
                'premium' => $premium,
                'commission' => $commission,
                'revenue' => $revenue,
                'carrier' => $lead->carrier_name ?? 'N/A',
            ];
        }

        return [
            'total' => round($totalRevenue, 2),
            'details' => $details,
        ];
    }

    /**
     * Calculate year-to-date revenue
     * 
     * @param int $year
     * @return array
     */
    public function calculateYearToDateRevenue(int $year): array
    {
        $monthlyData = [];
        $totalSalesRevenue = 0;
        $totalChargebackRevenue = 0;
        $totalSalesCount = 0;
        $totalChargebackCount = 0;

        for ($month = 1; $month <= 12; $month++) {
            $data = $this->calculateMonthlyRevenue($year, $month);
            $monthlyData[] = $data;
            
            $totalSalesRevenue += $data['sales']['revenue'];
            $totalChargebackRevenue += $data['chargebacks']['revenue'];
            $totalSalesCount += $data['sales']['count'];
            $totalChargebackCount += $data['chargebacks']['count'];
        }

        return [
            'monthly_breakdown' => $monthlyData,
            'year_total' => [
                'sales_revenue' => $totalSalesRevenue,
                'chargeback_revenue' => $totalChargebackRevenue,
                'net_revenue' => $totalSalesRevenue - $totalChargebackRevenue,
                'sales_count' => $totalSalesCount,
                'chargeback_count' => $totalChargebackCount,
                'net_count' => $totalSalesCount - $totalChargebackCount,
            ],
            'year' => $year,
        ];
    }

    /**
     * Get revenue summary for dashboard
     * 
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public function getDashboardSummary(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $currentMonth = $this->calculateMonthlyRevenue($year, $month);
        
        // Previous month for comparison
        $prevDate = Carbon::create($year, $month, 1)->subMonth();
        $previousMonth = $this->calculateMonthlyRevenue($prevDate->year, $prevDate->month);

        // Calculate growth
        $revenueGrowth = $previousMonth['net']['revenue'] > 0 
            ? (($currentMonth['net']['revenue'] - $previousMonth['net']['revenue']) / $previousMonth['net']['revenue']) * 100
            : 0;

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'growth' => [
                'revenue_change' => $currentMonth['net']['revenue'] - $previousMonth['net']['revenue'],
                'revenue_growth_percentage' => round($revenueGrowth, 2),
                'sales_change' => $currentMonth['net']['count'] - $previousMonth['net']['count'],
            ],
        ];
    }

    /**
     * Get top performers by revenue
     * 
     * @param int $year
     * @param int $month
     * @param int $limit
     * @return array
     */
    public function getTopPerformers(int $year, int $month, int $limit = 10): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $performers = Lead::select(
            'managed_by',
            DB::raw('COUNT(*) as sales_count'),
            DB::raw('SUM(monthly_premium) as total_premium')
        )
        ->where('status', 'accepted')
        ->whereNotNull('sale_date')
        ->whereBetween('sale_date', [$startDate, $endDate])
        ->whereNotNull('managed_by')
        ->groupBy('managed_by')
        ->orderByDesc('total_premium')
        ->limit($limit)
        ->with('managedBy:id,name,email')
        ->get();

        $results = [];
        foreach ($performers as $performer) {
            $revenue = $performer->total_premium * 9 * 0.10; // Approximate revenue
            
            $results[] = [
                'user_id' => $performer->managed_by,
                'name' => $performer->managedBy->name ?? 'Unknown',
                'sales_count' => $performer->sales_count,
                'total_premium' => $performer->total_premium,
                'estimated_revenue' => round($revenue, 2),
            ];
        }

        return $results;
    }
}
