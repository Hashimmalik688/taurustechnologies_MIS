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
        
        // Custom date range takes priority over month filter
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        if ($dateFrom && $dateTo) {
            $periodStart = Carbon::parse($dateFrom)->startOfDay();
            $periodEnd = Carbon::parse($dateTo)->endOfDay();
        } else {
            $periodStart = Carbon::parse($month . '-01')->startOfMonth();
            $periodEnd = Carbon::parse($month . '-01')->endOfMonth();
        }

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

        // Base query for this partner with period filter
        $baseQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'unassigned')
            ->whereBetween('created_at', [$periodStart, $periodEnd]);
        
        // All time totals (without period filter)
        $totalLeads = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'unassigned')
            ->count();
        
        // Period filtered statistics
        $monthlyLeads = (clone $baseQuery)->count();
        $totalSales = (clone $baseQuery)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->count();
        $pendingLeads = (clone $baseQuery)
            ->whereIn('status', ['pending', 'Pending'])
            ->count();

        // Calculate total revenue (check multiple premium fields)
        $salesQuery = (clone $baseQuery)->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done']);
        
        // Get all sales for this partner in the period
        $sales = $salesQuery->with('insuranceCarrier')->get();
        
        // Calculate actual revenue from commissions
        $totalCommissionRevenue = 0;
        $totalPremiumRevenue = 0;
        
        foreach($sales as $lead) {
            $premium = $lead->monthly_premium ?? $lead->premium_amount ?? $lead->issued_premium ?? 0;
            $commission = $lead->agent_commission ?? 0;
            
            $totalCommissionRevenue += $commission;
            $totalPremiumRevenue += $premium;
        }

        // Calculate Taurus Share in dollars
        $ourCommissionPercentage = $partner->our_commission_percentage ?? 15.0;
        $taurusShareDollars = $totalCommissionRevenue * ($ourCommissionPercentage / 100);
        
        // Partner gets the remaining commission after Taurus share
        $partnerCommission = $totalCommissionRevenue - $taurusShareDollars;
        
        // For display purposes, total revenue is the commission revenue
        $totalRevenue = $totalCommissionRevenue;

        // Commission paid/unpaid tracking
        $paidSalesQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->whereBetween('created_at', [$periodStart, $periodEnd]);
        
        $paidCommissionTotal = 0;
        $unpaidCommissionTotal = 0;
        
        $allPeriodSales = (clone $paidSalesQuery)->get();
        foreach ($allPeriodSales as $sale) {
            $saleCommission = $sale->agent_commission ?? 0;
            // Deduct taurus share from each sale's commission
            $partnerShare = $saleCommission - ($saleCommission * ($ourCommissionPercentage / 100));
            
            if ($sale->commission_paid_to_partner) {
                $paidCommissionTotal += $partnerShare;
            } else {
                $unpaidCommissionTotal += $partnerShare;
            }
        }
        
        $commissionPaid = $paidCommissionTotal;
        $commissionUnpaid = $unpaidCommissionTotal;

        // Get recent leads for table (period filtered)
        $recentLeads = (clone $baseQuery)
            ->with('insuranceCarrier')
            ->orderBy('created_at', 'desc')
            ->limit(100)
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
            'commissionPaid',
            'commissionUnpaid',
            'recentLeads',
            'month'
        ));
    }

    /**
     * Mark selected leads as commission paid by partner
     */
    public function markCommissionPaid(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        
        $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
        ]);
        
        $leadIds = $request->input('lead_ids');
        
        // Only update leads belonging to this partner that are sales and not already paid
        $updated = Lead::where('partner_id', $partner->id)
            ->whereIn('id', $leadIds)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->where('commission_paid_to_partner', false)
            ->update([
                'commission_paid_to_partner' => true,
                'commission_paid_at' => now(),
            ]);
        
        return response()->json([
            'success' => true,
            'message' => $updated . ' sale(s) marked as commission paid.',
            'updated_count' => $updated,
        ]);
    }

    /**
     * Mark selected leads as commission unpaid (reverse paid status)
     */
    public function markCommissionUnpaid(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        
        $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
        ]);
        
        $leadIds = $request->input('lead_ids');
        
        // Only update leads belonging to this partner that are currently marked as paid
        $updated = Lead::where('partner_id', $partner->id)
            ->whereIn('id', $leadIds)
            ->where('commission_paid_to_partner', true)
            ->update([
                'commission_paid_to_partner' => false,
                'commission_paid_at' => null,
            ]);
        
        return response()->json([
            'success' => true,
            'message' => $updated . ' sale(s) marked as commission unpaid.',
            'updated_count' => $updated,
        ]);
    }
}
