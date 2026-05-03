<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Services\CommissionCalculationService;
use App\Services\LedgerService;
use App\Traits\CommissionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Paid Sales
 *
 * Stage 7 (final) in the sales pipeline.
 * Read-only view of leads where paid_at IS NOT NULL.
 */
class PaidSalesController extends Controller
{
    use CommissionResolver;

    public function index(Request $request)
    {
        $search    = $request->get('search');
        $carrier   = $request->get('carrier');
        $partnerId = $request->get('partner');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        $query = Lead::with(['insuranceCarrier', 'paidBy', 'issuedByUser', 'partner', 'ledgerJournalEntry'])
            ->whereNotNull('paid_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cn_name',       'like', "%{$search}%")
                  ->orWhere('phone_number',  'like', "%{$search}%")
                  ->orWhere('carrier_name',  'like', "%{$search}%")
                  ->orWhere('closer_name',   'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $query->where('insurance_carrier_id', $carrier);
        }

        if ($partnerId) {
            $query->where('partner_id', $partnerId);
        }

        // Apply date filters (always apply them to respect user selection)
        if ($dateFrom) {
            $query->whereDate('paid_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('paid_at', '<=', $dateTo);
        }

        // Stats — KPI totals must reflect ALL filtered leads, not just the current page.
        $totalCount = (clone $query)->count();

        $leads    = $query->orderBy('paid_at', 'desc')->paginate(50);
        $carriers = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        // Shared commission service (used by the private helper)
        $commissionService = app(CommissionCalculationService::class);

        // KPI totals: all filtered leads (independent of pagination)
        $allFiltered     = (clone $query)->with('partner')->get();
        $totalCommission = 0;
        $totalOurShare   = 0;
        foreach ($allFiltered as $l) {
            $c = $this->calcLeadCommission($l, $commissionService);
            $totalCommission += $c['commission'];
            $totalOurShare   += $c['our_share'];
        }
        $totalCommission = round($totalCommission, 2);
        $totalOurShare   = round($totalOurShare, 2);

        // Unposted count (for "Post All" button badge)
        $unpostedCount = (clone $query)->whereNull('ledger_journal_entry_id')->count();

        // Chargeback count — paid leads that were later chargebacked
        $chargebackCount = (clone $query)->where('status', 'chargeback')->count();

        // Annotate each paginated row with its calculated values
        foreach ($leads as $lead) {
            $c = $this->calcLeadCommission($lead, $commissionService);
            $lead->calculated_commission = $c['commission'];
            $lead->calculated_our_share  = $c['our_share'];
        }

        return view('admin.paid-sales.index', compact(
            'leads', 'carriers', 'partners', 'search', 'carrier', 'partnerId',
            'dateFrom', 'dateTo',
            'totalCount', 'totalCommission', 'totalOurShare', 'unpostedCount', 'chargebackCount'
        ));
    }

    /**
     * Mark a paid lead as a chargeback (policy cancelled / money clawed back).
     */
    public function markChargeback(Request $request, int $id)
    {
        $lead = Lead::with('partner')->findOrFail($id);

        if (empty($lead->paid_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has not been paid yet.'], 422);
        }

        if ($lead->status === \App\Support\Statuses::LEAD_CHARGEBACK) {
            return response()->json(['success' => false, 'message' => 'Lead is already marked as chargeback.'], 422);
        }

        // A sale must be posted to the ledger before it can be chargebacked —
        // the sales return entry is the double-entry reversal of the original sale.
        if (!$lead->ledger_journal_entry_id) {
            return response()->json([
                'success' => false,
                'message' => 'This sale has not been posted to the ledger yet. Post it to the ledger before marking as Chargeback.',
            ], 422);
        }

        $lead->status                       = \App\Support\Statuses::LEAD_CHARGEBACK;
        $lead->retention_status             = \App\Support\Statuses::RETENTION_PENDING;
        $lead->chargeback_marked_by_id      = \Illuminate\Support\Facades\Auth::id();
        $lead->ledger_sales_return_status   = 'pending'; // Sales return NOT auto-posted; must be confirmed manually
        // chargeback_marked_date is auto-set by the model boot observer
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Lead marked as Chargeback. Sales return ledger entry is pending — confirm it from the Chargebacks page when ready.']);
    }

    /**
     * Post a single paid sale to the accounting ledger (double-entry journal).
     *
     * Creates a journal entry:
     *   Dr 1200 Accounts Receivable (partner)
     *   Cr 4100 Sales / Commission Income
     *
     * Marks the lead with the resulting ledger_journal_entry_id.
     */
    public function postToLedger(Request $request, int $id)
    {
        $lead = Lead::with('partner', 'insuranceCarrier')->findOrFail($id);

        if (empty($lead->paid_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has not been paid yet.'], 422);
        }

        if ($lead->ledger_journal_entry_id) {
            return response()->json(['success' => false, 'message' => 'Already posted to ledger.'], 422);
        }

        if (!$lead->partner_id) {
            return response()->json(['success' => false, 'message' => 'Lead has no partner assigned — cannot post to ledger.'], 422);
        }

        try {
            $ledger   = app(LedgerService::class);
            $commSvc  = app(CommissionCalculationService::class);
            $calc     = $this->calcLeadCommission($lead, $commSvc);
            $commission  = $calc['commission'];
            $ourSharePct = $calc['our_share_pct'];
            $ourShare    = $calc['our_share'];

            if ($ourShare <= 0) {
                return response()->json(['success' => false, 'message' => 'Calculated share is $0 — review commission settings before posting.'], 422);
            }

            $description = "Sale — {$lead->cn_name}" .
                ($lead->carrier_name ? " | {$lead->carrier_name}" : '') .
                ($lead->policy_number ? " | #{$lead->policy_number}" : '');

            $journalEntry = $ledger->createSaleEntry(
                partnerId:       $lead->partner_id,
                amount:          $ourShare,
                date:            $lead->paid_at->toDateString(),
                description:     $description,
                reference:       $lead->policy_number,
                carrierId:       $lead->insurance_carrier_id,
                grossAmount:     $commission,
                sharePercentage: $ourSharePct,
                insuredName:     $lead->cn_name,
                leadId:          $lead->id
            );

            // Link the lead back to the journal entry
            $lead->ledger_journal_entry_id = $journalEntry->id;
            $lead->save();

            return response()->json([
                'success'         => true,
                'message'         => 'Posted to ledger: ' . $journalEntry->entry_number,
                'entry_number'    => $journalEntry->entry_number,
                'journal_entry_id'=> $journalEntry->id,
                'amount'          => number_format($ourShare, 2),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PaidSales postToLedger error', [
                'lead_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk-post all unposted paid sales (within current filter, or all time) to the ledger.
     * Processes up to 200 records per request to avoid timeouts.
     */
    public function postAllToLedger(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Lead::with('partner', 'insuranceCarrier')
            ->whereNotNull('paid_at')
            ->whereNull('ledger_journal_entry_id')
            ->whereNotNull('partner_id');

        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        $leads   = $query->orderBy('paid_at', 'asc')->limit(200)->get();
        $posted  = 0;
        $skipped = 0;
        $errors  = [];

        $ledger  = app(LedgerService::class);
        $commSvc = app(CommissionCalculationService::class);

        foreach ($leads as $lead) {
            try {
                $calc        = $this->calcLeadCommission($lead, $commSvc);
                $commission  = $calc['commission'];
                $ourSharePct = $calc['our_share_pct'];
                $ourShare    = $calc['our_share'];

                if ($ourShare <= 0) {
                    $skipped++;
                    continue;
                }

                $description = "Sale — {$lead->cn_name}" .
                    ($lead->carrier_name ? " | {$lead->carrier_name}" : '') .
                    ($lead->policy_number ? " | #{$lead->policy_number}" : '');

                $journalEntry = $ledger->createSaleEntry(
                    partnerId:       $lead->partner_id,
                    amount:          $ourShare,
                    date:            $lead->paid_at->toDateString(),
                    description:     $description,
                    reference:       $lead->policy_number,
                    carrierId:       $lead->insurance_carrier_id,
                    grossAmount:     $commission,
                    sharePercentage: $ourSharePct,
                    insuredName:     $lead->cn_name,
                    leadId:          $lead->id
                );

                $lead->ledger_journal_entry_id = $journalEntry->id;
                $lead->save();
                $posted++;
            } catch (\Exception $e) {
                $errors[] = "Lead #{$lead->id}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => true,
            'posted'  => $posted,
            'skipped' => $skipped,
            'errors'  => $errors,
            'message' => "Posted {$posted} sale(s) to ledger." . ($skipped > 0 ? " Skipped {$skipped} ($0 commission)." : ''),
        ]);
    }

    /**
     * Mark a chargebacked lead as recovered/paid.
     *
     * When a partner pays back a chargebacked commission, record a recovery entry:
     *   Dr  1200 Accounts Receivable  (partner owes us — reinstated)
     *   Cr  4100 Sales Income         (income recovered on the date of recovery)
     *
     * The original sales return entry is kept intact for the full audit trail.
     */
    public function markChargebackPaid(Request $request, int $id)
    {
        $lead = Lead::with('partner', 'insuranceCarrier')->findOrFail($id);

        if ($lead->status !== \App\Support\Statuses::LEAD_CHARGEBACK) {
            return response()->json(['success' => false, 'message' => 'Lead is not in Chargeback status.'], 422);
        }

        if ($lead->ledger_chargeback_paid_entry_id) {
            return response()->json(['success' => false, 'message' => 'Chargeback recovery has already been recorded.'], 422);
        }

        if (!$lead->ledger_sales_return_entry_id) {
            return response()->json(['success' => false, 'message' => 'No Sales Return entry found for this chargeback.'], 422);
        }

        try {
            $ledger  = app(LedgerService::class);
            $commSvc = app(CommissionCalculationService::class);
            $calc    = $this->calcLeadCommission($lead, $commSvc);

            $ourShare    = $calc['our_share'];
            $commission  = $calc['commission'];
            $ourSharePct = $calc['our_share_pct'];

            if ($ourShare <= 0) {
                return response()->json(['success' => false, 'message' => 'Calculated share is $0 — cannot record recovery.'], 422);
            }

            $description = "Chargeback Recovery — {$lead->cn_name}" .
                ($lead->carrier_name ? " | {$lead->carrier_name}" : '') .
                ($lead->policy_number ? " | #{$lead->policy_number}" : '');

            $recoveryEntry = $ledger->createChargebackRecoveryEntry(
                partnerId:       $lead->partner_id,
                amount:          $ourShare,
                date:            now()->toDateString(),
                description:     $description,
                reference:       $lead->policy_number,
                carrierId:       $lead->insurance_carrier_id,
                insuredName:     $lead->cn_name,
                grossAmount:     $commission,
                sharePercentage: $ourSharePct,
                leadId:          $lead->id
            );

            $lead->ledger_chargeback_paid_entry_id = $recoveryEntry->id;
            $lead->chargeback_paid_at  = now();
            $lead->chargeback_paid_by_id = \Illuminate\Support\Facades\Auth::id();
            $lead->saveQuietly();

            return response()->json([
                'success'      => true,
                'message'      => 'Chargeback recovery posted: ' . $recoveryEntry->entry_number,
                'entry_number' => $recoveryEntry->entry_number,
                'amount'       => number_format($ourShare, 2),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PaidSales markChargebackPaid error', [
                'lead_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
