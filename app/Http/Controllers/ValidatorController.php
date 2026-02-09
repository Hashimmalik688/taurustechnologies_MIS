<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ValidatorController extends Controller
{
    /**
     * Show validator dashboard with closed leads to review
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'today'); // Default to 'today'
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');
        $showAllPending = $request->get('show_all_pending', false);

        // Get date range based on office hours (7am-5pm MT)
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        // Get leads assigned to this validator that need validation
        // Apply date filter only if NOT showing all pending
        $pendingQuery = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->with(['assignedValidator', 'assignedCloser']);
        
        if (!$showAllPending) {
            $pendingQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        
        $pendingLeads = $pendingQuery->orderBy('closed_at', 'desc')->get();
            
        // Get leads sent to home office
        $homeOfficeLeads = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'pending')
            ->where('pending_reason', 'Pending:Sent to Home Office')
            ->with(['assignedCloser', 'verifier', 'assignedValidator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get completed leads (marked as sale, declined, forwarded, or returned by this validator) - filtered by date
        $completedLeads = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['sale', 'declined', 'forwarded', 'returned'])
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->with(['validator', 'assignedValidator'])
            ->orderBy('validated_at', 'desc')
            ->get();

        // Calculate stats - based on validator performance within date range
        $allLeads = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'sale', 'declined', 'returned'])
            ->where(function($query) use ($startDate, $endDate) {
                // Pending leads use closed_at (when assigned to validator)
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->where('status', 'closed')
                      ->whereBetween('closed_at', [$startDate, $endDate]);
                })
                // Completed leads use validated_at (when validator processed them)
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['sale', 'declined', 'returned'])
                      ->whereBetween('validated_at', [$startDate, $endDate]);
                });
            })
            ->get();

        $salesLeads = $allLeads->where('status', 'sale');
        $declinedLeads = $allLeads->where('status', 'declined');
        $returnedLeads = $allLeads->where('status', 'returned');
        
        // Submitted to Sales Management (has sale_at timestamp) within date range
        $submittedLeads = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->whereNotNull('sale_at')
            ->whereBetween('sale_at', [$startDate, $endDate])
            ->get();

        // Calculate filtered total
        $filteredTotal = $allLeads->count();

        // Daily stats
        $todayStats = $this->getDailyStats(Auth::id(), $startDate, $endDate);

        return view('validator.index', compact(
            'pendingLeads', 
            'homeOfficeLeads', 
            'completedLeads', 
            'allLeads', 
            'salesLeads', 
            'declinedLeads', 
            'returnedLeads', 
            'submittedLeads', 
            'todayStats', 
            'filter', 
            'startDate', 
            'endDate',
            'filteredTotal'
        ));
    }

    /**
     * Mark lead as sale
     */
    public function markAsSale($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $lead->update([
            'status' => 'sale',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'sale_at' => now(),
            'sale_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }

    /**
     * Mark lead as forwarded (sent to home office)
     */
    public function markAsForwarded($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $lead->update([
            'status' => 'pending',
            'pending_reason' => 'Pending:Sent to Home Office',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
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
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        // Get all form data that was edited
        $updateData = $request->except(['_token', '_method']);
        
        // Update lead with any changes made by validator
        $updateData['status'] = 'returned';
        $updateData['assigned_validator_id'] = null;
        $updateData['returned_at'] = now();
        $updateData['validated_at'] = now();
        
        $lead->update($updateData);

        return redirect()->route('validator.index')
            ->with('success', 'Lead returned to closer for more information.');
    }

    /**
     * Show edit form for validator
     */
    public function edit($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        $validators = User::role(['Verification Officer', 'Verifier', 'Peregrine Validator', 'Manager'])->get(['id', 'name']);

        return view('validator.edit', compact('lead', 'validators'));
    }

    /**
     * Update lead from validator and mark as sale
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::where('team', 'peregrine')
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
            'assigned_partner' => ['nullable', 'string', 'max:255'],
            // Multiple beneficiaries support
            'beneficiaries' => ['required', 'array', 'min:1'],
            'beneficiaries.*.name' => ['required', 'string', 'max:255'],
            'beneficiaries.*.dob' => ['nullable', 'date'],
            'beneficiaries.*.relation' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'account_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:50'],
            'routing_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:20'],
            'bank_balance' => ['nullable', 'numeric', 'min:0'],
            'card_number' => ['nullable', 'required_if:account_type,Card', 'string', 'max:19'],
            'cvv' => ['nullable', 'required_if:account_type,Card', 'string', 'max:4'],
            'expiry_date' => ['nullable', 'required_if:account_type,Card', 'string', 'max:50'],
        ]);

        // Maintain backward compatibility: store first beneficiary in old fields
        if (!empty($validated['beneficiaries'][0])) {
            $validated['beneficiary'] = $validated['beneficiaries'][0]['name'];
            $validated['beneficiary_dob'] = $validated['beneficiaries'][0]['dob'] ?? null;
        }

        // Update lead and mark as sale
        $validated['status'] = 'sale';
        $validated['validated_by'] = Auth::id();
        $validated['validated_at'] = now();
        $validated['sale_at'] = now();
        $validated['sale_date'] = now()->format('Y-m-d');
        
        $lead->update($validated);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }

    /**
     * Mark lead as failed with reason
     */
    public function markAsFailed(Request $request, $id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'pending'])
            ->findOrFail($id);

        $validated = $request->validate([
            'decline_reason' => ['required', 'in:Declined:POA,Declined:DNQ-Age,Declined:Declined SSN,Declined:Not Interested,Declined:DNC,Declined:Cannot Afford,Declined:DNQ-Health,Declined:Declined Banking,Declined:No Pitch (Not Interested),Declined:No Answer'],
        ]);

        $lead->update([
            'status' => 'declined',
            'decline_reason' => $validated['decline_reason'],
            'validated_by' => Auth::id(),
            'declined_at' => now(),
            'validated_at' => now(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as ' . $validated['decline_reason'] . '.');
    }

    /**
     * Mark lead as declined without specific reason (simple decline)
     */
    public function markAsSimpleDeclined($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->whereIn('status', ['closed', 'pending'])
            ->findOrFail($id);

        $lead->update([
            'status' => 'declined',
            'decline_reason' => 'Declined',
            'validated_by' => Auth::id(),
            'declined_at' => now(),
            'validated_at' => now(),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Declined.');
    }

    /**
     * Mark home office lead as sale (simplified for home office)
     */
    public function markHomeOfficeSale($id)
    {
        $lead = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', Auth::id())
            ->where('status', 'pending')
            ->where('pending_reason', 'Pending:Sent to Home Office')
            ->findOrFail($id);

        $lead->update([
            'status' => 'sale',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'sale_at' => now(),
            'sale_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('validator.index')
            ->with('success', 'Lead marked as Sale successfully.');
    }

    /**
     * Get daily stats for validator
     */
    private function getDailyStats($validatorId, $startDate, $endDate)
    {
        // Leads submitted to validator (closed) within date range
        $submitted = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', $validatorId)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$startDate, $endDate])
            ->count();

        // Sales approved by this validator (uses validated_at)
        $sales = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', $validatorId)
            ->where('status', 'sale')
            ->whereNotNull('validated_at')
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->count();

        // Declined by this validator (uses validated_at)
        $declined = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', $validatorId)
            ->where('status', 'declined')
            ->whereNotNull('validated_at')
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->count();

        // Returned by this validator (uses validated_at)
        $returned = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', $validatorId)
            ->where('status', 'returned')
            ->whereNotNull('validated_at')
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->count();

        // Pending validation (status = closed) within date range
        $pending = Lead::where('team', 'peregrine')
            ->where('assigned_validator_id', $validatorId)
            ->where('status', 'closed')
            ->whereBetween('closed_at', [$startDate, $endDate])
            ->count();

        return [
            'total_processed' => $sales + $declined + $returned,
            'submitted' => $submitted,
            'sales' => $sales,
            'declined' => $declined,
            'returned' => $returned,
            'pending' => $pending,
        ];
    }

    /**
     * Helper method to get date range based on filter
     * Office hours: 7pm PKT to 5am PKT = 7am MT to 5pm MT
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        $timezone = 'America/Denver';
        
        switch ($filter) {
            case 'today':
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'yesterday':
                $start = Carbon::yesterday($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::yesterday($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'week':
                $start = Carbon::now($timezone)->startOfWeek()->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        $start = Carbon::parse($customStart, $timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::parse($customEnd, $timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    } catch (\Exception $e) {
                        $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    }
                }
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            default:
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
        }
    }
}
