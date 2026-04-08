<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Services\CommissionCalculationService;
use App\Services\LedgerService;
use App\Support\Statuses;
use App\Traits\CommissionResolver;
use Illuminate\Http\Request;

/**
 * Pending Draft
 *
 * Stage 6 in the sales pipeline.
 * Shows leads where the closer has completed the followup (followup_done_at IS NOT NULL)
 * and the first premium draft result is still pending.
 *
 * Retention Officer actions:
 *   • Mark Not Paid (FDFP) — sets FDFP type; manual_action requires a secondary
 *                             Not Issued-style disposition
 *
 * Manager / Finance actions:
 *   • Mark Paid       — moves lead to Paid Sales (paid_at set)
 *   • Mark Policy Died — terminal; resets lead.status → 'active' for Ravens re-dial
 */
class PendingDraftController extends Controller
{
    use CommissionResolver;

    public function index(Request $request)
    {
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');
        $partner  = $request->get('partner');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $tab      = $request->get('tab', 'pending'); // pending | not_paid

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        // All pending-draft leads: followup done, not yet paid/died
        $baseQuery = Lead::with(['insuranceCarrier', 'partner', 'notPaidBy', 'paidBy', 'policyDiedBy', 'followupDoneBy', 'pendingDraftBy'])
            ->whereNotNull('followup_done_at')
            ->whereNull('paid_at')
            ->whereNull('policy_died_at');

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('cn_name',        'like', "%{$search}%")
                  ->orWhere('phone_number',   'like', "%{$search}%")
                  ->orWhere('carrier_name',   'like', "%{$search}%")
                  ->orWhere('closer_name',    'like', "%{$search}%")
                  ->orWhere('policy_number',  'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $baseQuery->where('insurance_carrier_id', $carrier);
        }

        if ($partner) {
            $baseQuery->where('partner_id', $partner);
        }

        // Skip date range when searching so policy numbers are found regardless of dates
        if (!$search) {
            if ($dateFrom) {
                $baseQuery->whereDate('sale_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $baseQuery->whereDate('sale_date', '<=', $dateTo);
            }
        }

        // Stats
        $pendingCount  = (clone $baseQuery)->whereNull('not_paid_at')->count();
        $notPaidCount  = (clone $baseQuery)->whereNotNull('not_paid_at')->count();
        $totalCount    = $pendingCount + $notPaidCount;

        // Paginated results per tab
        if ($tab === 'not_paid') {
            $leads = (clone $baseQuery)->whereNotNull('not_paid_at')
                                       ->orderBy('not_paid_at', 'desc')
                                       ->paginate(50);
        } else {
            $leads = (clone $baseQuery)->whereNull('not_paid_at')
                                       ->orderBy('followup_done_at', 'desc')
                                       ->paginate(50);
        }

        $carriers   = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $partners   = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $fdfpTypes  = Statuses::FDFP_TYPES;
        $niDispositions = Statuses::NOT_ISSUED_DISPOSITIONS;

        return view('admin.pending-draft.index', compact(
            'leads', 'carriers', 'partners', 'search', 'carrier', 'partner',
            'dateFrom', 'dateTo', 'tab',
            'pendingCount', 'notPaidCount', 'totalCount',
            'fdfpTypes', 'niDispositions'
        ));
    }

    /**
     * Retention Officer marks a lead as Not Paid (FDFP).
     * If fdfp_type = 'manual_action', a secondary not_paid_manual_disposition is required.
     */
    public function markNotPaid(Request $request, int $id)
    {
        $request->validate([
            'not_paid_fdfp_type'          => 'required|in:' . implode(',', array_keys(Statuses::FDFP_TYPES)),
            'not_paid_manual_disposition' => 'required_if:not_paid_fdfp_type,manual_action|nullable|in:' . implode(',', array_keys(Statuses::NOT_ISSUED_DISPOSITIONS)),
            'not_paid_comment'            => 'nullable|string|max:1000',
        ]);

        $lead = Lead::findOrFail($id);

        if (empty($lead->followup_done_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has not completed followup yet.'], 422);
        }

        if (!empty($lead->paid_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is already marked as Paid.'], 422);
        }

        $lead->not_paid_fdfp_type          = $request->not_paid_fdfp_type;
        $lead->not_paid_manual_disposition = $request->not_paid_fdfp_type === Statuses::FDFP_MANUAL_ACTION
            ? $request->not_paid_manual_disposition
            : null;
        $lead->not_paid_comment = $request->not_paid_comment;
        $lead->not_paid_at   = now();
        $lead->not_paid_by_id = auth()->id();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Not Paid (FDFP: ' . Statuses::FDFP_TYPES[$request->not_paid_fdfp_type] . ').',
        ]);
    }

    /**
     * Manager/Finance marks a lead as Paid (first draft cleared).
     * Moves lead to Paid Sales.
     */
    public function markPaid(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if (empty($lead->followup_done_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has not completed followup yet.'], 422);
        }

        if (!empty($lead->policy_died_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is marked as Policy Died.'], 422);
        }

        $lead->paid_at    = now();
        $lead->paid_by_id = auth()->id();
        // Clear any prior Not Paid state
        $lead->not_paid_at              = null;
        $lead->not_paid_by_id           = null;
        $lead->not_paid_fdfp_type       = null;
        $lead->not_paid_manual_disposition = null;
        $lead->not_paid_comment         = null;
        $lead->save();

        // ── Auto-post to accounting ledger ────────────────────────────────
        // As soon as a sale is marked Paid, create a double-entry journal entry
        // (Dr 1200 AR / Cr 4100 Sales Income) so the ledger is always up to date.
        if ($lead->partner_id && !$lead->ledger_journal_entry_id) {
            try {
                $lead->loadMissing('partner', 'insuranceCarrier');
                $ledger  = app(LedgerService::class);
                $commSvc = app(CommissionCalculationService::class);
                $calc    = $this->calcLeadCommission($lead, $commSvc);

                if ($calc['our_share'] > 0) {
                    $description = "Sale — {$lead->cn_name}" .
                        ($lead->carrier_name ? " | {$lead->carrier_name}" : '') .
                        ($lead->policy_number ? " | #{$lead->policy_number}" : '');

                    $journalEntry = $ledger->createSaleEntry(
                        partnerId:       $lead->partner_id,
                        amount:          $calc['our_share'],
                        date:            $lead->paid_at->toDateString(),
                        description:     $description,
                        reference:       $lead->policy_number,
                        carrierId:       $lead->insurance_carrier_id,
                        grossAmount:     $calc['commission'],
                        sharePercentage: $calc['our_share_pct'],
                        insuredName:     $lead->cn_name,
                        leadId:          $lead->id
                    );

                    $lead->ledger_journal_entry_id = $journalEntry->id;
                    $lead->saveQuietly();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('PendingDraft markPaid: auto-post to ledger failed', [
                    'lead_id' => $lead->id,
                    'error'   => $e->getMessage(),
                ]);
                // Never let a ledger failure block the payment action
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Paid. Moved to Paid Sales.',
        ]);
    }

    /**
     * Mark lead as Policy Died.
     * Terminal state (no retention action), but re-dialable.
     * Resets lead.status to 'active' so it enters Ravens queue.
     */
    public function markPolicyDied(Request $request, int $id)
    {
        $request->validate([
            'policy_died_reason' => 'required|in:' . implode(',', array_keys(Statuses::POLICY_DIED_REASONS)),
        ]);

        $lead = Lead::findOrFail($id);

        if (!empty($lead->paid_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is already Paid and cannot be marked as Policy Died.'], 422);
        }

        $lead->policy_died_reason  = $request->policy_died_reason;
        $lead->policy_died_at      = now();
        $lead->policy_died_by_id   = auth()->id();

        // Reset lead to active so it re-enters the Ravens dialing queue
        $lead->status = Statuses::LEAD_ACTIVE;

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Policy Died (' . Statuses::POLICY_DIED_REASONS[$request->policy_died_reason] . '). Lead reset to active.',
        ]);
    }

    /**
     * Retention Officer clears a Not Paid flag (e.g., issue was resolved).
     * Lead goes back to the pending tab.
     */
    public function clearNotPaid(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        $lead->not_paid_at              = null;
        $lead->not_paid_by_id           = null;
        $lead->not_paid_fdfp_type       = null;
        $lead->not_paid_manual_disposition = null;
        $lead->not_paid_comment         = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Not Paid flag cleared. Lead back in Pending Draft queue.',
        ]);
    }
}
