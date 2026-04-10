<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PartnerRevenueService;
use App\Repositories\PartnerLedgerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Partner Portal API Controller
 * 
 * RESTful endpoints for partner dashboard and analytics
 * Accessible only to authenticated partners via partner guard
 */
class PartnerApiController extends Controller
{
    protected PartnerRevenueService $revenueService;
    protected PartnerLedgerRepository $ledgerRepository;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $partner = Auth::guard('partner')->user();
            if (!$partner) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $this->revenueService = new PartnerRevenueService($partner);
            $this->ledgerRepository = new PartnerLedgerRepository();
            return $next($request);
        });
    }

    /**
     * Get dashboard revenue metrics
     * GET /api/partner/metrics/revenue
     */
    public function getRevenueMetrics(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        
        $from = $request->query('from') ? \Carbon\Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? \Carbon\Carbon::parse($request->query('to')) : null;

        return response()->json([
            'projected_revenue' => $this->revenueService->getProjectedRevenue($from, $to),
            'earned_revenue' => $this->revenueService->getEarnedRevenue($from, $to),
            'chargebacks' => $this->revenueService->getTotalChargebacks($from, $to),
            'partner_earned_share' => $this->revenueService->getPartnerEarnedShare($from, $to),
            'partner_projected_share' => $this->revenueService->getPartnerProjectedShare($from, $to),
            'taurus_percentage' => $partner->our_commission_percentage ?? 15.0,
        ]);
    }

    /**
     * Get partner balance from ledger
     * GET /api/partner/metrics/balance
     */
    public function getBalance()
    {
        $partner = Auth::guard('partner')->user();

        return response()->json([
            'current_balance' => $this->ledgerRepository->getBalance($partner),
            'stats' => $this->ledgerRepository->getDashboardStats($partner),
        ]);
    }

    /**
     * Get revenue breakdown by carrier
     * GET /api/partner/analytics/carriers
     */
    public function getCarrierBreakdown(Request $request)
    {
        $from = $request->query('from') ? \Carbon\Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? \Carbon\Carbon::parse($request->query('to')) : null;

        $breakdown = $this->revenueService->getEarnedRevenueByCarrier($from, $to);

        return response()->json([
            'carriers' => $breakdown->map(fn($item) => [
                'carrier_id' => $item['carrier_id'],
                'carrier_name' => $item['carrier']['name'] ?? null,
                'total_revenue' => $item['total_revenue'],
                'partner_share' => $item['partner_share'],
                'our_share' => $item['our_share'],
                'sales_count' => $item['sales_count'],
            ]),
            'total_carriers' => $breakdown->count(),
        ]);
    }

    /**
     * Get revenue breakdown by state
     * GET /api/partner/analytics/states
     */
    public function getStateBreakdown(Request $request)
    {
        $from = $request->query('from') ? \Carbon\Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? \Carbon\Carbon::parse($request->query('to')) : null;

        $breakdown = $this->revenueService->getEarnedRevenueByState($from, $to);

        return response()->json([
            'states' => $breakdown->map(fn($item) => [
                'state' => $item['state'],
                'total_revenue' => $item['total_revenue'],
                'partner_share' => $item['partner_share'],
                'our_share' => $item['our_share'],
                'sales_count' => $item['sales_count'],
            ]),
            'total_states' => $breakdown->count(),
        ]);
    }

    /**
     * Get year-to-date metrics
     * GET /api/partner/analytics/ytd
     */
    public function getYearToDate(Request $request)
    {
        $year = $request->query('year', now()->year);
        $metrics = $this->revenueService->getYearToDateMetrics($year);

        return response()->json($metrics);
    }

    /**
     * Get monthly breakdown chart data
     * GET /api/partner/analytics/monthly
     */
    public function getMonthlyBreakdown(Request $request)
    {
        $year = $request->query('year', now()->year);
        $breakdown = $this->revenueService->getMonthlyBreakdown($year);

        return response()->json([
            'year' => $year,
            'months' => $breakdown,
        ]);
    }

    /**
     * Get recent transactions
     * GET /api/partner/transactions
     */
    public function getTransactions(Request $request)
    {
        $limit = $request->query('limit', 50);
        $transactions = $this->revenueService->getRecentTransactions($limit);

        return response()->json([
            'transactions' => $transactions,
            'count' => $transactions->count(),
        ]);
    }

    /**
     * Get partner's full ledger
     * GET /api/partner/ledger
     */
    public function getLedger(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        
        $from = $request->query('from') ? \Carbon\Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? \Carbon\Carbon::parse($request->query('to')) : null;

        $ledger = $this->ledgerRepository->getLedger($partner, $from, $to);

        return response()->json([
            'ledger_entries' => $ledger,
            'count' => $ledger->count(),
            'current_balance' => $ledger->last()['running_balance'] ?? 0,
        ]);
    }

    /**
     * Get active carriers and authorized states
     * GET /api/partner/partnerships
     */
    public function getPartnerships()
    {
        $carriers = $this->revenueService->getActiveCarriers();
        $states = $this->revenueService->getAuthorizedStates();

        return response()->json([
            'carriers' => $carriers,
            'authorized_states' => $states,
        ]);
    }

    /**
     * Estimate commission for a potential lead
     * POST /api/partner/estimate-commission
     */
    public function estimateCommission(Request $request)
    {
        $validated = $request->validate([
            'monthly_premium' => 'required|numeric|min:0',
            'state' => 'required|string|max:2',
            'carrier_id' => 'required|integer|exists:insurance_carriers,id',
            'settlement_type' => 'sometimes|in:level,graded,gi,modified',
        ]);

        $settlementType = $validated['settlement_type'] ?? 'level';
        $commission = $this->revenueService->estimateCommission(
            $validated['monthly_premium'],
            $validated['state'],
            $validated['carrier_id'],
            $settlementType
        );

        if ($commission === null) {
            return response()->json([
                'error' => 'Commission rate not configured for this partnership',
                'details' => 'State/Carrier/Settlement type combination not found',
            ], 422);
        }

        return response()->json([
            'premium' => $validated['monthly_premium'],
            'settlement_type' => $settlementType,
            'carrier_id' => $validated['carrier_id'],
            'state' => $validated['state'],
            'estimated_commission' => $commission,
            'monthly_commission' => round($commission / 9, 2),
        ]);
    }
}
