<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\AgentCarrierState;
use App\Models\Lead;
use App\Services\CommissionCalculationService;
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

        // ── Agent routing: agents get a simplified dashboard ──
        if ($partner->type === 'agent') {
            return $this->agentDashboard($partner);
        }

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

        // ── Pending Contract counts (carrier-scoped) ───────────────────
        $allTimeQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->whereNotNull('pending_contract_at')
            ->when($carrierId, fn ($q) => $q->where('insurance_carrier_id', $carrierId));

        $totalContracts = (clone $allTimeQuery)->count();

        $periodQuery = (clone $allTimeQuery)->whereBetween('sale_date', [$periodStart, $periodEnd]);

        $monthlyContracts   = (clone $periodQuery)->count();
        $issuedContracts    = (clone $periodQuery)->where('issuance_status', 'Issued')->count();
        $notIssuedContracts = (clone $periodQuery)->where('issuance_status', 'Not Issued')->count();
        $pendingContracts   = (clone $periodQuery)->where(function ($q) {
            $q->whereNull('issuance_status')
              ->orWhere('issuance_status', 'Pending');
        })->count();
        $draftContracts     = (clone $periodQuery)
            ->whereNotNull('pending_draft_at')
            ->where('issuance_status', 'Issued')
            ->count();

        // ── Annual Premium (period-filtered, carrier-scoped) ───────────
        $totalAP = (clone $periodQuery)->sum(DB::raw('monthly_premium * 12'));

        // ── Carriers list for filter pills ─────────────────────────────
        $activeCarriers = $this->revenueService->getActiveCarriers();

        // ── Downline agent summary ─────────────────────────────────────
        $downlineAgents = $partner->agents()->get()->map(function ($agent) use ($periodStart, $periodEnd) {
            $leads = Lead::where('partner_id', $agent->id)
                ->whereNotNull('pending_contract_at');
            return [
                'id'         => $agent->id,
                'name'       => $agent->name,
                'code'       => $agent->code,
                'is_active'  => $agent->is_active,
                'total_sales'   => (clone $leads)->count(),
                'issued_sales'   => (clone $leads)->where('issuance_status', 'Issued')->count(),
                'period_sales'   => (clone $leads)->whereBetween('sale_date', [$periodStart, $periodEnd])->count(),
                'period_commission' => Lead::where('partner_id', $agent->id)
                    ->whereNotNull('pending_contract_at')
                    ->whereBetween('sale_date', [$periodStart, $periodEnd])
                    ->sum('agent_commission'),
            ];
        });

        return view('partner.dashboard-advanced', compact(
            'partner',
            'activeCarriers',
            'carrierId',
            'totalContracts',
            'monthlyContracts',
            'issuedContracts',
            'notIssuedContracts',
            'pendingContracts',
            'draftContracts',
            'totalAP',
            'projectedRevenue',
            'earnedRevenue',
            'chargebacks',
            'partnerEarnedShare',
            'partnerProjectedShare',
            'currentBalance',
            'month',
            'downlineAgents'
        ));
    }

    /**
     * Agent-specific simplified dashboard
     */
    protected function agentDashboard($agent)
    {
        $upline = $agent->parent;
        if (!$upline) {
            $upline = new \stdClass();
            $upline->name = 'N/A';
        }

        // Agent's carrier states
        $agentCarriers = AgentCarrierState::where('partner_id', $agent->id)
            ->with('insuranceCarrier')
            ->get()
            ->groupBy('insurance_carrier_id')
            ->map(function ($states) {
                $first = $states->first();
                return [
                    'carrier'               => $first->insuranceCarrier,
                    'states'                => $states->pluck('state')->sort()->values()->toArray(),
                    'state_count'           => $states->count(),
                ];
            });
        $carrierCount = $agentCarriers->count();
        $stateCount = AgentCarrierState::where('partner_id', $agent->id)->count();

        // Agent's leads
        $myLeads = Lead::where('partner_id', $agent->id)
            ->whereNotNull('pending_contract_at')
            ->with('insuranceCarrier')
            ->orderBy('sale_date', 'desc')
            ->take(50)
            ->get();

        $commSvc = new CommissionCalculationService();
        $totalCommission = 0;
        foreach ($myLeads as $lead) {
            $premiumVal    = (float) ($lead->monthly_premium ?? 0);
            $lCarrierId    = $lead->insurance_carrier_id;
            $lState        = $lead->state;
            $lSettlement   = CommissionCalculationService::normalizeSettlementType(
                $lead->settlement_type ?? $lead->policy_type
            );
            if ($premiumVal > 0 && $lCarrierId && $lState) {
                $result = $commSvc->calculateCommission(
                    $agent->id,
                    $lCarrierId,
                    $lState,
                    $lSettlement,
                    $premiumVal
                );
                if ($result['success']) {
                    $lead->agent_commission = $result['commission'];
                    $totalCommission += $result['commission'];
                }
            }
        }

        $totalSales   = Lead::where('partner_id', $agent->id)->whereNotNull('pending_contract_at')->count();
        $pendingSales = Lead::where('partner_id', $agent->id)
            ->whereNotNull('pending_contract_at')
            ->where(function ($q) {
                $q->whereNull('issuance_status')->orWhere('issuance_status', 'Pending');
            })->count();

        return view('partner.agent-dashboard', compact(
            'agent',
            'upline',
            'agentCarriers',
            'carrierCount',
            'stateCount',
            'myLeads',
            'totalSales',
            'pendingSales',
            'totalCommission'
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
        $partner      = Auth::guard('partner')->user();
        $carrierId    = $request->get('carrier_id');
        $statusFilter = $request->get('status');
        $search       = $request->get('search');

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

        // Base query — pending contracts only (carrier + period filtered)
        $baseQuery = Lead::where('partner_id', $partner->id)
            ->whereNotNull('partner_id')
            ->whereNotNull('pending_contract_at')
            ->when($carrierId, fn ($q) => $q->where('insurance_carrier_id', $carrierId))
            ->when($statusFilter, function ($q) use ($statusFilter) {
                if ($statusFilter === 'Pending Draft') {
                    $q->whereNotNull('pending_draft_at')->where('status', '!=', 'chargeback');
                } elseif ($statusFilter === 'Chargeback') {
                    $q->where('status', 'chargeback');
                } else {
                    $q->where('issuance_status', $statusFilter);
                }
            })
            ->when($search, fn ($q) => $q->where('cn_name', 'like', '%' . $search . '%'))
            ->whereBetween('sale_date', [$periodStart, $periodEnd]);

        $monthlyContracts      = (clone $baseQuery)->count();
        $issuedContracts       = (clone $baseQuery)->where('issuance_status', 'Issued')->where('status', '!=', 'chargeback')->whereNull('pending_draft_at')->count();
        $notIssuedContracts    = (clone $baseQuery)->where('issuance_status', 'Not Issued')->count();
        $pendingContracts      = (clone $baseQuery)->where(function ($q) {
            $q->whereNull('issuance_status')
              ->orWhere('issuance_status', 'Pending');
        })->count();
        $draftContracts        = (clone $baseQuery)
            ->whereNotNull('pending_draft_at')
            ->where('issuance_status', 'Issued')
            ->where('status', '!=', 'chargeback')
            ->count();
        $chargebackContracts   = (clone $baseQuery)->where('status', 'chargeback')->count();

        // ── Annual Premium (respects all active filters) ────────────────
        $totalAP = (clone $baseQuery)->sum(DB::raw('monthly_premium * 12'));

        // Revenue by carrier (filter by carrierId post-collection if specified)
        $revenueByCarrier = $this->revenueService->getEarnedRevenueByCarrier($periodStart, $periodEnd);
        if ($carrierId) {
            $revenueByCarrier = $revenueByCarrier
                ->filter(fn ($r) => ($r['carrier_id'] ?? null) == $carrierId)
                ->values();
        }

        // Leads table — recalculate commissions on-the-fly using current partner rates
        $leads = (clone $baseQuery)
            ->with('insuranceCarrier')
            ->orderBy('sale_date', 'desc')
            ->get();

        $commSvc = new CommissionCalculationService();
        foreach ($leads as $lead) {
            $premiumVal    = (float) ($lead->monthly_premium ?? 0);
            $lCarrierId    = $lead->insurance_carrier_id;
            $lState        = $lead->state;
            // Derive settlement type from settlement_type field, falling back to policy_type
            $lSettlement   = CommissionCalculationService::normalizeSettlementType(
                $lead->settlement_type ?? $lead->policy_type
            );

            if ($premiumVal > 0 && $lCarrierId && $lState) {
                $result = $commSvc->calculateCommission(
                    $partner->id,
                    $lCarrierId,
                    $lState,
                    $lSettlement,
                    $premiumVal
                );
                if ($result['success']) {
                    // Override in memory only — no DB write
                    $lead->agent_commission = $result['commission'];
                    $lead->setAttribute('_commission_live', true);
                    $lead->setAttribute('_commission_pct', $result['settlement_pct']);
                    $lead->setAttribute('_settlement_type', $lSettlement);
                }
            }
        }

        return view('partner.sales', compact(
            'partner',
            'activeCarriers',
            'carrierId',
            'monthlyContracts',
            'issuedContracts',
            'notIssuedContracts',
            'pendingContracts',
            'draftContracts',
            'chargebackContracts',
            'totalAP',
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
