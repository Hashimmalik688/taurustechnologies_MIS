<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Support\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $status   = $request->get('status', 'pending'); // Default to pending approval

        // Default to last 6 months if no date filters provided
        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->subMonths(6)->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        // Base query: ravens_validation_status = 'valid', not yet sent to Pending Contract
        // IMPORTANT: Only show actual SALES (same criteria as Sales page)
        // 
        // RESELLING LOGIC:
        // - When same customer is resold by different closers (even same team),
        //   only show the MOST RECENT lead (highest ID) per phone number
        // - This prevents duplicate customer sales from appearing in the queue
        // - Older resales remain in database for history but don't show in active pipeline
        
        // Subquery: Get the latest lead ID for each phone number
        $latestLeadIds = \DB::table('leads')
            ->select('phone_number', \DB::raw('MAX(id) as latest_id'))
            ->where('ravens_validation_status', 'valid')
            ->where('team', '!=', 'peregrine')
            ->whereNull('pending_contract_at')
            ->whereNotNull('closer_name')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->groupBy('phone_number')
            ->pluck('latest_id');
        
        $query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'notIssuedResolvedBy', 'qaUser'])
            ->where('ravens_validation_status', 'valid')
            ->where('team', '!=', 'peregrine')  // Exclude old Peregrine system (pre-2026)
            ->whereNull('pending_contract_at')
            ->whereIn('id', $latestLeadIds)  // Only show most recent lead per customer
            // Sales criteria - must have closer and sale timestamp
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            // At least one real sale field must be filled
            ->where(function($q) {
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            });

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

        // Status filter (from KPI clicks)
        if ($status === 'pending') {
            $query->whereNull('manager_status');
        } elseif ($status === 'approved') {
            $query->where('manager_status', 'approved');
        } elseif ($status === 'declined') {
            $query->where('manager_status', 'declined');
        } elseif ($status === 'underwriting') {
            $query->where('manager_status', 'underwriting');
        }
        // 'all' = no status filter

        // Stats - same exclusions as main query (including latest lead per customer)
        $statsLatestIds = \DB::table('leads')
            ->select('phone_number', \DB::raw('MAX(id) as latest_id'))
            ->where('ravens_validation_status', 'valid')
            ->where('team', '!=', 'peregrine')
            ->whereNull('pending_contract_at')
            ->whereNotNull('closer_name')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->groupBy('phone_number')
            ->pluck('latest_id');
        
        $statsBase = Lead::where('ravens_validation_status', 'valid')
            ->where('team', '!=', 'peregrine')  // Exclude old Peregrine system
            ->whereNull('pending_contract_at')
            ->whereIn('id', $statsLatestIds)  // Only count most recent lead per customer
            // Sales criteria - same as above
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where(function($q) {
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            })
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

        $totalCount        = (clone $statsBase)->count();
        $pendingCount      = (clone $statsBase)->whereNull('manager_status')->count();
        $approvedCount     = (clone $statsBase)->where('manager_status', 'approved')->count();
        $declinedCount     = (clone $statsBase)->where('manager_status', 'declined')->count();
        $underwritingCount = (clone $statsBase)->where('manager_status', 'underwriting')->count();

        $leads    = $query->orderBy('sale_date', 'desc')->paginate(50);
        $carriers = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        // Active partners for modal dropdown
        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.pendings-approved.index', compact(
            'leads', 'carriers', 'search', 'carrier',
            'dateFrom', 'dateTo', 'status',
            'totalCount', 'pendingCount', 'approvedCount', 'declinedCount', 'underwritingCount',
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
     * Save lead details — Decision (manager_status), App ID, Policy Number, Partner.
     */
    public function saveDecision(Request $request, int $id)
    {
        $rules = [
            'manager_status'  => 'required|in:approved,declined,underwriting',
            'app_id'          => 'nullable|string|max:100',
            'policy_number'   => 'nullable|string|max:255',
            'assigned_partner'=> 'nullable|string|max:255',
            'partner_id'      => 'nullable|exists:partners,id',
        ];

        $request->validate($rules);

        $lead = Lead::findOrFail($id);

        // Save manager_status
        $lead->manager_status = $request->manager_status;

        // Save App ID for all decisions
        if ($request->filled('app_id')) $lead->app_id = $request->app_id;

        // Only save policy_number and partner for "approved"
        if ($request->manager_status === 'approved') {
            if ($request->filled('policy_number'))    $lead->policy_number    = $request->policy_number;
            if ($request->filled('assigned_partner')) $lead->assigned_partner = $request->assigned_partner;
            if ($request->partner_id) {
                $lead->partner_id     = $request->partner_id;
                $lead->partner_set_at = now();
            }

            // Auto-send approved sales to Pending Contracts if they have policy + partner
            if (!empty($lead->policy_number) && !empty($lead->assigned_partner)) {
                $lead->pending_contract_at    = now();
                $lead->pending_contract_by_id = auth()->id();
            }
        }

        $lead->save();

        $msg = 'Decision saved: ' . ucfirst($request->manager_status);
        if ($request->manager_status === 'approved' && !empty($lead->pending_contract_at)) {
            $msg .= ' — Sent to Pending Contracts';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }

    // Legacy updateStatus removed — manager_status no longer used in flow

    /**
     * Update a single field on a lead
     */
    public function updateField(Request $request, int $id)
    {
        $request->validate([
            'field' => 'required|in:policy_number,assigned_partner,app_id',
            'value' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->{$request->field} = $request->value;
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Field updated.']);
    }

    /**
     * Recall / Send Back a declined lead to the closer for re-dial.
     * Sets recall fields so the closer sees it on their Ravens dashboard.
     */
    public function recallToCloser(Request $request, int $id)
    {
        $request->validate([
            'recall_note' => 'required|string|max:1000',
        ]);

        $lead = Lead::findOrFail($id);

        if ($lead->recall_requested_at) {
            return response()->json([
                'success' => false,
                'message' => 'This lead has already been recalled.',
            ], 422);
        }

        $lead->recall_requested_at = now();
        $lead->recall_requested_by = Auth::id();
        $lead->recall_note         = $request->recall_note;
        $lead->save();

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Submissions — Recall to Closer',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['recall_requested_at' => null]),
            'new_values' => json_encode([
                'recall_requested_at' => now()->toISOString(),
                'recall_note'         => $request->recall_note,
            ]),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead \"{$lead->cn_name}\" recalled to closer.",
        ]);
    }
}
