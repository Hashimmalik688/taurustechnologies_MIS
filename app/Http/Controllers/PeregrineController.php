<?php

namespace App\Http\Controllers;

use App\Models\AgentCarrierState;
use App\Models\Lead;
use App\Models\User;
use App\Support\Roles;
use App\Support\Statuses;
use App\Support\Teams;
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

        // Get date range based on office hours (7am-5pm PT)
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);
        
        // Get validators for dropdown (only Managers and Peregrine Validators)
        $validators = User::role([Roles::PEREGRINE_VALIDATOR, Roles::MANAGER])
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Get pending/transferred leads assigned to this closer (including returned from validator)
        // Apply date filter only if NOT showing all pending
        $pendingQuery = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', $userId)
            ->whereIn('status', [Statuses::LEAD_PENDING, Statuses::LEAD_TRANSFERRED, Statuses::LEAD_RETURNED])
            ->with(['assignedValidator']);
        
        if (!$showAllPending) {
            $pendingQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }
        
        $pendingLeads = $pendingQuery->orderBy('created_at', 'desc')->get();

        // Get completed/sent leads (closed, sale, or forwarded) - filtered by date
        $completedLeads = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', $userId)
            ->whereIn('status', [Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_FORWARDED])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['assignedValidator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get failed leads (includes both closer failures and validator declines) - filtered by date
        $failedLeads = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', $userId)
            ->where('status', Statuses::LEAD_DECLINED)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate filtered total for conversion rate
        $filteredTotal = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', $userId)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        // Daily stats within the selected date range
        $todayStats = $this->getDailyStats($userId, $startDate, $endDate);

        $carrierPartnerData = $this->buildCarrierPartnerData();

        return view('peregrine.closers.index', compact(
            'pendingLeads', 
            'completedLeads', 
            'failedLeads', 
            'validators', 
            'todayStats', 
            'filter', 
            'startDate', 
            'endDate',
            'filteredTotal',
            'carrierPartnerData'
        ));
    }

    /**
     * Show form for closer to complete the lead
     */
    public function closerEdit($id)
    {
        $lead = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', Auth::id())
            ->findOrFail($id);

        // Get validators for dropdown (only Managers and Peregrine Validators)
        $validators = User::role([Roles::PEREGRINE_VALIDATOR, Roles::MANAGER])
            ->orderBy('name')
            ->get(['id', 'name']);

        $carrierPartnerData = $this->buildCarrierPartnerData();

        return view('peregrine.closers.edit', compact('lead', 'validators', 'carrierPartnerData'));
    }

    /**
     * Update lead with closer's information
     */
    public function closerUpdate(Request $request, $id)
    {
        $lead = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', Auth::id())
            ->whereIn('status', [Statuses::LEAD_PENDING, Statuses::LEAD_TRANSFERRED, Statuses::LEAD_RETURNED])
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
            'height' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'smoker' => ['nullable', 'boolean'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_number' => ['nullable', 'string', 'max:50'],
            'doctor_address' => ['nullable', 'string', 'max:500'],
            'medical_issue' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'carrier_name' => ['nullable', 'string', 'max:255'],
            'insurance_carrier_id' => ['nullable', 'exists:insurance_carriers,id'],
            'policy_type' => ['required', 'string', 'max:255'],
            'beneficiaries.*.name' => ['required', 'string', 'max:255'],
            'beneficiaries.*.dob' => ['required', 'date'],
            'beneficiaries.*.relation' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_address' => ['required', 'string', 'max:500'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'account_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:50'],
            'routing_number' => ['required_unless:account_type,Card', 'nullable', 'string', 'max:20'],
            'bank_balance' => ['nullable', 'numeric', 'min:0'],
            'card_number' => ['nullable', 'required_if:account_type,Card', 'string', 'max:19'],
            'cvv' => ['nullable', 'required_if:account_type,Card', 'string', 'max:4'],
            'expiry_date' => ['nullable', 'required_if:account_type,Card', 'string', 'max:50'],
            'assigned_partner' => ['required', 'string', 'max:255'],
            'partner_id' => ['nullable', 'exists:partners,id'],
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
        $validated['status'] = Statuses::LEAD_CLOSED;
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
        $lead = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', Auth::id())
            ->whereIn('status', [Statuses::LEAD_PENDING, Statuses::LEAD_TRANSFERRED, Statuses::LEAD_RETURNED])
            ->findOrFail($id);

        $validated = $request->validate([
            'failure_reason' => ['required', 'in:Failed:POA,Failed:DNQ-Age,Failed:Declined SSN,Failed:Not Interested,Failed:DNC,Failed:Cannot Afford,Failed:DNQ-Health,Failed:Declined Banking,Failed:No Pitch (Not Interested),Failed:No Answer'],
            'disposition_comment' => ['required', 'string', 'max:1000'],
        ]);

        $lead->update([
            'status' => Statuses::LEAD_DECLINED,
            'decline_reason' => $validated['failure_reason'],
            'declined_at' => now(),
            'comments' => $validated['disposition_comment'],
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
        $lead = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', Auth::id())
            ->whereIn('status', [Statuses::LEAD_PENDING, Statuses::LEAD_TRANSFERRED, Statuses::LEAD_RETURNED])
            ->findOrFail($id);

        $validated = $request->validate([
            'pending_reason' => ['required', 'in:Pending:Future Potential,Pending:Callback,Pending:Pending Banking,Pending:Pending Validation'],
            'disposition_comment' => ['required', 'string', 'max:1000'],
        ]);

        // Save all form data without strict validation (partial data allowed)
        $fillableFields = [
            'cn_name', 'phone_number', 'date_of_birth', 'gender', 'ssn', 'address', 'state', 'zip_code',
            'birth_place', 'height_weight', 'height', 'weight', 'smoker', 'doctor_name', 'doctor_number', 'doctor_address',
            'medical_issue', 'medications', 'carrier_name', 'insurance_carrier_id', 'policy_type', 'initial_draft_date', 'future_draft_date',
            'coverage_amount', 'monthly_premium', 'source', 'beneficiary', 'beneficiary_dob',
            'bank_name', 'bank_address', 'account_type', 'account_number', 'routing_number', 'bank_balance',
            'card_number', 'cvv', 'expiry_date', 'assigned_validator_id', 'assigned_partner', 'partner_id'
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
        
        $data['status'] = Statuses::LEAD_PENDING;
        $data['pending_reason'] = $validated['pending_reason'];
        $data['comments'] = $validated['disposition_comment'];
        
        $lead->update($data);

        return redirect()->route('peregrine.closers.index')
            ->with('success', 'Lead marked as ' . $validated['pending_reason'] . '. All entered data saved.');
    }

    /**
     * Store a manually-created lead, bypassing PJC and going straight to validator.
     * Creates the lead as status=closed so it immediately appears in the validator queue.
     */
    public function manualStore(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'cn_name'               => ['required', 'string', 'max:255'],
            'phone_number'          => ['required', 'string', 'max:30'],
            'date_of_birth'         => ['required', 'date'],
            'gender'                => ['required', 'in:Male,Female,Other'],
            'ssn'                   => ['required', 'string', 'max:20'],
            'address'               => ['required', 'string'],
            'state'                 => ['required', 'string', 'max:50'],
            'zip_code'              => ['required', 'string', 'max:10'],
            'birth_place'           => ['nullable', 'string', 'max:255'],
            'height'                => ['nullable', 'string', 'max:50'],
            'weight'                => ['nullable', 'string', 'max:50'],
            'smoker'                => ['nullable', 'boolean'],
            'doctor_name'           => ['required', 'string', 'max:255'],
            'doctor_number'         => ['required', 'string', 'max:50'],
            'doctor_address'        => ['required', 'string', 'max:500'],
            'medical_issue'         => ['required', 'string'],
            'medications'           => ['required', 'string', 'max:1000'],
            'carrier_name'          => ['nullable', 'string', 'max:255'],
            'insurance_carrier_id'  => ['nullable', 'exists:insurance_carriers,id'],
            'policy_type'           => ['required', 'string', 'max:255'],
            'initial_draft_date'    => ['required', 'date'],
            'future_draft_date'     => ['required', 'date'],
            'coverage_amount'       => ['required', 'numeric', 'min:0'],
            'monthly_premium'       => ['required', 'numeric', 'min:0'],
            'source'                => ['nullable', 'string', 'max:255'],
            'assigned_partner'      => ['nullable', 'string', 'max:255'],
            'partner_id'            => ['nullable', 'exists:partners,id'],
            'beneficiaries'         => ['required', 'array', 'min:1'],
            'beneficiaries.*.name'  => ['required', 'string', 'max:255'],
            'beneficiaries.*.dob'   => ['required', 'date'],
            'beneficiaries.*.relation' => ['nullable', 'string', 'max:50'],
            'bank_name'             => ['required', 'string', 'max:255'],
            'bank_address'          => ['required', 'string', 'max:500'],
            'account_type'          => ['required', 'in:Checking,Savings,Card'],
            'account_number'        => ['required_unless:account_type,Card', 'nullable', 'string', 'max:50'],
            'routing_number'        => ['required_unless:account_type,Card', 'nullable', 'string', 'max:20'],
            'bank_balance'          => ['nullable', 'numeric', 'min:0'],
            'card_number'           => ['nullable', 'string', 'max:19'],
            'cvv'                   => ['nullable', 'string', 'max:4'],
            'expiry_date'           => ['nullable', 'string', 'max:50'],
            'assigned_validator_id' => ['required', 'exists:users,id'],
            'followup_required'     => ['required', 'boolean'],
            'followup_scheduled_at' => ['required_if:followup_required,1', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('peregrine.closers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('openManualModal', true);
        }

        $validated = $validator->validated();

        // Same business rule as closerUpdate: must confirm follow-up
        if ($validated['followup_required'] == 0) {
            return redirect()->route('peregrine.closers.index')
                ->withErrors(['followup_required' => 'You must select "Yes" for Follow Up to submit this lead to the validator.'])
                ->withInput()
                ->with('openManualModal', true);
        }

        $firstBeneficiary = $validated['beneficiaries'][0] ?? null;

        Lead::create([
            'date'                  => now()->format('Y-m-d'),
            'cn_name'               => $validated['cn_name'],
            'phone_number'          => $validated['phone_number'],
            'date_of_birth'         => $validated['date_of_birth'],
            'gender'                => $validated['gender'],
            'ssn'                   => $validated['ssn'],
            'address'               => $validated['address'],
            'state'                 => $validated['state'],
            'zip_code'              => $validated['zip_code'],
            'birth_place'           => $validated['birth_place'] ?? null,
            'height'                => $validated['height'] ?? null,
            'weight'                => $validated['weight'] ?? null,
            'smoker'                => isset($validated['smoker']) ? ($validated['smoker'] ? 'yes' : 'no') : null,
            'medical_issue'         => $validated['medical_issue'] ?? null,
            'medications'           => $validated['medications'] ?? null,
            'doctor_name'           => $validated['doctor_name'] ?? null,
            'doctor_number'         => $validated['doctor_number'] ?? null,
            'doctor_address'        => $validated['doctor_address'] ?? null,
            'carrier_name'          => $validated['carrier_name'] ?? null,
            'insurance_carrier_id'  => $validated['insurance_carrier_id'] ?? null,
            'policy_type'           => $validated['policy_type'],
            'initial_draft_date'    => $validated['initial_draft_date'],
            'future_draft_date'     => $validated['future_draft_date'],
            'coverage_amount'       => $validated['coverage_amount'],
            'monthly_premium'       => $validated['monthly_premium'],
            'source'                => $validated['source'] ?? null,
            'assigned_partner'      => $validated['assigned_partner'] ?? null,
            'partner_id'            => $validated['partner_id'] ?? null,
            'bank_name'             => $validated['bank_name'],
            'bank_address'          => $validated['bank_address'],
            'account_type'          => $validated['account_type'],
            'account_number'        => $validated['account_number'] ?? null,
            'acc_number'            => $validated['account_number'] ?? null,
            'routing_number'        => $validated['routing_number'] ?? null,
            'bank_balance'          => $validated['bank_balance'] ?? null,
            'card_number'           => $validated['card_number'] ?? null,
            'cvv'                   => $validated['cvv'] ?? null,
            'expiry_date'           => $validated['expiry_date'] ?? null,
            'assigned_validator_id' => $validated['assigned_validator_id'],
            'followup_required'     => $validated['followup_required'],
            'followup_scheduled_at' => $validated['followup_scheduled_at'] ?? null,
            'beneficiaries'         => $validated['beneficiaries'],
            'beneficiary'           => $firstBeneficiary['name'] ?? null,
            'beneficiary_dob'       => $firstBeneficiary['dob'] ?? null,
            // Pipeline identifiers
            'team'                  => Teams::PEREGRINE,
            'status'                => Statuses::LEAD_CLOSED,
            'managed_by'            => Auth::id(),
            'closer_id'             => Auth::id(),
            'closer_name'           => Auth::user()->name,
            'source_type'           => Teams::PEREGRINE,
            'closed_at'             => now(),
        ]);

        return redirect()->route('peregrine.closers.index')
            ->with('success', 'Lead created and sent to validator successfully.');
    }

    /**
     * Build the carrier → partner → states data array (same structure as Ravens dashboard).
     */
    private function buildCarrierPartnerData(): array
    {
        return AgentCarrierState::with(['insuranceCarrier', 'partner'])
            ->whereHas('insuranceCarrier', fn($q) => $q->where('is_active', true))
            ->whereHas('partner',          fn($q) => $q->where('is_active', true))
            ->get()
            ->groupBy(fn($item) => $item->insurance_carrier_id . '_' . $item->partner_id)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'carrier_id'   => $first->insurance_carrier_id,
                    'carrier_name' => $first->insuranceCarrier->name,
                    'partner_id'   => $first->partner_id,
                    'partner_name' => $first->partner->name,
                    'partner_code' => $first->partner->code,
                    'display_name' => $first->insuranceCarrier->name . ' (' . $first->partner->code . ')',
                    'states'       => $group->pluck('state')->unique()->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get daily stats for closer
     */
    private function getDailyStats($closerId, $startDate, $endDate)
    {
        $leads = Lead::where('team', Teams::PEREGRINE)
            ->where('managed_by', $closerId)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();

        return [
            'total_assigned' => $leads->count(),
            'with_closer' => $leads->whereIn('status', [Statuses::LEAD_TRANSFERRED, Statuses::LEAD_PENDING, Statuses::LEAD_RETURNED])->count(),
            'sent_to_validator' => $leads->where('status', Statuses::LEAD_CLOSED)->count(),
            'sales' => $leads->whereIn('status', [Statuses::LEAD_SALE, Statuses::LEAD_ACCEPTED])->count(),
            'declined' => $leads->where('status', Statuses::LEAD_DECLINED)->count(),
        ];
    }

    /**
     * Helper method to get date range based on filter
     * Office hours: 8pm PKT to 6am PKT = 7am PT to 5pm PT
     * Extended to end of day to capture all submissions within office hours
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        $timezone = 'America/Los_Angeles';
        
        switch ($filter) {
            case 'today':
                $start = Carbon::today($timezone)->setTime(7, 0, 0);
                $end = Carbon::today($timezone)->setTime(23, 59, 59);
                return [$start, $end];
            
            case 'yesterday':
                $start = Carbon::yesterday($timezone)->setTime(7, 0, 0);
                $end = Carbon::yesterday($timezone)->setTime(23, 59, 59);
                return [$start, $end];
            
            case 'week':
                $start = Carbon::now($timezone)->startOfWeek()->setTime(7, 0, 0);
                $end = Carbon::today($timezone)->setTime(23, 59, 59);
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        $start = Carbon::parse($customStart, $timezone)->setTime(7, 0, 0);
                        $end = Carbon::parse($customEnd, $timezone)->setTime(23, 59, 59);
                        return [$start, $end];
                    } catch (\Exception $e) {
                        $start = Carbon::today($timezone)->setTime(7, 0, 0);
                        $end = Carbon::today($timezone)->setTime(23, 59, 59);
                        return [$start, $end];
                    }
                }
                $start = Carbon::today($timezone)->setTime(7, 0, 0);
                $end = Carbon::today($timezone)->setTime(23, 59, 59);
                return [$start, $end];
            
            default:
                $start = Carbon::today($timezone)->setTime(7, 0, 0);
                $end = Carbon::today($timezone)->setTime(23, 59, 59);
                return [$start, $end];
        }
    }
}
