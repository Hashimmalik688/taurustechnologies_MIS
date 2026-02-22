<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Support\Roles;
use App\Support\Statuses;
use Illuminate\Http\Request;

class BankVerificationController extends Controller
{
    public function index(Request $request)
    {
        // Get all approved sales that are also issued
        $query = Lead::where('status', Statuses::LEAD_ACCEPTED)
            ->where('manager_status', Statuses::MGR_APPROVED)
            ->whereNotNull('issuance_status')
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->with(['insuranceCarrier', 'bankVerifiedByUser', 'bankVerifierAssignedByUser']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->filled('verification_status')) {
            $query->where('bank_verification_status', $request->verification_status);
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('issuance_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('issuance_date', $request->year);
        }

        $leads = $query->orderBy('issuance_date', 'desc')->paginate(50);
        
        // Single query for all bank verification status counts (instead of 4 separate COUNTs)
        $bvAgg = Lead::where('status', Statuses::LEAD_ACCEPTED)
            ->where('manager_status', Statuses::MGR_APPROVED)
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->selectRaw("
                SUM(CASE WHEN bank_verification_status = 'Good' THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN bank_verification_status = 'Average' THEN 1 ELSE 0 END) as average_count,
                SUM(CASE WHEN bank_verification_status = 'Bad' THEN 1 ELSE 0 END) as bad_count,
                SUM(CASE WHEN bank_verification_status IS NULL THEN 1 ELSE 0 END) as unverified_count
            ")
            ->first();

        $good_count = (int) ($bvAgg->good_count ?? 0);
        $average_count = (int) ($bvAgg->average_count ?? 0);
        $bad_count = (int) ($bvAgg->bad_count ?? 0);
        $unverified_count = (int) ($bvAgg->unverified_count ?? 0);

        // Get users who can be assigned for bank verification
        $bankVerifiers = User::role([Roles::EMPLOYEE, Roles::MANAGER, Roles::COORDINATOR])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.bank-verification.index', compact(
            'leads', 
            'good_count', 
            'average_count', 
            'bad_count', 
            'unverified_count',
            'bankVerifiers'
        ));
    }

    public function show($id)
    {
        $lead = Lead::with('insuranceCarrier')->findOrFail($id);
        return view('admin.bank-verification.show', compact('lead'));
    }

    public function updateVerification(Request $request, $id)
    {
        $validated = $request->validate([
            'bank_verification_status' => 'required|in:Good,Average,Bad',
            'bank_verification_notes' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update([
            'bank_verification_status' => $validated['bank_verification_status'],
            'bank_verification_date' => now(),
            'bank_verification_notes' => $validated['bank_verification_notes'],
            'bank_verified_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', "Bank verification updated for {$lead->cn_name}");
    }

    public function assignVerifier(Request $request, $id)
    {
        $request->validate([
            'assigned_bank_verifier' => 'nullable|exists:users,id'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->assigned_bank_verifier = $request->assigned_bank_verifier;
        $lead->bank_verifier_assigned_by = auth()->id();
        $lead->bank_verifier_assigned_at = now();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Bank verifier assigned successfully.'
        ]);
    }

    public function updateAssignmentDetails(Request $request, $id)
    {
        $request->validate([
            'bank_verification_comment' => 'nullable|string|max:500',
            'bank_verification_status' => 'nullable|in:Good,Average,Bad'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->bank_verification_comment = $request->bank_verification_comment;
        
        if ($request->filled('bank_verification_status')) {
            $lead->bank_verification_status = $request->bank_verification_status;
            $lead->bank_verification_date = now();
            $lead->bank_verified_by = auth()->id();
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Bank verification details updated successfully.'
        ]);
    }
}
