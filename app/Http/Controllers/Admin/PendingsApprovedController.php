<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Support\Statuses;
use Illuminate\Http\Request;

/**
 * Submissions
 *
 * Stage 2 in the sales pipeline.
 * Shows all leads validated as "valid" by Ravens Validator that have NOT yet been sent to
 * Pending Contract (pending_contract_at IS NULL).
 *
 * Manager actions:
 *   • Mark as Approved   — lead ready for policy submission
 *   • Mark as Declined   — reject the validated lead
 *   • Mark as Underwriting — send to underwriting
 *   • Send to Contract   — moves lead to Pending Contract
 *   • Mark Not Issued    — flags lead with a disposition for Retention to resolve
 *
 * Retention Officer actions:
 *   • Resolve Not Issued — clears the block; lead returns to this queue
 */
class PendingsApprovedController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        // Base query: ravens_validation_status = 'valid', not yet sent to Pending Contract
        $query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'notIssuedResolvedBy'])
            ->where('ravens_validation_status', 'valid')
            ->whereNull('pending_contract_at');

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

        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        // Stats
        $statsBase = Lead::where('ravens_validation_status', 'valid')->whereNull('pending_contract_at')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('cn_name', 'like', "%{$search}%")
                          ->orWhere('phone_number', 'like', "%{$search}%")
                          ->orWhere('carrier_name', 'like', "%{$search}%")
                          ->orWhere('closer_name', 'like', "%{$search}%");
                });
            })
            ->when($carrier, fn($q) => $q->where('insurance_carrier_id', $carrier))
            ->when($dateFrom, fn($q) => $q->whereDate('sale_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('sale_date', '<=', $dateTo));

        $totalCount     = (clone $statsBase)->count();
        $approvedCount  = (clone $statsBase)->where('manager_status', Statuses::MGR_APPROVED)->count();
        $declinedCount  = (clone $statsBase)->where('manager_status', Statuses::MGR_DECLINED)->count();
        $underwritingCount = (clone $statsBase)->where('manager_status', Statuses::MGR_UNDERWRITING)->count();

        $leads    = $query->orderBy('sale_date', 'desc')->paginate(50);
        $carriers = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        // Get unique partners for the modal dropdown
        $partners = Lead::distinct()
            ->whereNotNull('assigned_partner')
            ->pluck('assigned_partner')
            ->sort()
            ->values();

        return view('admin.pendings-approved.index', compact(
            'leads', 'carriers', 'search', 'carrier',
            'dateFrom', 'dateTo',
            'totalCount', 'approvedCount', 'declinedCount', 'underwritingCount',
            'partners'
        ));
    }

    /**
     * Send lead from Pendings Approved → Pending Contract.
     * Only allowed if lead is NOT currently blocked as Not Issued.
     */
    public function sendToContract(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if ($lead->manager_status !== Statuses::MGR_APPROVED) {
            return response()->json(['success' => false, 'message' => 'Lead is not manager-approved.'], 422);
        }

        if (!empty($lead->pending_contract_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has already been sent to Pending Contract.'], 422);
        }

        // Cannot send if there is an unresolved Not Issued block
        if (!empty($lead->not_issued_at) && empty($lead->not_issued_resolved_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is blocked as Not Issued. Retention must resolve it first.'], 422);
        }

        $lead->pending_contract_at    = now();
        $lead->pending_contract_by_id = auth()->id();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead sent to Pending Contract.',
        ]);
    }

    /**
     * Mark a lead as Not Issued (Manager action).
     * Sets the not_issued_disposition, not_issued_at, not_issued_by_id.
     * Clears any previous resolved state so it re-enters the Not Issued queue.
     */
    public function markNotIssued(Request $request, int $id)
    {
        $request->validate([
            'not_issued_disposition' => 'required|in:' . implode(',', array_keys(Statuses::NOT_ISSUED_DISPOSITIONS)),
        ]);

        $lead = Lead::findOrFail($id);

        if ($lead->manager_status !== Statuses::MGR_APPROVED) {
            return response()->json(['success' => false, 'message' => 'Lead is not manager-approved.'], 422);
        }

        if (!empty($lead->pending_contract_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is already in Pending Contract.'], 422);
        }

        $lead->not_issued_disposition   = $request->not_issued_disposition;
        $lead->not_issued_at            = now();
        $lead->not_issued_by_id         = auth()->id();
        $lead->not_issued_resolved_at   = null;
        $lead->not_issued_resolved_by_id = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Not Issued.',
            'disposition_label' => Statuses::NOT_ISSUED_DISPOSITIONS[$request->not_issued_disposition],
        ]);
    }

    /**
     * Retention Officer resolves a Not Issued block.
     * Clears not_issued fields so the lead goes back to the regular queue.
     */
    public function resolveNotIssued(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if (empty($lead->not_issued_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is not marked as Not Issued.'], 422);
        }

        $lead->not_issued_resolved_at    = now();
        $lead->not_issued_resolved_by_id = auth()->id();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Not Issued block resolved. Lead is back in Submissions.',
        ]);
    }

    /**
     * Update manager_status for a lead
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'manager_status' => 'required|in:approved,declined,underwriting',
        ]);

        $lead = Lead::findOrFail($id);

        // Map dropdown values to Statuses constants
        $statusMap = [
            'approved'     => Statuses::MGR_APPROVED,
            'declined'     => Statuses::MGR_DECLINED,
            'underwriting' => Statuses::MGR_UNDERWRITING,
        ];

        $lead->manager_status = $statusMap[$request->manager_status];
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Update a field on a lead (policy_number, etc)
     */
    public function updateField(Request $request, int $id)
    {
        $request->validate([
            'field' => 'required|in:policy_number,assigned_partner',
            'value' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);
        $field = $request->field;
        $lead->{$field} = $request->value;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully.',
        ]);
    }
}
