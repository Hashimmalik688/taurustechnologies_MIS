<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\InsuranceCarrier;
use App\Models\LedgerJournalEntry;
use App\Models\LedgerJournalEntryLine;
use App\Models\Partner;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class LedgerJournalController extends Controller
{
    protected LedgerService $ledger;

    public function __construct(LedgerService $ledger)
    {
        $this->ledger = $ledger;
    }

    // ── Accounting Dashboard ───────────────────────────────────────────────

    public function dashboard()
    {
        $totalSales      = LedgerJournalEntry::where('type', 'sale')->sum('total_debit');
        $totalChargebacks= LedgerJournalEntry::whereIn('type', ['chargeback', 'sales_return'])->sum('total_debit');
        $totalPayments   = LedgerJournalEntry::where('type', 'payment_received')->sum('total_debit');
        $totalEntries    = LedgerJournalEntry::count();
        $netAR           = $totalSales - $totalChargebacks - $totalPayments;

        $thisMonth = now()->startOfMonth()->toDateString();
        $salesThisMonth  = LedgerJournalEntry::where('type', 'sale')
                            ->where('entry_date', '>=', $thisMonth)->sum('total_debit');
        $chargesThisMonth= LedgerJournalEntry::whereIn('type', ['chargeback', 'sales_return'])
                            ->where('entry_date', '>=', $thisMonth)->sum('total_debit');

        $recentEntries = LedgerJournalEntry::with('creator')
                            ->orderByDesc('entry_date')->orderByDesc('id')
                            ->limit(8)->get();

        // Monthly sales trend (last 6 months)
        $trend = DB::table('ledger_journal_entries')
            ->selectRaw("DATE_FORMAT(entry_date, '%b %Y') as month, DATE_FORMAT(entry_date, '%Y-%m') as ym,
                SUM(CASE WHEN type='sale' THEN total_debit ELSE 0 END) as sales,
                SUM(CASE WHEN type IN ('chargeback','sales_return') THEN total_debit ELSE 0 END) as chargebacks")
            ->where('entry_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym', 'month')
            ->orderBy('ym')
            ->get();

        // AR balances by partner
        $arAccount = \App\Models\ChartOfAccount::where('account_code', '1200')->first();
        $partnerBalances = [];
        if ($arAccount) {
            $partnerBalances = DB::table('ledger_journal_entry_lines as l')
                ->join('partners as p', 'l.partner_id', '=', 'p.id')
                ->where('l.account_id', $arAccount->id)
                ->groupBy('l.partner_id', 'p.name')
                ->selectRaw('l.partner_id, p.name as partner_name,
                    SUM(l.debit) as total_dr, SUM(l.credit) as total_cr,
                    SUM(l.debit) - SUM(l.credit) as balance')
                ->having('balance', '!=', 0)
                ->orderByDesc('balance')
                ->limit(5)
                ->get();
        }

        return view('admin.accounting.dashboard', compact(
            'totalSales','totalChargebacks','totalPayments','totalEntries','netAR',
            'salesThisMonth','chargesThisMonth','recentEntries','trend','partnerBalances'
        ));
    }

    // ── Sales Ledger (AR Sub-ledger) ───────────────────────────────────────

    public function salesLedger(Request $request)
    {
        $arAccount = \App\Models\ChartOfAccount::where('account_code', '1200')->first();

        $partnersWithAR = collect();
        if ($arAccount) {
            $partnersWithAR = DB::table('ledger_journal_entry_lines as l')
                ->join('partners as p', 'l.partner_id', '=', 'p.id')
                ->where('l.account_id', $arAccount->id)
                ->groupBy('l.partner_id', 'p.name', 'p.code')
                ->selectRaw('l.partner_id, p.name as partner_name, p.code as partner_code,
                    SUM(l.debit) as total_dr, SUM(l.credit) as total_cr,
                    SUM(l.debit) - SUM(l.credit) as balance,
                    COUNT(DISTINCT l.journal_entry_id) as tx_count,
                    MAX(je.entry_date) as last_activity')
                ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
                ->orderByDesc('balance')
                ->get();
        }

        $totalDr      = $partnersWithAR->sum('total_dr');
        $totalCr      = $partnersWithAR->sum('total_cr');
        $totalBalance = $partnersWithAR->sum('balance');

        $partners = \App\Models\Partner::where('is_active', true)->orderBy('name')->get(['id','name','code']);

        // ── All Sales Entries (flat table — sales only, returns on separate page) ──
        $entryQuery = LedgerJournalEntry::with(['creator'])
            ->where('type', 'sale')
            ->orderByDesc('entry_date')
            ->orderByDesc('id');

        $allEntriesTotal = (clone $entryQuery)->count();
        $allEntries      = $entryQuery->paginate(50, ['*'], 'epage');

        // Flag which entries have a corresponding sales return (chargebacked)
        $entryIds      = $allEntries->pluck('id');
        $returnedLeads = \App\Models\Lead::whereIn('ledger_journal_entry_id', $entryIds)
            ->whereNotNull('ledger_sales_return_entry_id')
            ->pluck('ledger_sales_return_entry_id', 'ledger_journal_entry_id');

        return view('admin.accounting.sales-ledger.index',
            compact('partnersWithAR','totalDr','totalCr','totalBalance','partners','allEntries','allEntriesTotal','returnedLeads'));
    }

    public function salesLedgerPartner(Request $request, int $partnerId)
    {
        $partner   = \App\Models\Partner::findOrFail($partnerId);
        $arAccount = \App\Models\ChartOfAccount::where('account_code', '1200')->first();

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->leftJoin('insurance_carriers as ic', 'l.insurance_carrier_id', '=', 'ic.id')
            ->where('l.partner_id', $partnerId)
            ->where('l.account_id', $arAccount?->id ?? 0);

        if ($request->filled('date_from')) {
            $query->where('je.entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('je.entry_date', '<=', $request->date_to);
        }
        if ($request->filled('carrier_id')) {
            $query->where('l.insurance_carrier_id', $request->carrier_id);
        }

        $lines = $query->selectRaw('
            je.id as entry_id, je.entry_number, je.entry_date, je.type,
            je.description, je.reference, je.insured_name,
            l.debit, l.credit,
            ic.name as carrier_name
        ')->orderBy('je.entry_date')->orderBy('je.id')->get();

        // Compute running balance
        $running = 0;
        $lines = $lines->map(function ($row) use (&$running) {
            $running += ($row->debit - $row->credit);
            $row->running_balance = $running;
            return $row;
        });

        $totalDr = $lines->sum('debit');
        $totalCr = $lines->sum('credit');
        $closingBalance = $running;

        $carriers = \App\Models\InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id','name']);

        return view('admin.accounting.sales-ledger.partner',
            compact('partner','lines','totalDr','totalCr','closingBalance','carriers'));
    }

    /**
     * Sales Returns sub-ledger — shows all 'sales_return' type entries.
     * Each row links back to its originating lead for full context.
     */
    public function salesReturnLedger(Request $request)
    {
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');
        $partnerId = $request->get('partner_id');
        $search    = $request->get('search');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfYear()->toDateString();
            $dateTo   = now()->toDateString();
        }

        $entryQuery = LedgerJournalEntry::with(['creator'])
            ->where('type', 'sales_return');

        if (!$search) {
            if ($dateFrom) {
                $entryQuery->whereDate('entry_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $entryQuery->whereDate('entry_date', '<=', $dateTo);
            }
        }

        if ($partnerId) {
            $entryQuery->whereHas('lines', function ($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            });
        }

        if ($search) {
            $entryQuery->where(function ($q) use ($search) {
                $q->where('insured_name', 'like', "%{$search}%")
                  ->orWhere('reference',   'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $entryQuery->orderByDesc('entry_date')->orderByDesc('id');

        $totalAmount  = (clone $entryQuery)->sum('total_debit');
        $totalEntries = (clone $entryQuery)->count();
        $entries      = $entryQuery->paginate(50);

        // Partner breakdown: group total_debit by partner via lines
        $arCode    = \App\Models\ChartOfAccount::where('account_code', '1200')->value('id');
        $partnerBreakdown = collect();
        if ($arCode) {
            $partnerBreakdown = DB::table('ledger_journal_entry_lines as l')
                ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
                ->join('partners as p', 'l.partner_id', '=', 'p.id')
                ->where('je.type', 'sales_return')
                ->where('l.account_id', $arCode)
                ->when(!$search && $dateFrom, fn($q) => $q->where('je.entry_date', '>=', $dateFrom))
                ->when(!$search && $dateTo,   fn($q) => $q->where('je.entry_date', '<=', $dateTo))
                ->groupBy('l.partner_id', 'p.name', 'p.code')
                ->selectRaw('l.partner_id, p.name as partner_name, p.code as partner_code, SUM(l.credit) as total_amount, COUNT(DISTINCT l.journal_entry_id) as tx_count')
                ->orderByDesc('total_amount')
                ->get();
        }

        // Map entry_id → lead for linking back to the pipeline
        $entryIds    = $entries->pluck('id');
        $leadsByEntry = \App\Models\Lead::whereIn('ledger_sales_return_entry_id', $entryIds)
            ->get(['id', 'ledger_sales_return_entry_id', 'ledger_chargeback_paid_entry_id', 'cn_name'])
            ->keyBy('ledger_sales_return_entry_id');

        $partners = \App\Models\Partner::where('is_active', true)->orderBy('name')->get(['id','name','code']);

        return view('admin.accounting.sales-ledger.returns', compact(
            'entries', 'totalAmount', 'totalEntries',
            'partnerBreakdown', 'leadsByEntry',
            'partners', 'dateFrom', 'dateTo', 'partnerId', 'search'
        ));
    }

    // ── Journal Entries List ───────────────────────────────────────────────

    public function index(Request $request)
    {
        if ($request->has('draw')) {
            $entries = LedgerJournalEntry::with('creator')
                ->select('ledger_journal_entries.*');

            if ($request->filled('type')) {
                $entries->where('type', $request->type);
            }

            if ($request->filled('date_from')) {
                $entries->where('entry_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $entries->where('entry_date', '<=', $request->date_to);
            }

            return DataTables::of($entries)
                ->editColumn('entry_date', fn ($row) => $row->entry_date->format('Y-m-d'))
                ->addColumn('type_badge', function ($row) {
                    $map = [
                        'sale'             => ['label' => 'Sale',           'cls' => 'acct-badge-sale'],
                        'payment_received' => ['label' => 'Payment',        'cls' => 'acct-badge-payment'],
                        'opening_balance'  => ['label' => 'Opening Bal.',   'cls' => 'acct-badge-opening'],
                        'chargeback'       => ['label' => 'Chargeback',     'cls' => 'acct-badge-chargeback'],
                        'general'          => ['label' => 'General',        'cls' => 'acct-badge-general'],
                    ];
                    $m = $map[$row->type] ?? ['label' => e($row->type_label), 'cls' => 'acct-badge-general'];
                    return '<span class="acct-type-badge ' . $m['cls'] . '">' . $m['label'] . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('admin.accounting.journal.show', $row->id) . '" class="btn-acct-view">
                        <i class="bx bx-show-alt"></i> View
                    </a>';
                })
                ->rawColumns(['type_badge', 'actions'])
                ->make(true);
        }

        // Summary stats for KPI bar
        $stats = [
            'entry_count'    => LedgerJournalEntry::count(),
            'total_sales'    => LedgerJournalEntry::where('type', 'sale')->sum('total_debit'),
            'total_payments' => LedgerJournalEntry::where('type', 'payment_received')->sum('total_debit'),
            'partner_count'  => DB::table('ledger_journal_entry_lines')
                                    ->whereNotNull('partner_id')
                                    ->distinct('partner_id')
                                    ->count('partner_id'),
        ];
        $stats['net_outstanding'] = $stats['total_sales'] - $stats['total_payments'];

        return view('admin.accounting.journal.index', compact('stats'));
    }

    public function show(int $id)
    {
        $entry = LedgerJournalEntry::with(['lines.account', 'lines.partner', 'lines.carrier', 'creator'])
            ->findOrFail($id);

        return view('admin.accounting.journal.show', compact('entry'));
    }

    public function printEntry(int $id)
    {
        $entry = LedgerJournalEntry::with(['lines.account', 'lines.partner', 'lines.carrier', 'creator'])
            ->findOrFail($id);

        return view('admin.accounting.journal.print', compact('entry'));
    }

    // ── Quick: Record a Sale ───────────────────────────────────────────────

    public function recordSaleForm()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get();
        return view('admin.accounting.quick.sale', compact('partners'));
    }

    public function storeSale(Request $request)
    {
        $data = $request->validate([
            'partner_id'           => 'required|exists:partners,id',
            'insurance_carrier_id' => 'nullable|exists:insurance_carriers,id',
            'insured_name'         => 'nullable|string|max:200',
            'gross_amount'         => 'nullable|numeric|min:0.01',
            'our_share_percentage' => 'nullable|numeric|min:0|max:100',
            'amount'               => 'required|numeric|min:0.01',
            'entry_date'           => 'required|date',
            'reference'            => 'nullable|string|max:100',
            'description'          => 'required|string|max:500',
        ]);

        // Recalculate share amount server-side when both gross + % are provided
        $gross      = isset($data['gross_amount']) ? (float) $data['gross_amount'] : null;
        $sharePct   = isset($data['our_share_percentage']) ? (float) $data['our_share_percentage'] : null;
        $amount     = ($gross !== null && $sharePct !== null)
                        ? round($gross * $sharePct / 100, 2)
                        : (float) $data['amount'];

        $entry = $this->ledger->createSaleEntry(
            (int)   $data['partner_id'],
            $amount,
            $data['entry_date'],
            $data['description'],
            $data['reference'] ?? null,
            isset($data['insurance_carrier_id']) ? (int) $data['insurance_carrier_id'] : null,
            $gross,
            $sharePct,
            $data['insured_name'] ?? null
        );

        return redirect()
            ->route('admin.accounting.journal.show', $entry->id)
            ->with('success', 'Sale recorded successfully — ' . $entry->entry_number);
    }

    // ── Quick: Record a Payment Received ──────────────────────────────────

    public function recordPaymentForm()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get();
        return view('admin.accounting.quick.payment', compact('partners'));
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'partner_id'           => 'required|exists:partners,id',
            'insurance_carrier_id' => 'nullable|exists:insurance_carriers,id',
            'amount'               => 'required|numeric|min:0.01',
            'entry_date'           => 'required|date',
            'reference'            => 'nullable|string|max:100',
            'description'          => 'required|string|max:500',
        ]);

        $entry = $this->ledger->createPaymentEntry(
            (int)   $data['partner_id'],
            (float) $data['amount'],
            $data['entry_date'],
            $data['description'],
            $data['reference'] ?? null,
            isset($data['insurance_carrier_id']) ? (int) $data['insurance_carrier_id'] : null
        );

        return redirect()
            ->route('admin.accounting.journal.show', $entry->id)
            ->with('success', 'Payment received recorded — ' . $entry->entry_number);
    }

    // ── Quick: Opening Balance ─────────────────────────────────────────────

    public function openingBalanceForm()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get();
        return view('admin.accounting.quick.opening-balance', compact('partners'));
    }

    public function storeOpeningBalance(Request $request)
    {
        $data = $request->validate([
            'partner_id'           => 'required|exists:partners,id',
            'insurance_carrier_id' => 'nullable|exists:insurance_carriers,id',
            'amount'               => 'required|numeric|min:0.01',
            'normal_balance'       => 'required|in:debit,credit',
            'entry_date'           => 'required|date',
            'description'          => 'nullable|string|max:500',
        ]);

        $entry = $this->ledger->createOpeningBalanceEntry(
            (int)   $data['partner_id'],
            (float) $data['amount'],
            $data['normal_balance'],
            $data['entry_date'],
            $data['description'] ?? 'Opening Balance',
            isset($data['insurance_carrier_id']) ? (int) $data['insurance_carrier_id'] : null
        );

        return redirect()
            ->route('admin.accounting.journal.show', $entry->id)
            ->with('success', 'Opening balance recorded — ' . $entry->entry_number);
    }

    // ── General Journal Entry ──────────────────────────────────────────────

    public function createGeneral()
    {
        $accounts = ChartOfAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get(['id', 'account_code', 'account_name']);

        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.accounting.journal.create', compact('accounts', 'partners'));
    }

    public function storeGeneral(Request $request)
    {
        $request->validate([
            'entry_date'                   => 'required|date',
            'description'                  => 'required|string|max:500',
            'reference'                    => 'nullable|string|max:100',
            'lines'                        => 'required|array|min:2',
            'lines.*.account_id'           => 'required|exists:chart_of_accounts,id',
            'lines.*.partner_id'           => 'nullable|exists:partners,id',
            'lines.*.insurance_carrier_id' => 'nullable|exists:insurance_carriers,id',
            'lines.*.debit'                => 'required|numeric|min:0',
            'lines.*.credit'               => 'required|numeric|min:0',
        ]);

        try {
            $entry = $this->ledger->createGeneralEntry(
                $request->lines,
                $request->entry_date,
                $request->description,
                $request->reference
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['lines' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.accounting.journal.show', $entry->id)
            ->with('success', 'Journal entry posted — ' . $entry->entry_number);
    }

    // ── Quick: ChargeBack / Sales Return ───────────────────────────────────

    public function recordChargebackForm()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get();
        return view('admin.accounting.quick.chargeback', compact('partners'));
    }

    public function storeChargeback(Request $request)
    {
        $data = $request->validate([
            'partner_id'           => 'required|exists:partners,id',
            'insurance_carrier_id' => 'nullable|exists:insurance_carriers,id',
            'insured_name'         => 'nullable|string|max:200',
            'gross_amount'         => 'nullable|numeric|min:0.01',
            'our_share_percentage' => 'nullable|numeric|min:0|max:100',
            'amount'               => 'required|numeric|min:0.01',
            'entry_date'           => 'required|date',
            'reference'            => 'nullable|string|max:100',
            'description'          => 'required|string|max:500',
        ]);

        $gross    = isset($data['gross_amount']) ? (float) $data['gross_amount'] : null;
        $sharePct = isset($data['our_share_percentage']) ? (float) $data['our_share_percentage'] : null;
        $amount   = ($gross !== null && $sharePct !== null)
                        ? round($gross * $sharePct / 100, 2)
                        : (float) $data['amount'];

        $entry = $this->ledger->createChargebackEntry(
            (int)   $data['partner_id'],
            $amount,
            $data['entry_date'],
            $data['description'],
            $data['reference'] ?? null,
            isset($data['insurance_carrier_id']) ? (int) $data['insurance_carrier_id'] : null,
            $data['insured_name'] ?? null,
            $gross,
            $sharePct
        );

        return redirect()
            ->route('admin.accounting.journal.show', $entry->id)
            ->with('success', 'Chargeback recorded — ' . $entry->entry_number);
    }

    // ── Partner Ledger Report ──────────────────────────────────────────────

    /**
     * AJAX: return carriers linked to a given partner (via agent_carrier_states).
     */
    public function getPartnerCarriers(int $partnerId)
    {
        $partner  = Partner::findOrFail($partnerId);
        $carriers = $partner->carriers()
            ->where('insurance_carriers.is_active', true)
            ->orderBy('insurance_carriers.name')
            ->get(['insurance_carriers.id', 'insurance_carriers.name'])
            ->unique('id')
            ->values();

        return response()->json($carriers);
    }

    public function partnerLedgerSelect()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        return view('admin.accounting.partner-ledger.index', compact('partners'));
    }

    /**
     * Resolve an optional "3rd → 3rd" period from a ?period=YYYY-MM param,
     * matching the Executive Dashboard's period logic. Returns null (= All Time)
     * when no valid period param is given.
     */
    private function resolveOptionalPeriod(?string $periodParam): ?array
    {
        if (!$periodParam || !preg_match('/^\d{4}-\d{2}$/', $periodParam)) {
            return null;
        }
        $anchor = \Carbon\Carbon::createFromFormat('Y-m', $periodParam)->setDay(3);
        if ($anchor->day >= 3) {
            $start = $anchor->copy()->setDay(3)->startOfDay();
            $end   = $anchor->copy()->addMonthNoOverflow()->setDay(3)->endOfDay();
        } else {
            $start = $anchor->copy()->subMonthNoOverflow()->setDay(3)->startOfDay();
            $end   = $anchor->copy()->setDay(3)->endOfDay();
        }

        return [
            'start'            => $start,
            'end'              => $end,
            'label'            => $start->format('M j') . ' → ' . $end->format('M j, Y'),
            'selected'         => $start->format('Y-m'),
            'prev'             => $start->copy()->subMonthNoOverflow()->format('Y-m'),
            'next'             => $start->copy()->addMonthNoOverflow()->format('Y-m'),
            'is_current'       => today()->betweenIncluded($start, $end),
        ];
    }

    /** The "Y-m" key for the 3rd→3rd period currently in progress. */
    private function currentPeriodKey(): string
    {
        return today()->day >= 3
            ? today()->format('Y-m')
            : today()->copy()->subMonthNoOverflow()->format('Y-m');
    }

    public function partnerLedgerShow(int $partnerId, Request $request)
    {
        $partner   = Partner::findOrFail($partnerId);
        $arAccount = ChartOfAccount::where('account_code', LedgerService::ACCOUNT_AR)->first();
        $period    = $this->resolveOptionalPeriod($request->get('period'));

        $allLines = $arAccount
            ? LedgerJournalEntryLine::with('carrier')
                ->where('partner_id', $partnerId)
                ->where('account_id', $arAccount->id)
                ->when($period, fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->dateRange(
                    $period['start']->toDateString(),
                    $period['end']->toDateString()
                )))
                ->get()
            : collect();

        // Summarise per carrier (key 0 = unassigned / no carrier)
        $byCarrier = $allLines->groupBy(fn ($l) => $l->insurance_carrier_id ?? 0);

        $carrierSummaries = collect();
        foreach ($byCarrier as $rawId => $lines) {
            $cid     = (int) $rawId;
            $carrier = $cid > 0 ? $lines->first()->carrier : null;
            $dr      = (float) $lines->sum('debit');
            $cr      = (float) $lines->sum('credit');
            $carrierSummaries->push([
                'carrier_id'   => $cid,
                'carrier_name' => $carrier?->name ?? '— Unassigned —',
                'total_dr'     => $dr,
                'total_cr'     => $cr,
                'balance'      => $dr - $cr,
                'tx_count'     => $lines->count(),
            ]);
        }

        // Also surface carriers linked via agent_carrier_states even with no transactions
        foreach ($partner->carriers()->where('insurance_carriers.is_active', true)->get() as $c) {
            if (!$carrierSummaries->firstWhere('carrier_id', $c->id)) {
                $carrierSummaries->push([
                    'carrier_id'   => $c->id,
                    'carrier_name' => $c->name,
                    'total_dr'     => 0.0,
                    'total_cr'     => 0.0,
                    'balance'      => 0.0,
                    'tx_count'     => 0,
                ]);
            }
        }

        $carrierSummaries = $carrierSummaries->sortBy('carrier_name')->values();
        $totalDr    = (float) $allLines->sum('debit');
        $totalCr    = (float) $allLines->sum('credit');
        $netBalance = $totalDr - $totalCr;

        $currentPeriodKey = $this->currentPeriodKey();

        return view('admin.accounting.partner-ledger.overview',
            compact('partner', 'carrierSummaries', 'totalDr', 'totalCr', 'netBalance', 'period', 'currentPeriodKey'));
    }

    public function partnerCarrierLedgerShow(int $partnerId, int $carrierId, Request $request)
    {
        $partner = Partner::findOrFail($partnerId);
        $carrier = $carrierId > 0 ? InsuranceCarrier::findOrFail($carrierId) : null;
        $period  = $this->resolveOptionalPeriod($request->get('period'));

        $ledger         = $this->ledger->getPartnerCarrierLedger(
            $partnerId,
            $carrierId > 0 ? $carrierId : null,
            $period['start'] ?? null,
            $period['end'] ?? null
        );
        $lines          = $ledger['lines'];
        $openingBalance = $ledger['openingBalance'];

        $totalDr        = (float) $lines->sum('debit');
        $totalCr        = (float) $lines->sum('credit');
        $closingBalance = $lines->last()?->running_balance ?? $openingBalance;

        $currentPeriodKey = $this->currentPeriodKey();

        return view('admin.accounting.partner-ledger.carrier-show',
            compact('partner', 'carrier', 'carrierId', 'lines', 'totalDr', 'totalCr', 'closingBalance', 'period', 'openingBalance', 'currentPeriodKey'));
    }

    // ── Financial Reports ───────────────────────────────────────────────────

    /**
     * Trial Balance — sum all debits and credits per account as of a date.
     */
    public function trialBalance(Request $request)
    {
        $asOf      = $request->get('as_of', now()->toDateString());
        $partnerId = $request->get('partner_id');

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->join('chart_of_accounts as coa', 'l.account_id', '=', 'coa.id')
            ->where('je.entry_date', '<=', $asOf)
            ->groupBy('coa.id', 'coa.account_code', 'coa.account_name', 'coa.account_type')
            ->selectRaw('coa.id, coa.account_code, coa.account_name, coa.account_type,
                         SUM(l.debit) as total_debit,
                         SUM(l.credit) as total_credit')
            ->orderBy('coa.account_code');

        if ($partnerId) {
            $query->where('l.partner_id', $partnerId);
        }

        $rows = $query->get();

        $grandDebit  = $rows->sum('total_debit');
        $grandCredit = $rows->sum('total_credit');
        $balanced    = abs($grandDebit - $grandCredit) < 0.01;

        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.accounting.reports.trial-balance', compact(
            'rows', 'grandDebit', 'grandCredit', 'balanced', 'asOf', 'partnerId', 'partners'
        ));
    }

    /**
     * Profit & Loss Statement — Revenue vs Expenses for a date range.
     */
    public function profitAndLoss(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $lines = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->join('chart_of_accounts as coa', 'l.account_id', '=', 'coa.id')
            ->whereBetween('je.entry_date', [$dateFrom, $dateTo])
            ->groupBy('coa.id', 'coa.account_code', 'coa.account_name', 'coa.account_type')
            ->selectRaw('coa.id, coa.account_code, coa.account_name, coa.account_type,
                         SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')
            ->orderBy('coa.account_code')
            ->get();

        // Sales Income (4100): normal balance = credit
        $salesIncome   = (float) $lines->where('account_code', '4100')->sum(fn($r) => $r->total_credit - $r->total_debit);
        // Sales Returns (4200): normal balance = debit (contra-revenue)
        $salesReturns  = (float) $lines->where('account_code', '4200')->sum(fn($r) => $r->total_debit - $r->total_credit);
        $grossProfit   = $salesIncome - $salesReturns;

        // Expense accounts (5xxx): normal balance = debit
        $expenseRows = $lines->filter(fn($r) => str_starts_with($r->account_code, '5'));
        $totalExpenses = (float) $expenseRows->sum(fn($r) => $r->total_debit - $r->total_credit);
        $netProfit     = $grossProfit - $totalExpenses;

        return view('admin.accounting.reports.profit-loss', compact(
            'salesIncome', 'salesReturns', 'grossProfit',
            'expenseRows', 'totalExpenses', 'netProfit',
            'dateFrom', 'dateTo'
        ));
    }

    /**
     * Balance Sheet — Assets, Liabilities, Equity as of a date.
     */
    public function balanceSheet(Request $request)
    {
        $asOf = $request->get('as_of', now()->toDateString());

        $rows = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->join('chart_of_accounts as coa', 'l.account_id', '=', 'coa.id')
            ->where('je.entry_date', '<=', $asOf)
            ->groupBy('coa.id', 'coa.account_code', 'coa.account_name', 'coa.account_type')
            ->selectRaw('coa.id, coa.account_code, coa.account_name, coa.account_type,
                         SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')
            ->orderBy('coa.account_code')
            ->get();

        // Assets (1xxx): normal debit = debit - credit
        $assetRows  = $rows->filter(fn($r) => str_starts_with($r->account_code, '1'));
        $totalAssets = (float) $assetRows->sum(fn($r) => $r->total_debit - $r->total_credit);

        // Liabilities (2xxx): normal credit = credit - debit
        $liabilityRows    = $rows->filter(fn($r) => str_starts_with($r->account_code, '2'));
        $totalLiabilities = (float) $liabilityRows->sum(fn($r) => $r->total_credit - $r->total_debit);

        // Equity (3xxx): normal credit = credit - debit
        $equityRows    = $rows->filter(fn($r) => str_starts_with($r->account_code, '3'));
        $baseEquity    = (float) $equityRows->sum(fn($r) => $r->total_credit - $r->total_debit);

        // Retained Earnings = total revenue - total expenses (as of date)
        $revenue  = (float) $rows->filter(fn($r) => str_starts_with($r->account_code, '4'))
                        ->sum(fn($r) => $r->total_credit - $r->total_debit);
        $expenses = (float) $rows->filter(fn($r) => str_starts_with($r->account_code, '5'))
                        ->sum(fn($r) => $r->total_debit - $r->total_credit);
        $retainedEarnings = $revenue - $expenses;
        $totalEquity      = $baseEquity + $retainedEarnings;

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $balanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;

        return view('admin.accounting.reports.balance-sheet', compact(
            'assetRows', 'totalAssets',
            'liabilityRows', 'totalLiabilities',
            'equityRows', 'baseEquity', 'retainedEarnings', 'totalEquity',
            'totalLiabilitiesAndEquity', 'balanced', 'asOf'
        ));
    }

    /**
     * Expense Tracker — monthly breakdown of all 5xxx expense accounts.
     */
    public function expenseTracker(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // Get all expense accounts
        $expenseAccounts = ChartOfAccount::where('account_type', 'Expense')
            ->orderBy('account_code')
            ->get();

        if ($expenseAccounts->isEmpty()) {
            return view('admin.accounting.reports.expense-tracker', [
                'expenseAccounts' => collect(),
                'months'          => [],
                'matrix'          => [],
                'monthTotals'     => [],
                'accountTotals'   => [],
                'grandTotal'      => 0,
                'year'            => $year,
                'availableYears'  => [now()->year],
            ]);
        }

        $accountIds = $expenseAccounts->pluck('id')->toArray();

        $rows = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->whereIn('l.account_id', $accountIds)
            ->whereYear('je.entry_date', $year)
            ->selectRaw('l.account_id, MONTH(je.entry_date) as month_num,
                         SUM(l.debit - l.credit) as net_debit')
            ->groupBy('l.account_id', 'month_num')
            ->get();

        // Build month-label array
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = now()->setMonth($m)->format('M');
        }

        // Build matrix[accountId][monthNum] = amount
        $matrix       = [];
        $accountTotals = [];
        $monthTotals  = array_fill(1, 12, 0.0);
        $grandTotal   = 0.0;

        foreach ($expenseAccounts as $acct) {
            $matrix[$acct->id]        = array_fill(1, 12, 0.0);
            $accountTotals[$acct->id] = 0.0;
        }

        foreach ($rows as $row) {
            $amt = (float) $row->net_debit;
            $matrix[$row->account_id][$row->month_num]  = $amt;
            $accountTotals[$row->account_id]            += $amt;
            $monthTotals[$row->month_num]               += $amt;
            $grandTotal                                  += $amt;
        }

        $availableYears = DB::table('ledger_journal_entries')
            ->selectRaw('YEAR(entry_date) as yr')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('yr')
            ->toArray();

        return view('admin.accounting.reports.expense-tracker', compact(
            'expenseAccounts', 'months', 'matrix',
            'monthTotals', 'accountTotals', 'grandTotal',
            'year', 'availableYears'
        ));
    }
}