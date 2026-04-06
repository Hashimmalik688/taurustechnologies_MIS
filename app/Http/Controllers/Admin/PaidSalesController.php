<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Services\CommissionCalculationService;
use App\Services\LedgerService;
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
                $q->where('cn_name',      'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name',  'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $query->where('insurance_carrier_id', $carrier);
        }

        if ($partnerId) {
            $query->where('partner_id', $partnerId);
        }

        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
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

        // Annotate each paginated row with its calculated values
        foreach ($leads as $lead) {
            $c = $this->calcLeadCommission($lead, $commissionService);
            $lead->calculated_commission = $c['commission'];
            $lead->calculated_our_share  = $c['our_share'];
        }

        return view('admin.paid-sales.index', compact(
            'leads', 'carriers', 'partners', 'search', 'carrier', 'partnerId',
            'dateFrom', 'dateTo',
            'totalCount', 'totalCommission', 'totalOurShare', 'unpostedCount'
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

        $lead->status                = \App\Support\Statuses::LEAD_CHARGEBACK;
        $lead->retention_status      = \App\Support\Statuses::RETENTION_PENDING;
        // chargeback_marked_date is auto-set by the model boot observer
        $lead->save();

        // Auto-post a Sales Return entry to the accounting ledger
        if ($lead->ledger_journal_entry_id && !$lead->ledger_sales_return_entry_id && $lead->partner_id) {
            try {
                $ledger  = app(LedgerService::class);
                $calc    = $this->calcLeadCommission($lead, app(CommissionCalculationService::class));
                $commission  = $calc['commission'];
                $ourSharePct = $calc['our_share_pct'];
                $ourShare    = $calc['our_share'];

                if ($ourShare > 0) {
                    $description = "Sales Return (Chargeback) — {$lead->cn_name}" .
                        ($lead->carrier_name ? " | {$lead->carrier_name}" : '') .
                        ($lead->policy_number ? " | #{$lead->policy_number}" : '');

                    $returnEntry = $ledger->createSalesReturnEntry(
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

                    $lead->ledger_sales_return_entry_id = $returnEntry->id;
                    $lead->saveQuietly();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('PaidSales markChargeback: could not post sales return to ledger', [
                    'lead_id' => $id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Lead marked as Chargeback.']);
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

    // ── Private helpers ─────────────────────────────────────────────────────

    /**
     * Resolve the policy type string from a lead's settlement_type / policy_type.
     * Returns one of: 'gi', 'graded', 'modified', 'level'.
     */
    private function resolveCommissionType(Lead $lead): string
    {
        $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
        if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) return 'gi';
        if (str_contains($raw, 'grad'))                             return 'graded';
        if (str_contains($raw, 'modif'))                            return 'modified';
        return 'level';
    }

    /**
     * Calculate commission totals for a lead.
     * Returns ['commission' => float, 'our_share' => float, 'our_share_pct' => float].
     */
    private function calcLeadCommission(Lead $lead, CommissionCalculationService $commSvc): array
    {
        $result     = $commSvc->calculateCommission(
            $lead->partner_id ?? 0,
            $lead->insurance_carrier_id ?? 0,
            $lead->state ?? '',
            $this->resolveCommissionType($lead),
            (float) ($lead->monthly_premium ?? 0)
        );
        $commission  = $result['success'] ? (float) ($result['commission'] ?? 0) : 0;
        $ourSharePct = $lead->partner ? (float) ($lead->partner->our_commission_percentage ?? 15.0) : 15.0;

        return [
            'commission'    => round($commission, 2),
            'our_share'     => round($commission * ($ourSharePct / 100), 2),
            'our_share_pct' => $ourSharePct,
        ];
    }
}
