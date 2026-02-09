<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\AgentCarrierState;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PartnerDashboardController extends Controller
{
    /**
     * Partner dashboard
     */
    public function index(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        
        // Get month filter
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = Carbon::parse($month . '-01')->endOfMonth();

        // Get partner's assigned carriers with states grouped
        $carrierStates = AgentCarrierState::where('partner_id', $partner->id)
            ->with('insuranceCarrier')
            ->get()
            ->groupBy('insurance_carrier_id')
            ->map(function ($states) {
                $firstState = $states->first();
                return [
                    'carrier' => $firstState->insuranceCarrier,
                    'states' => $states->pluck('state')->toArray(),
                    'state_count' => $states->count(),
                    'settlement_level_pct' => $firstState->settlement_level_pct,
                    'settlement_graded_pct' => $firstState->settlement_graded_pct,
                    'settlement_gi_pct' => $firstState->settlement_gi_pct,
                    'settlement_modified_pct' => $firstState->settlement_modified_pct,
                ];
            });

        // Base query for this partner with month filter
        $baseQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'unassigned')
            ->whereBetween('created_at', [$monthStart, $monthEnd]);
        
        // All time totals (without month filter)
        $totalLeads = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'unassigned')
            ->count();
        
        // Monthly filtered statistics
        $monthlyLeads = (clone $baseQuery)->count();
        $totalSales = (clone $baseQuery)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->count();
        $pendingLeads = (clone $baseQuery)
            ->whereIn('status', ['pending', 'Pending'])
            ->count();

        // Calculate total revenue (check multiple premium fields)
        $salesQuery = (clone $baseQuery)->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done']);
        
        // Get all sales for this partner in the month
        $sales = $salesQuery->with('insuranceCarrier')->get();
        
        // Calculate actual revenue from commissions
        $totalCommissionRevenue = 0; // This is the sum of all commissions
        $totalPremiumRevenue = 0;    // This is the sum of all premiums (for reference)
        
        // Sum up all commission amounts - this becomes our "total revenue"
        foreach($sales as $lead) {
            $premium = $lead->monthly_premium ?? $lead->premium_amount ?? $lead->issued_premium ?? 0;
            $commission = $lead->agent_commission ?? 0;
            
            $totalCommissionRevenue += $commission; // Revenue = sum of commissions
            $totalPremiumRevenue += $premium;       // Keep premium total for reference
        }

        // Calculate Taurus Share in dollars (our commission from the total commission revenue)
        $ourCommissionPercentage = $partner->our_commission_percentage ?? 15.0;
        $taurusShareDollars = $totalCommissionRevenue * ($ourCommissionPercentage / 100);
        
        // Partner gets the remaining commission after Taurus share
        $partnerCommission = $totalCommissionRevenue - $taurusShareDollars;
        
        // For display purposes, total revenue is the commission revenue
        $totalRevenue = $totalCommissionRevenue;

        // Get recent leads for table (month filtered)
        $recentLeads = (clone $baseQuery)
            ->with('insuranceCarrier')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('partner.dashboard', compact(
            'partner',
            'carrierStates',
            'totalLeads',
            'monthlyLeads',
            'totalSales',
            'pendingLeads',
            'totalRevenue',
            'partnerCommission',
            'ourCommissionPercentage',
            'taurusShareDollars',
            'recentLeads',
            'month'
        ));
    }
}
