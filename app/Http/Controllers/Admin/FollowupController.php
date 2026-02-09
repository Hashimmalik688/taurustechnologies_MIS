<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
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
            ->where('manager_status', 'approved');
        
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
        $followupUsers = User::role('Employee')
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
        $lead->assigned_followup_person = $request->assigned_followup_person;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Followup person assigned successfully.'
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
        $query = Lead::with(['insuranceCarrier', 'assignedAgent'])
            ->where('assigned_followup_person', $userId)
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
            ->where('issuance_status', 'Issued')
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
        if ($lead->assigned_followup_person != auth()->id() && !auth()->user()->hasAnyRole(['Super Admin', 'Manager', 'CEO', 'Co-ordinator'])) {
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
        if ($lead->assigned_bank_verifier != auth()->id() && !auth()->user()->hasAnyRole(['Super Admin', 'Manager', 'CEO', 'Co-ordinator'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this bank verification.'
            ], 403);
        }
        
        // Check if status is already set and user is just a bank verifier (not admin/manager)
        if ($lead->bank_verification_status && !auth()->user()->hasAnyRole(['Super Admin', 'Manager', 'CEO', 'Co-ordinator'])) {
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
}
