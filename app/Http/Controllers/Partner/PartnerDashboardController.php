<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\AgentCarrierState;
use App\Models\Lead;
use App\Services\PartnerRevenueService;
use App\Repositories\PartnerLedgerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Get period filter
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $carrierId = $request->get('carrier_id');

        if ($dateFrom && $dateTo) {
            $periodStart = Carbon::parse($dateFrom)->startOfDay();
            $periodEnd   = Carbon::parse($dateTo)->endOfDay();
        } else {
            $periodStart = Carbon::parse($month . '-01')->startOfMonth();
            $periodEnd   = Carbon::parse($month . '-01')->endOfMonth();
        }

        // ── Revenue KPIs (global – not carrier-scoped) ─────────────────
        $projectedRevenue    = $this->revenueService->getProjectedRevenue($periodStart, $periodEnd);
        $earnedRevenue       = $this->revenueService->getEarnedRevenue($periodStart, $periodEnd);
        $chargebacks         = $this->revenueService->getTotalChargebacks($periodStart, $periodEnd);
        $partnerEarnedShare  = $this->revenueService->getPartnerEarnedShare($periodStart, $periodEnd);
        $partnerProjectedShare = $this->revenueService->getPartnerProjectedShare($periodStart, $periodEnd);

        // ── Balance ─────────────────────────────────────────────────────
        $currentBalance = $this->ledgerRepository->getBalance($partner);

        // ── Lead/Sale counts (carrier-scoped) ──────────────────────────
        $allTimeQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'unassigned')
            ->when($carrierId, fn ($q) => $q->where('insurance_carrier_id', $carrierId));

        $totalLeads = (clone $allTimeQuery)->count();

        $periodQuery = (clone $allTimeQuery)->whereBetween('created_at', [$periodStart, $periodEnd]);

        $monthlyLeads = (clone $periodQuery)->count();
        $totalSales   = (clone $periodQuery)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->count();
        $pendingLeads = (clone $periodQuery)
            ->whereIn('status', ['pending', 'Pending'])
            ->count();

        // ── Carriers list for filter pills ─────────────────────────────
        $activeCarriers = $this->revenueService->getActiveCarriers();

        return view('partner.dashboard-advanced', compact(
            'partner',
            'activeCarriers',
            'carrierId',
            'totalLeads',
            'monthlyLeads',
            'totalSales',
            'pendingLeads',
            'projectedRevenue',
            'earnedRevenue',
            'chargebacks',
            'partnerEarnedShare',
            'partnerProjectedShare',
            'currentBalance',
            'month'
        ));
    }

    /**
     * Carriers & States page
     */
    public function carriers(Request $request)
    {
        $partner   = Auth::guard('partner')->user();
        $carrierId = $request->get('carrier_id');

        $activeCarriers    = $this->revenueService->getActiveCarriers();
        $authorizedStates  = $this->revenueService->getAuthorizedStates();

        $carrierStates = AgentCarrierState::where('partner_id', $partner->id)
            ->with('insuranceCarrier')
            ->when($carrierId, fn ($q) => $q->where('insurance_carrier_id', $carrierId))
            ->get()
            ->groupBy('insurance_carrier_id')
            ->map(function ($states) {
                $first = $states->first();
                return [
                    'carrier'               => $first->insuranceCarrier,
                    'states'                => $states->pluck('state')->sort()->values()->toArray(),
                    'state_count'           => $states->count(),
                    'settlement_level_pct'    => $first->settlement_level_pct,
                    'settlement_graded_pct'   => $first->settlement_graded_pct,
                    'settlement_gi_pct'       => $first->settlement_gi_pct,
                    'settlement_modified_pct' => $first->settlement_modified_pct,
                ];
            });

        return view('partner.carriers', compact(
            'partner',
            'activeCarriers',
            'authorizedStates',
            'carrierStates',
            'carrierId'
        ));
    }

    /**
     * Sales page – leads table + revenue by carrier breakdown
     */
    public function sales(Request $request)
    {
        $partner   = Auth::guard('partner')->user();
        $carrierId = $request->get('carrier_id');

        $month    = $request->get('month', Carbon::now()->format('Y-m'));
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if ($dateFrom && $dateTo) {
            $periodStart = Carbon::parse($dateFrom)->startOfDay();
            $periodEnd   = Carbon::parse($dateTo)->endOfDay();
        } else {
            $periodStart = Carbon::parse($month . '-01')->startOfMonth();
            $periodEnd   = Carbon::parse($month . '-01')->endOfMonth();
        }

        $activeCarriers = $this->revenueService->getActiveCarriers();
        $taurusPct = $partner->our_commission_percentage ?? 15.0;

        // Base query (carrier + period filtered)
        $baseQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->when($carrierId, fn ($q) => $q->where('insurance_carrier_id', $carrierId))
            ->where('status', '!=', 'unassigned')
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        $monthlyLeads = (clone $baseQuery)->count();
        $totalSales   = (clone $baseQuery)
            ->whereIn('status', ['sale', 'approved', 'done', 'Accepted', 'Sale', 'Approved', 'Done'])
            ->count();
        $pendingLeads = (clone $baseQuery)
            ->whereIn('status', ['pending', 'Pending'])
            ->count();

        // Revenue by carrier (filter by carrierId post-collection if specified)
        $revenueByCarrier = $this->revenueService->getEarnedRevenueByCarrier($periodStart, $periodEnd);
        if ($carrierId) {
            $revenueByCarrier = $revenueByCarrier
                ->filter(fn ($r) => ($r['carrier_id'] ?? null) == $carrierId)
                ->values();
        }

        // Leads table
        $leads = (clone $baseQuery)
            ->with('insuranceCarrier')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partner.sales', compact(
            'partner',
            'activeCarriers',
            'carrierId',
            'monthlyLeads',
            'totalSales',
            'pendingLeads',
            'revenueByCarrier',
            'leads',
            'month',
            'taurusPct'
        ));
    }

    /**
     * Full Ledger page – complete transaction history, no row cap
     */
    public function ledger(Request $request)
    {
        $partner   = Auth::guard('partner')->user();
        $carrierId = $request->get('carrier_id');

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $periodStart = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $periodEnd   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : null;

        $activeCarriers = $this->revenueService->getActiveCarriers();
        $currentBalance = $this->ledgerRepository->getBalance($partner);

        // Full ledger – AR account 1200, with optional carrier & date filters
        $arAccount = DB::table('chart_of_accounts')->where('account_code', '1200')->first();

        $ledgerEntries = collect();
        if ($arAccount) {
            $rows = DB::table('ledger_journal_entry_lines as l')
                ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
                ->leftJoin('insurance_carriers as ic', 'l.insurance_carrier_id', '=', 'ic.id')
                ->where('l.partner_id', $partner->id)
                ->where('l.account_id', $arAccount->id)
                ->when($carrierId, fn ($q) => $q->where('l.insurance_carrier_id', $carrierId))
                ->when($periodStart, fn ($q) => $q->where('je.entry_date', '>=', $periodStart))
                ->when($periodEnd,   fn ($q) => $q->where('je.entry_date', '<=', $periodEnd))
                ->orderBy('je.entry_date', 'asc')
                ->orderBy('l.id', 'asc')
                ->select([
                    'l.id',
                    'je.entry_date',
                    'je.type',
                    'je.reference',
                    'ic.name as carrier_name',
                    'l.debit',
                    'l.credit',
                    'je.description',
                ])
                ->get();

            $runBal = 0;
            $ledgerEntries = $rows->map(function ($line) use (&$runBal) {
                $runBal += ((float) $line->debit - (float) $line->credit);
                return [
                    'date'            => Carbon::parse($line->entry_date),
                    'type'            => $line->type,
                    'reference'       => $line->reference,
                    'carrier'         => $line->carrier_name ?? 'General',
                    'debit'           => (float) $line->debit,
                    'credit'          => (float) $line->credit,
                    'running_balance' => $runBal,
                    'description'     => $line->description,
                ];
            });
        }

        return view('partner.ledger', compact(
            'partner',
            'activeCarriers',
            'carrierId',
            'ledgerEntries',
            'currentBalance',
            'dateFrom',
            'dateTo'
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
