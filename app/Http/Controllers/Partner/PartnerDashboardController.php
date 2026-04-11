<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\AgentCarrierState;
use App\Models\Lead;
use App\Services\PartnerRevenueService;
use App\Repositories\PartnerLedgerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PartnerDashboardController extends Controller
{
    protected PartnerRevenueService $revenueService;
    protected PartnerLedgerRepository $ledgerRepository;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $partner = Auth::guard('partner')->user();
            $this->revenueService = new PartnerRevenueService($partner);
            $this->ledgerRepository = new PartnerLedgerRepository();
            return $next($request);
        });
    }

    /**
     * Advanced Partner Dashboard with Revenue Analytics
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

        // ── Revenue Metrics ────────────────────────────────────────────
        $projectedRevenue = $this->revenueService->getProjectedRevenue($periodStart, $periodEnd);
        $earnedRevenue = $this->revenueService->getEarnedRevenue($periodStart, $periodEnd);
        $chargebacks = $this->revenueService->getTotalChargebacks($periodStart, $periodEnd);
        
        $partnerEarnedShare = $this->revenueService->getPartnerEarnedShare($periodStart, $periodEnd);
        $partnerProjectedShare = $this->revenueService->getPartnerProjectedShare($periodStart, $periodEnd);

        // ── Balance & Ledger ────────────────────────────────────────────
        $currentBalance = $this->ledgerRepository->getBalance($partner);
        $ledgerStats = $this->ledgerRepository->getDashboardStats($partner);
        $paymentsSummary = $this->ledgerRepository->getPaymentsSummary($partner, $periodStart, $periodEnd);
        $chargebackSummary = $this->ledgerRepository->getChargebacksSummary($partner, $periodStart, $periodEnd);

        // ── Performance Breakdowns ──────────────────────────────────
        $revenueByCarrier = $this->revenueService->getEarnedRevenueByCarrier($periodStart, $periodEnd);
        $revenueByState = $this->revenueService->getEarnedRevenueByState($periodStart, $periodEnd);
        $activeCarriers = $this->revenueService->getActiveCarriers();
        $authorizedStates = $this->revenueService->getAuthorizedStates();

        // ── Transaction History ────────────────────────────────────
        $recentTransactions = $this->revenueService->getRecentTransactions(20, $periodStart, $periodEnd);

        // ── YTD Summary ─────────────────────────────────────────────
        $ytdMetrics = $this->revenueService->getYearToDateMetrics();
        $monthlyBreakdown = $this->revenueService->getMonthlyBreakdown();

        // ── Lead Details ────────────────────────────────────────────
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

        // Commission paid/unpaid tracking
        $paidSalesQuery = Lead::where('partner_id', $partner->id)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->whereBetween('created_at', [$periodStart, $periodEnd]);
        
        $allPeriodSales = $paidSalesQuery->get();
        $paidCommissionTotal = 0;
        $unpaidCommissionTotal = 0;
        
        foreach ($allPeriodSales as $sale) {
            $saleCommission = $sale->agent_commission ?? 0;
            $partnerShare = $saleCommission - ($saleCommission * ($partner->our_commission_percentage ?? 15.0) / 100);
            
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

        return view('partner.dashboard-advanced', compact(
            'partner',
            'carrierStates',
            'activeCarriers',
            'authorizedStates',
            
            // Lead metrics
            'totalLeads',
            'monthlyLeads',
            'totalSales',
            'pendingLeads',
            
            // Revenue metrics
            'projectedRevenue',
            'earnedRevenue',
            'chargebacks',
            'partnerEarnedShare',
            'partnerProjectedShare',
            
            // Balance & Ledger
            'currentBalance',
            'ledgerStats',
            'paymentsSummary',
            'chargebackSummary',
            
            // Performance breakdown
            'revenueByCarrier',
            'revenueByState',
            
            // Transaction history
            'recentTransactions',
            
            // YTD summary
            'ytdMetrics',
            'monthlyBreakdown',
            
            // Commission tracking
            'commissionPaid',
            'commissionUnpaid',
            
            // Recent leads
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
