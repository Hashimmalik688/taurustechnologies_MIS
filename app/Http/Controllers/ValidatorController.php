<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidatorController extends Controller
{
    /**
     * Show validator dashboard with closed leads to review
     */
    public function index()
    {
        // Get leads assigned to this validator that need validation
        $pendingLeads = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Get leads sent to home office
        $homeOfficeLeads = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'pending')
            ->where('pending_reason', 'Pending:Sent to Home Office')
            ->with(['assignedCloser', 'verifier'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get completed leads (marked as sale, declined, forwarded, or returned by this validator)
        $completedLeads = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['sale', 'declined', 'forwarded'])
            ->with('validator')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate stats - based on validator performance
        $allLeads = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'sale', 'declined', 'returned'])
            ->get();

        $salesLeads = $allLeads->where('status', 'sale');
        $declinedLeads = $allLeads->where('status', 'declined');
        $returnedLeads = $allLeads->where('status', 'returned');

        return view('validator.index', compact('pendingLeads', 'homeOfficeLeads', 'completedLeads', 'allLeads', 'salesLeads', 'declinedLeads', 'returnedLeads'));
    }

    /**
     * Mark lead as sale
     */
    public function markAsSale($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $lead->update([
            'status' => 'sale',
            'validated_by' => Auth::id(),
            'sale_at' => now(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }

    /**
     * Mark lead as forwarded (sent to home office)
     */
    public function markAsForwarded($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $lead->update([
            'status' => 'pending',
            'pending_reason' => 'Pending:Sent to Home Office',
            'validated_by' => Auth::id(),
            // Keep assigned_validator_id so it stays visible to validator
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Pending:Sent to Home Office.');
    }

    /**
     * Return lead back to closer for more information
     */
    public function returnToCloser(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        // Get all form data that was edited
        $updateData = $request->except(['_token', '_method']);
        
        // Update lead with any changes made by validator
        $updateData['status'] = 'returned';
        $updateData['assigned_validator_id'] = null;
        
        $lead->update($updateData);

        return redirect()->route('validator.index')
            ->with('success', 'Lead returned to closer for more information.');
    }

    /**
     * Show edit form for validator
     */
    public function edit($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $validators = User::role('Verification Officer')->get(['id', 'name']);

        return view('validator.edit', compact('lead', 'validators'));
    }

    /**
     * Update lead from validator and mark as sale
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        // For normal validation, require full form data
        $validated = $request->validate([
            'cn_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['nullable', 'string'],
            'ssn' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'state' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:10'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'height_weight' => ['nullable', 'string', 'max:100'],
            'smoker' => ['nullable', 'boolean'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'medical_issue' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'carrier_name' => ['nullable', 'string', 'max:255'],
            'policy_type' => ['required', 'string', 'max:255'],
            'initial_draft_date' => ['required', 'date'],
            'coverage_amount' => ['required', 'numeric', 'min:0'],
            'monthly_premium' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:255'],
            'beneficiary' => ['required', 'string', 'max:255'],
            'beneficiary_dob' => ['nullable', 'date'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'routing_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:20'],
            'bank_balance' => ['nullable', 'numeric', 'min:0'],
            'card_number' => ['required_if:account_type,Card', 'nullable', 'string', 'max:19'],
            'cvv' => ['required_if:account_type,Card', 'nullable', 'string', 'max:4'],
            'expiry_date' => ['required_if:account_type,Card', 'nullable', 'string', 'max:7'],
        ]);

        // Update lead and mark as sale
        $validated['status'] = 'sale';
        $validated['validated_by'] = Auth::id();
        $validated['sale_at'] = now();
        
        $lead->update($validated);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }

    /**
     * Mark lead as failed with reason
     */
    public function markAsFailed(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'pending'])
            ->findOrFail($id);

        $validated = $request->validate([
            'decline_reason' => ['required', 'in:Declined:POA,Declined:DNQ-Age,Declined:Declined SSN,Declined:Not Interested,Declined:DNC,Declined:Cannot Afford,Declined:DNQ-Health,Declined:Declined Banking'],
        ]);

        $lead->update([
            'status' => 'declined',
            'decline_reason' => $validated['decline_reason'],
            'validated_by' => Auth::id(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as ' . $validated['decline_reason'] . '.');
    }

    /**
     * Mark lead as declined without specific reason (simple decline)
     */
    public function markAsSimpleDeclined($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'pending'])
            ->findOrFail($id);

        $lead->update([
            'status' => 'declined',
            'decline_reason' => 'Declined',
            'validated_by' => Auth::id(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Declined.');
    }

    /**
     * Mark home office lead as sale (simplified for home office)
     */
    public function markHomeOfficeSale($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'pending')
            ->where('pending_reason', 'Pending:Sent to Home Office')
            ->findOrFail($id);

        $lead->update([
            'status' => 'sale',
            'validated_by' => Auth::id(),
            'sale_at' => now(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }
}
