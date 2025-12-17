<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParaguinsController extends Controller
{
    /**
     * Show list of leads for Paraguins live closers
     */
    public function closersIndex()
    {
        $userId = Auth::id();
        
        // Get validators for dropdown
        $validators = User::role('Verifier')->get(['id', 'name']);
        
        // Get pending/transferred leads assigned to this closer (including returned from validator)
        $pendingLeads = Lead::where('team', 'paraguins')
            ->where('managed_by', $userId)
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed/sent leads (closed, sale, or forwarded)
        $completedLeads = Lead::where('team', 'paraguins')
            ->where('managed_by', $userId)
            ->whereIn('status', ['closed', 'sale', 'forwarded'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get failed leads (includes both closer failures and validator declines)
        $failedLeads = Lead::where('team', 'paraguins')
            ->where('managed_by', $userId)
            ->whereIn('status', ['rejected', 'declined'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate stats
        $allLeads = Lead::where('team', 'paraguins')
            ->where('managed_by', $userId)
            ->get();

        return view('paraguins.closers.index', compact('pendingLeads', 'completedLeads', 'failedLeads', 'allLeads', 'validators'));
    }

    /**
     * Show form for closer to complete the lead
     */
    public function closerEdit($id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('managed_by', Auth::id())
            ->findOrFail($id);

        $validators = User::role('Verification Officer')->get(['id', 'name']);

        return view('paraguins.closers.edit', compact('lead', 'validators'));
    }

    /**
     * Update lead with closer's information
     */
    public function closerUpdate(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('managed_by', Auth::id())
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->findOrFail($id);

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
            'assigned_validator_id' => ['required', 'exists:users,id'],
        ]);

        // Update lead with closer's information and mark as closed (sent to validator)
        $validated['status'] = 'closed';
        $lead->update($validated);

        return redirect()->route('paraguins.closers.index')
            ->with('success', 'Lead submitted successfully and sent to validator.');
    }

    /**
     * Mark lead as failed with reason
     */
    public function closerMarkFailed(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('managed_by', Auth::id())
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->findOrFail($id);

        $validated = $request->validate([
            'failure_reason' => ['required', 'in:Failed:POA,Failed:DNQ-Age,Failed:Declined SSN,Failed:Not Interested,Failed:DNC,Failed:Cannot Afford,Failed:DNQ-Health,Failed:Declined Banking'],
        ]);

        $lead->update([
            'status' => 'rejected',
            'failure_reason' => $validated['failure_reason'],
        ]);

        return redirect()->route('paraguins.closers.index')
            ->with('success', 'Lead marked as ' . $validated['failure_reason'] . '.');
    }

    /**
     * Save partial data and mark as pending (callback requested)
     */
    public function closerMarkPending(Request $request, $id)
    {
        $lead = Lead::where('team', 'paraguins')
            ->where('managed_by', Auth::id())
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->findOrFail($id);

        $validated = $request->validate([
            'pending_reason' => ['required', 'in:Pending:Future Potential,Pending:Callback,Pending:Pending Banking,Pending:Pending Validation'],
        ]);

        // Save all form data without strict validation (partial data allowed)
        $fillableFields = [
            'cn_name', 'phone_number', 'date_of_birth', 'gender', 'ssn', 'address', 'state', 'zip_code',
            'birth_place', 'height_weight', 'smoker', 'doctor_name', 'medical_issue',
            'medications', 'carrier_name', 'policy_type', 'initial_draft_date',
            'coverage_amount', 'monthly_premium', 'source', 'beneficiary', 'beneficiary_dob',
            'bank_name', 'account_type', 'account_number', 'routing_number', 'bank_balance',
            'card_number', 'cvv', 'expiry_date', 'assigned_validator_id'
        ];
        
        $data = [];
        foreach ($fillableFields as $field) {
            if ($request->has($field) && $request->input($field) !== null && $request->input($field) !== '') {
                $data[$field] = $request->input($field);
            }
        }
        
        $data['status'] = 'pending';
        $data['pending_reason'] = $validated['pending_reason'];
        
        $lead->update($data);

        return redirect()->route('paraguins.closers.index')
            ->with('success', 'Lead marked as ' . $validated['pending_reason'] . '. All entered data saved.');
    }
}
