<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeregrineController extends Controller
{
    /**
     * Show list of leads for Peregrine live closers
     */
    public function closersIndex(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->get('filter', 'today'); // Default to 'today'
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');
        $showAllPending = $request->get('show_all_pending', false);

        // Get date range based on office hours (7am-5pm MT)
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);
        
        // Get validators for dropdown (only Managers and Peregrine Validators)
        $validators = User::role(['Peregrine Validator', 'Manager'])
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Get pending/transferred leads assigned to this closer (including returned from validator)
        // Apply date filter only if NOT showing all pending
        $pendingQuery = Lead::where('team', 'peregrine')
            ->where('managed_by', $userId)
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->with(['assignedValidator']);
        
        if (!$showAllPending) {
            $pendingQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }
        
        $pendingLeads = $pendingQuery->orderBy('created_at', 'desc')->get();

        // Get completed/sent leads (closed, sale, or forwarded) - filtered by date
        $completedLeads = Lead::where('team', 'peregrine')
            ->where('managed_by', $userId)
            ->whereIn('status', ['closed', 'sale', 'forwarded'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['assignedValidator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get failed leads (includes both closer failures and validator declines) - filtered by date
        $failedLeads = Lead::where('team', 'peregrine')
            ->where('managed_by', $userId)
            ->where('status', 'declined')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate filtered total for conversion rate
        $filteredTotal = Lead::where('team', 'peregrine')
            ->where('managed_by', $userId)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        // Daily stats within the selected date range
        $todayStats = $this->getDailyStats($userId, $startDate, $endDate);

        return view('peregrine.closers.index', compact(
            'pendingLeads', 
            'completedLeads', 
            'failedLeads', 
            'validators', 
            'todayStats', 
            'filter', 
            'startDate', 
            'endDate',
            'filteredTotal'
        ));
    }

    /**
     * Show form for closer to complete the lead
     */
    public function closerEdit($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('managed_by', Auth::id())
            ->findOrFail($id);

        // Get validators for dropdown (only Managers and Peregrine Validators)
        $validators = User::role(['Peregrine Validator', 'Manager'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('peregrine.closers.edit', compact('lead', 'validators'));
    }

    /**
     * Update lead with closer's information
     */
    public function closerUpdate(Request $request, $id)
    {
        $lead = Lead::where('team', 'peregrine')
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
            'doctor_number' => ['nullable', 'string', 'max:50'],
            'doctor_address' => ['nullable', 'string', 'max:500'],
            'medical_issue' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'carrier_name' => ['nullable', 'string', 'max:255'],
            'policy_type' => ['required', 'string', 'max:255'],
            'initial_draft_date' => ['required', 'date'],
            'coverage_amount' => ['required', 'numeric', 'min:0'],
            'monthly_premium' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:255'],
            // Multiple beneficiaries support
            'beneficiaries' => ['required', 'array', 'min:1'],
            'beneficiaries.*.name' => ['required', 'string', 'max:255'],
            'beneficiaries.*.dob' => ['required', 'date'],
            'beneficiaries.*.relation' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'account_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:50'],
            'routing_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:20'],
            'bank_balance' => ['nullable', 'numeric', 'min:0'],
            'card_number' => ['nullable', 'required_if:account_type,Card', 'string', 'max:19'],
            'cvv' => ['nullable', 'required_if:account_type,Card', 'string', 'max:4'],
            'expiry_date' => ['nullable', 'required_if:account_type,Card', 'string', 'max:50'],
            'assigned_partner' => ['required', 'string', 'max:255'],
            'assigned_validator_id' => ['required', 'exists:users,id'],
            // Follow up schedule fields
            'followup_required' => ['required', 'boolean'],
            'followup_scheduled_at' => ['required_if:followup_required,1', 'nullable', 'date'],
        ]);

        // Check if followup is required - cannot submit if followup_required is "No"
        if ($validated['followup_required'] == 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['followup_required' => 'You must select "Yes" for Follow Up to submit this lead to the validator.']);
        }

        // Update lead with closer's information and mark as closed (sent to validator)
        $validated['status'] = 'closed';
        $validated['closed_at'] = now();
        
        // Maintain backward compatibility: store first beneficiary in old fields
        if (!empty($validated['beneficiaries'][0])) {
            $validated['beneficiary'] = $validated['beneficiaries'][0]['name'];
            $validated['beneficiary_dob'] = $validated['beneficiaries'][0]['dob'] ?? null;
            // Note: relation is only stored in the beneficiaries JSON field
        }
        
        $lead->update($validated);

        return redirect()->route('peregrine.closers.index')
            ->with('success', 'Lead submitted successfully and sent to validator.');
    }

    /**
     * Mark lead as failed with reason
     */
    public function closerMarkFailed(Request $request, $id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('managed_by', Auth::id())
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->findOrFail($id);

        $validated = $request->validate([
            'failure_reason' => ['required', 'in:Failed:POA,Failed:DNQ-Age,Failed:Declined SSN,Failed:Not Interested,Failed:DNC,Failed:Cannot Afford,Failed:DNQ-Health,Failed:Declined Banking,Failed:No Pitch (Not Interested),Failed:No Answer'],
        ]);

        $lead->update([
            'status' => 'declined',
            'decline_reason' => $validated['failure_reason'],
            'declined_at' => now(),
        ]);

        // CRITICAL: Update status on all linked users (Validator and Manager)
        // This ensures the validator and manager are notified of the failure
        if ($lead->assigned_validator_id) {
            \Log::info('Peregrine Closer marked lead as failed', [
                'lead_id' => $lead->id,
                'closer_id' => Auth::id(),
                'validator_id' => $lead->assigned_validator_id,
                'failure_reason' => $validated['failure_reason'],
                'timestamp' => now()
            ]);
        }

        return redirect()->route('peregrine.closers.index')
            ->with('success', 'Lead marked as ' . $validated['failure_reason'] . '. Linked users have been notified.');
    }

    /**
     * Save partial data and mark as pending (callback requested)
     */
    public function closerMarkPending(Request $request, $id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('managed_by', Auth::id())
            ->whereIn('status', ['pending', 'transferred', 'returned'])
            ->findOrFail($id);

        $validated = $request->validate([
            'pending_reason' => ['required', 'in:Pending:Future Potential,Pending:Callback,Pending:Pending Banking,Pending:Pending Validation'],
        ]);

        // Save all form data without strict validation (partial data allowed)
        $fillableFields = [
            'cn_name', 'phone_number', 'date_of_birth', 'gender', 'ssn', 'address', 'state', 'zip_code',
            'birth_place', 'height_weight', 'smoker', 'doctor_name', 'doctor_number', 'doctor_address',
            'medical_issue', 'medications', 'carrier_name', 'policy_type', 'initial_draft_date',
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
        
        // Handle beneficiaries array
        if ($request->has('beneficiaries')) {
            $data['beneficiaries'] = $request->input('beneficiaries');
            // Maintain backward compatibility
            if (!empty($data['beneficiaries'][0])) {
                $data['beneficiary'] = $data['beneficiaries'][0]['name'];
                $data['beneficiary_dob'] = $data['beneficiaries'][0]['dob'] ?? null;
            }
        }
        
        $data['status'] = 'pending';
        $data['pending_reason'] = $validated['pending_reason'];
        
        $lead->update($data);

        return redirect()->route('peregrine.closers.index')
            ->with('success', 'Lead marked as ' . $validated['pending_reason'] . '. All entered data saved.');
    }

    /**
     * Get daily stats for closer
     */
    private function getDailyStats($closerId, $startDate, $endDate)
    {
        $leads = Lead::where('team', 'peregrine')
            ->where('managed_by', $closerId)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();

        return [
            'total_assigned' => $leads->count(),
            'transferred' => $leads->where('status', 'transferred')->count(),
            'closed' => $leads->where('status', 'closed')->count(),
            'sales' => $leads->where('status', 'sale')->count(),
            'returned' => $leads->where('status', 'returned')->count(),
            'declined' => $leads->where('status', 'declined')->count(),
        ];
    }

    /**
     * Helper method to get date range based on filter
     * Office hours: 7pm PKT to 5am PKT = 7am MT to 5pm MT
     * Extended to end of day to capture all submissions within office hours
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        $timezone = 'America/Denver';
        
        switch ($filter) {
            case 'today':
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                return [$start, $end];
            
            case 'yesterday':
                $start = Carbon::yesterday($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::yesterday($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                return [$start, $end];
            
            case 'week':
                $start = Carbon::now($timezone)->startOfWeek()->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        $start = Carbon::parse($customStart, $timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::parse($customEnd, $timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                        return [$start, $end];
                    } catch (\Exception $e) {
                        $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::today($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                        return [$start, $end];
                    }
                }
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                return [$start, $end];
            
            default:
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(23, 59, 59)->setTimezone('UTC');
                return [$start, $end];
        }
    }
}
