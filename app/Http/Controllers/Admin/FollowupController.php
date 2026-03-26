<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\User;
use App\Support\Roles;
use App\Support\Statuses;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    /**
     * Display followup index page for managers (assign followup person)
     */
    public function index(Request $request)
    {
        // Get leads that are in issuance (manager approved sales)
        $query = Lead::with(['insuranceCarrier', 'assignedAgent', 'followupPerson'])
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('submission_status', Statuses::SUB_APPROVED);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by carrier
        if ($request->filled('carrier')) {
            $query->where('carrier_name', $request->carrier);
        }
        
        // Filter by followup status
        if ($request->filled('followup_status')) {
            $query->where('followup_status', $request->followup_status);
        }
        
        // Month filter for sale_date
        if ($request->filled('month')) {
            $query->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('sale_date', $request->year);
        }
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::distinct()->pluck('carrier_name')->filter();
        
        // Get all employees who can be assigned for followup
        $followupUsers = User::role(Roles::EMPLOYEE)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        
        return view('admin.followup.index', compact('leads', 'carriers', 'followupUsers'));
    }

    /**
     * Update followup person assignment
     */
    public function updateFollowupPerson(Request $request, $id)
    {
        $request->validate([
            'assigned_followup_person' => 'nullable|exists:users,id'
        ]);

        $lead = Lead::findOrFail($id);

        $isReassignment = !empty($lead->assigned_followup_person)
            && $lead->assigned_followup_person != $request->assigned_followup_person;

        $lead->assigned_followup_person = $request->assigned_followup_person;
        $lead->followup_assigned_by     = auth()->id();
        $lead->followup_assigned_at     = now();

        // When reassigning, reset prior followup completion so the new person
        // sees the lead fresh in their My Followups queue.
        // Only reset if the lead hasn't been paid out (paid_at) or policy-died yet.
        if ($isReassignment && empty($lead->paid_at) && empty($lead->policy_died_at)) {
            $lead->followup_status      = null;
            $lead->followup_done_at     = null;
            $lead->followup_done_by_id  = null;
            // Pull back from Pending Draft if it was only moved there via followup-done
            if (!empty($lead->pending_draft_at) && empty($lead->paid_at)) {
                $lead->pending_draft_at    = null;
                $lead->pending_draft_by_id = null;
            }
        }

        $lead->save();

        $message = $isReassignment
            ? 'Followup reassigned successfully. Previous completion reset.'
            : 'Followup person assigned successfully.';

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Employee/Agent followup page - where they update followup status
     * Shows only leads assigned to the logged-in user
     */
    public function myFollowups(Request $request)
    {
        $userId = auth()->id();
        
        // Get leads assigned to this user for followup
        // Only show leads that are actually in Followup stage (Issued status, not yet sent to Pending Draft)
        $query = Lead::with(['insuranceCarrier', 'assignedAgent'])
            ->where('assigned_followup_person', $userId)
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->whereNull('followup_done_at')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by followup status
        if ($request->filled('followup_status')) {
            $query->where('followup_status', $request->followup_status);
        }
        
        // Filter by carrier
        if ($request->filled('carrier')) {
            $query->where('carrier_name', $request->carrier);
        }
        
        // Get leads assigned to this user for bank verification
        $bvQuery = Lead::with(['insuranceCarrier', 'assignedAgent', 'bankVerifier'])
            ->where('assigned_bank_verifier', $userId)
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->whereNotNull('sale_at');
        
        // Bank verification search functionality
        if ($request->filled('bv_search')) {
            $search = $request->bv_search;
            $bvQuery->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by bank verification status
        if ($request->filled('bv_status')) {
            $bvQuery->where('bank_verification_status', $request->bv_status);
        }
        
        // Filter by carrier for bank verification
        if ($request->filled('bv_carrier')) {
            $bvQuery->where('carrier_name', $request->bv_carrier);
        }
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::where(function($q) use ($userId) {
                $q->where('assigned_followup_person', $userId)
                  ->orWhere('assigned_bank_verifier', $userId);
            })
            ->distinct()
            ->pluck('carrier_name')
            ->filter();
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50, ['*'], 'followup_page');
        $bankVerificationLeads = $bvQuery->orderBy('issuance_date', 'desc')->paginate(50, ['*'], 'bv_page');
        
        return view('admin.followup.my-followups', compact('leads', 'carriers', 'bankVerificationLeads'));
    }

    /**
     * Update followup status (Yes/No)
     */
    public function updateFollowupStatus(Request $request, $id)
    {
        $request->validate([
            'followup_status' => 'required|in:Yes,No'
        ]);

        $lead = Lead::findOrFail($id);
        
        // Only the assigned followup person can update the status
        if ($lead->assigned_followup_person != auth()->id() && !auth()->user()->hasAnyRole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::CEO, Roles::COORDINATOR])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this followup status.'
            ], 403);
        }
        
        $lead->followup_status = $request->followup_status;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Followup status updated successfully.'
        ]);
    }

    /**
     * Update bank verification details (comment and status)
     */
    public function updateBankVerification(Request $request, $id)
    {
        $request->validate([
            'bank_verification_comment' => 'nullable|string|max:500',
            'bank_verification_status' => 'nullable|in:Good,Average,Bad'
        ]);

        $lead = Lead::findOrFail($id);
        
        // Only the assigned bank verifier can update
        if ($lead->assigned_bank_verifier != auth()->id() && !auth()->user()->hasAnyRole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::CEO, Roles::COORDINATOR])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this bank verification.'
            ], 403);
        }
        
        // Check if status is already set and user is just a bank verifier (not admin/manager)
        if ($lead->bank_verification_status && !auth()->user()->hasAnyRole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::CEO, Roles::COORDINATOR])) {
            // Bank verifier can only update comment, not status once it's set
            $lead->bank_verification_comment = $request->bank_verification_comment;
        } else {
            // Admin/Manager can change everything, or bank verifier setting status for first time
            $lead->bank_verification_comment = $request->bank_verification_comment;
            if ($request->filled('bank_verification_status')) {
                $lead->bank_verification_status = $request->bank_verification_status;
                $lead->bank_verification_date = now();
            }
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Bank verification updated successfully.'
        ]);
    }

    /**
     * Display a summary report of followup assignments per person.
     * Columns: Total Assigned | Pending (status = No) | Done (status = Yes)
     */
    public function report(Request $request)
    {
        // Default to current month if no date filter is applied (mirrors issuance page behaviour)
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'date_from' => now()->startOfMonth()->toDateString(),
                'date_to'   => now()->endOfMonth()->toDateString(),
            ]);
        }

        // Base: only leads with a followup person assigned
        $baseQuery = fn() => Lead::whereNotNull('assigned_followup_person');

        // Apply optional date range filters on followup_assigned_at
        $applyDateFilters = function ($q) use ($request) {
            if ($request->filled('date_from')) {
                $q->whereDate('followup_assigned_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('followup_assigned_at', '<=', $request->date_to);
            }
            return $q;
        };

        // Aggregate by person + status
        $rows = $applyDateFilters($baseQuery())
            ->selectRaw('assigned_followup_person, followup_status, COUNT(*) as cnt')
            ->groupBy('assigned_followup_person', 'followup_status')
            ->get();

        // Count pending (unassigned) leads — same base query as the issuance/policy-submission page
        // but scoped to those not yet assigned a followup person
        $pendingQuery = Lead::whereNull('assigned_followup_person')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('submission_status', \App\Support\Statuses::SUB_APPROVED)
            ->where(fn($q) => $q->whereNull('followup_status')->orWhere('followup_status', '!=', 'Yes'));
        if ($request->filled('date_from')) {
            $pendingQuery->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $pendingQuery->whereDate('sale_date', '<=', $request->date_to);
        }
        $totalUnassigned = $pendingQuery->count();

        // Build per-person summary
        $userIds = $rows->pluck('assigned_followup_person')->unique();
        $users   = User::whereIn('id', $userIds)->pluck('name', 'id');

        $summary = [];
        foreach ($rows as $row) {
            $uid = $row->assigned_followup_person;
            if (!isset($summary[$uid])) {
                $summary[$uid] = [
                    'name'    => $users[$uid] ?? 'Unknown',
                    'total'   => 0,
                    'pending' => 0,  // assigned but followup_status = 'No'
                    'done'    => 0,  // followup_status = 'Yes'
                ];
            }
            $summary[$uid]['total'] += $row->cnt;
            if ($row->followup_status === 'Yes') {
                $summary[$uid]['done'] += $row->cnt;
            } else {
                $summary[$uid]['pending'] += $row->cnt;
            }
        }

        // Sort by total descending
        uasort($summary, fn($a, $b) => $b['total'] <=> $a['total']);

        // Grand totals across all agents
        $grandTotal   = array_sum(array_column($summary, 'total'));
        $grandPending = array_sum(array_column($summary, 'pending'));
        $grandDone    = array_sum(array_column($summary, 'done'));

        $today = now()->toDateString();

        return view('admin.followup.report', compact(
            'summary',
            'totalUnassigned',
            'grandTotal',
            'grandPending',
            'grandDone',
            'today'
        ));
    }

    /**
     * Mark a lead's followup as Done (Closer action).
     * Once done, lead appears in manager's Pending Draft queue.
     *
     * Route: POST /followup/{id}/mark-done
     */
    public function markFollowupDone(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if ($lead->issuance_status !== \App\Support\Statuses::ISSUANCE_ISSUED) {
            return response()->json(['success' => false, 'message' => 'Lead is not in Issued state.'], 422);
        }

        if (!empty($lead->followup_done_at)) {
            return response()->json(['success' => false, 'message' => 'Followup already marked as done.'], 422);
        }

        $lead->followup_done_at    = now();
        $lead->followup_done_by_id = auth()->id();

        // Advance to Pending Draft stage automatically
        $lead->pending_draft_at    = now();
        $lead->pending_draft_by_id = auth()->id();

        // Also set legacy followup_status = 'Yes' for backwards compatibility
        $lead->followup_status = 'Yes';

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Followup marked as done. Lead moved to Pending Draft queue.',
        ]);
    }

    /**
     * Manager view: leads that have completed followup and are awaiting draft.
     * Alias for PendingDraftController context — shown in followup report area.
     *
     * Route: GET /followup/followup-done
     */
    public function followupDone(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');

        $query = Lead::with(['insuranceCarrier', 'followupDoneBy', 'notPaidBy'])
            ->whereNotNull('followup_done_at')
            ->whereNull('paid_at')
            ->whereNull('policy_died_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cn_name',      'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('closer_name',  'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $query->where('insurance_carrier_id', $carrier);
        }

        $query->whereDate('sale_date', '>=', $dateFrom)
              ->whereDate('sale_date', '<=', $dateTo);

        $totalCount        = (clone $query)->count();
        $pendingDraftCount = (clone $query)->whereNull('pending_draft_at')->count();
        $leads             = $query->orderBy('followup_done_at', 'desc')->paginate(50);
        $carriers          = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.followup.done', compact(
            'leads', 'search', 'carrier', 'dateFrom', 'dateTo',
            'carriers', 'totalCount', 'pendingDraftCount'
        ));
    }
}
