<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadDial;
use App\Models\BadLead;
use App\Models\CallLog;
use App\Services\NotificationService;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\Roles;
use App\Support\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RavensDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'today');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $search = $request->input('search');

        // Determine date range
        $timezone = 'America/Denver';
        if ($filter === 'custom' && $customStart && $customEnd) {
            try {
                $startDate = \Carbon\Carbon::parse($customStart, $timezone)->startOfDay();
                $endDate = \Carbon\Carbon::parse($customEnd, $timezone)->endOfDay();
            } catch (\Exception $e) {
                $startDate = \Carbon\Carbon::today($timezone)->startOfDay();
                $endDate = \Carbon\Carbon::today($timezone)->endOfDay();
            }
        } else {
            $startDate = \Carbon\Carbon::today($timezone)->startOfDay();
            $endDate = \Carbon\Carbon::today($timezone)->endOfDay();
        }

        // Get stats for the Ravens employee (filtered by date range)
        $stats = [
            'dialed' => $this->getDialedCount($user->id, $startDate, $endDate),
            'calls_connected' => $this->getCallsConnectedFiltered($user->id, $startDate, $endDate),
            'sales' => $this->getSalesCount($user->id, $startDate, $endDate),
            'mtd_sales' => $this->getMTDSalesCount($user->id),
        ];

        // Get sales made by this closer for the Ravens dashboard (filtered)
        $mySalesQuery = Lead::where(function($q) use ($user) {
                $q->where('closer_name', $user->name)
                  ->orWhere('closer_id', $user->id);
            })
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where('team', Teams::RAVENS);

        // Apply date filter to sales
        $mySalesQuery->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('sale_at', [$startDate, $endDate])
              ->orWhereBetween('sale_date', [$startDate, $endDate]);
        });

        // Apply search filter
        if ($search) {
            $mySalesQuery->where(function($q) use ($search) {
                $q->where('cn_name', 'like', '%'.$search.'%')
                  ->orWhere('phone_number', 'like', '%'.$search.'%')
                  ->orWhere('carrier_name', 'like', '%'.$search.'%');
            });
        }

        $mySales = $mySalesQuery->orderByRaw('COALESCE(sale_at, sale_date, created_at) DESC')
            ->paginate(10)
            ->appends($request->query());

        // Get declined/chargeback leads for this closer
        $declinedChargebacks = Lead::where(function($q) use ($user) {
                $q->where('closer_name', $user->name)
                  ->orWhere('closer_id', $user->id);
            })
            ->where('team', Teams::RAVENS)
            ->whereIn('status', ['declined', 'chargeback'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('updated_at', [$startDate, $endDate])
                  ->orWhereBetween('sale_at', [$startDate, $endDate])
                  ->orWhereBetween('sale_date', [$startDate, $endDate]);
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('ravens.dashboard', compact('stats', 'mySales', 'declinedChargebacks', 'filter', 'search'));
    }

    public function calling(Request $request)
    {
        // Get all leads for Ravens employees to call
        // Exclude leads that have been marked as sold (status = 'accepted' and sale_at is not null)
        // UNLESS the current user is the one who closed it
        // Also exclude leads submitted by Peregrine closers (team = Teams::PEREGRINE)
        $currentUser = Auth::user();
        $search = $request->input('search');
        
        $leads = Lead::select([
            'id', 'cn_name', 'phone_number', 'secondary_phone_number', 
            'closer_name', 'team', 'assigned_partner', 'created_at', 
            'status', 'sale_at', 'verified_by', 'callback_note', 'callback_note_updated_at'
        ])
        // Exclude disposed (bad) leads — they should never appear in calling system
        ->where('status', '!=', 'disposed')
        ->where(function($query) use ($currentUser) {
            // Include leads that are not sold yet
            $query->where(function($q) {
                $q->whereNull('sale_at')
                  ->orWhere('status', '!=', 'accepted');
            })
            // OR include leads sold by current user (so they can see their own sales)
            ->orWhere('closer_name', $currentUser->name);
        })
        // Exclude Peregrine team leads
        ->where(function($query) {
            $query->where('team', '!=', Teams::PEREGRINE)
                  ->orWhereNull('team');
        })
        // MUST have valid phone number for calling system
        ->whereNotNull('phone_number')
        ->where('phone_number', '!=', 'N/A')
        ->where('phone_number', '!=', '')
        // Exclude verifier-submitted leads
        ->whereNull('verified_by')
        // Deduplicate: only show the latest lead per phone number (prevents CSV import duplicates)
        ->whereIn('id', function($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('leads')
                ->where('status', '!=', 'disposed')
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->where('phone_number', '!=', 'N/A')
                ->groupBy('phone_number');
        })
        // Apply search filter
        ->when($search, function($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(100);

        // Get Peregrine closer names for tagging
        $peregrineClosers = \App\Models\User::role(Roles::PEREGRINE_CLOSER)->pluck('name')->toArray();

        // Get carrier-partner combinations with their approved states
        $carrierPartnerData = \App\Models\AgentCarrierState::with(['insuranceCarrier', 'partner'])
            ->whereHas('insuranceCarrier', function($q) {
                $q->where('is_active', true);
            })
            ->whereHas('partner', function($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->groupBy(function($item) {
                return $item->insurance_carrier_id . '_' . $item->partner_id;
            })
            ->map(function($group) {
                $first = $group->first();
                return [
                    'carrier_id' => $first->insurance_carrier_id,
                    'carrier_name' => $first->insuranceCarrier->name,
                    'partner_id' => $first->partner_id,
                    'partner_name' => $first->partner->name,
                    'partner_code' => $first->partner->code,
                    'display_name' => $first->insuranceCarrier->name . ' (' . $first->partner->code . ')',
                    'states' => $group->pluck('state')->unique()->toArray()
                ];
            })
            ->values();

        // For backward compatibility, also get simple carrier list
        $insuranceCarriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();

        // US States list
        $usStates = [
            'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
            'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
            'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
            'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
            'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
            'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
            'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
            'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
            'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
            'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
            'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
            'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
            'WI' => 'Wisconsin', 'WY' => 'Wyoming', 'DC' => 'District of Columbia'
        ];

        return view('ravens.calling', compact('leads', 'peregrineClosers', 'insuranceCarriers', 'carrierPartnerData', 'usStates'));
    }

    /**
     * Get count of unique leads dialed today by this employee
     */
    private function getDialedTodayCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->distinct('lead_id')
            ->count('lead_id');
    }

    /**
     * Get total number of dials made in date range (each attempt counts).
     * Uses LeadDial table — the single source of truth for dial tracking.
     */
    private function getDialedCount($userId, $startDate, $endDate)
    {
        return LeadDial::where('user_id', $userId)
            ->whereBetween('dialed_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get count of sales made today by this employee
     */
    private function getSalesTodayCount($userId)
    {
        return Lead::where(function($q) use ($userId) {
                $q->where('closer_name', Auth::user()->name)
                  ->orWhere('closer_id', $userId);
            })
            ->where(function($q) {
                $q->whereDate('sale_at', today())
                  ->orWhereDate('sale_date', today());
            })
            ->where('team', Teams::RAVENS)
            ->count();
    }

    /**
     * Get count of calls connected today
     */
    private function getCallsConnectedCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->where('call_status', 'connected')
            ->count();
    }

    /**
     * Get count of sales in date range
     */
    private function getSalesCount($userId, $startDate, $endDate)
    {
        return Lead::where(function($q) use ($userId) {
                $q->where('closer_name', Auth::user()->name)
                  ->orWhere('closer_id', $userId);
            })
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_at', [$startDate, $endDate])
                  ->orWhereBetween('sale_date', [$startDate, $endDate]);
            })
            ->where('team', Teams::RAVENS)
            ->count();
    }

    /**
     * Get count of calls connected in date range.
     * Uses LeadDial table with outcome = 'connected'.
     */
    private function getCallsConnectedFiltered($userId, $startDate, $endDate)
    {
        return LeadDial::where('user_id', $userId)
            ->whereBetween('dialed_at', [$startDate, $endDate])
            ->where('outcome', 'connected')
            ->count();
    }

    /**
     * Get MTD (Month-To-Date) sales count for this employee
     */
    private function getMTDSalesCount($userId)
    {
        return Lead::where(function($q) use ($userId) {
                $q->where('closer_name', Auth::user()->name)
                  ->orWhere('closer_id', $userId);
            })
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->whereMonth('sale_at', now()->month)
                        ->whereYear('sale_at', now()->year);
                })
                ->orWhere(function($sub) {
                    $sub->whereMonth('sale_date', now()->month)
                        ->whereYear('sale_date', now()->year);
                });
            })
            ->where('team', Teams::RAVENS)
            ->count();
    }

    /**
     * Get lead data for the Ravens form popup
     */
    public function getLeadData($leadId)
    {
        $lead = Lead::find($leadId);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        try {
            // Safely parse beneficiaries - handle string/null/array
            $beneficiaries = $lead->beneficiaries;
            if (is_string($beneficiaries)) {
                $decoded = json_decode($beneficiaries, true);
                $beneficiaries = is_array($decoded) ? $decoded : [];
            }
            if (!is_array($beneficiaries)) {
                $beneficiaries = [];
            }
            // Fallback: if no structured beneficiaries, use the legacy text field
            if (empty($beneficiaries) && $lead->beneficiary) {
                $beneficiaries = [['name' => $lead->beneficiary, 'dob' => $lead->beneficiary_dob ? \Carbon\Carbon::parse($lead->beneficiary_dob)->format('Y-m-d') : null, 'relation' => '']];
            }

            // Return full lead data for the form with properly formatted dates
            return response()->json([
                'id' => $lead->id,
                'cn_name' => $lead->cn_name,
                'phone_number' => $lead->phone_number,
                'secondary_phone_number' => $lead->secondary_phone_number,
                'date_of_birth' => $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : null,
                'ssn' => $lead->ssn,
                'gender' => $lead->gender,
                'state' => $lead->state,
                'zip_code' => $lead->zip_code,
                'beneficiaries' => $beneficiaries,
                'beneficiary_raw' => $lead->beneficiary,
                'carrier_name' => $lead->carrier_name,
                'coverage_amount' => $lead->coverage_amount,
                'monthly_premium' => $lead->monthly_premium,
                'birth_place' => $lead->birth_place,
                'smoker' => $lead->smoker,
                'height_weight' => $lead->height_weight,
                'height' => $lead->height,
                'weight' => $lead->weight,
                'driving_license' => $lead->driving_license,
                'address' => $lead->address,
                'emergency_contact' => $lead->emergency_contact,
                'medical_issue' => $lead->medical_issue,
                'medications' => $lead->medications,
                'doctor_name' => $lead->doctor_name,
                'doctor_number' => $lead->doctor_number,
                'doctor_address' => $lead->doctor_address,
                'policy_type' => $lead->policy_type,
                'policy_number' => $lead->policy_number,
                'initial_draft_date' => $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : null,
                'future_draft_date' => $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('Y-m-d') : null,
                'bank_name' => $lead->bank_name,
                'account_title' => $lead->account_title,
                'account_type' => $lead->account_type,
                'routing_number' => $lead->routing_number,
                'account_number' => $lead->acc_number,
                'account_verified_by' => $lead->account_verified_by,
                'verified_by' => $lead->account_verified_by,
                'bank_balance' => $lead->bank_balance,
                'card_number' => $lead->card_number,
                'cvv' => $lead->cvv,
                'expiry_date' => $lead->expiry_date,
                'source' => $lead->source,
                'closer_name' => $lead->closer_name,
            ]);
        } catch (\Exception $e) {
            \Log::error('getLeadData error for lead ' . $leadId . ': ' . $e->getMessage());
            // Return minimal data so the form can still open
            return response()->json([
                'id' => $lead->id,
                'cn_name' => $lead->cn_name,
                'phone_number' => $lead->phone_number,
                'beneficiaries' => [],
            ]);
        }
    }

    /**
     * Save lead data without marking as sale
     */
    public function saveLead(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::findOrFail($leadId);

            // Prepare update data - only update fields that are provided and not empty
            $updateData = [];
            
            // Handle all possible fields from phase 2 and phase 3
            if ($request->filled('cn_name')) {
                $updateData['cn_name'] = $request->input('cn_name');
            }
            
            if ($request->filled('phone_number')) {
                $updateData['phone_number'] = $request->input('phone_number');
            }
            
            if ($request->filled('date_of_birth')) {
                $updateData['date_of_birth'] = $request->input('date_of_birth');
            }
            
            if ($request->filled('ssn')) {
                $updateData['ssn'] = $request->input('ssn');
            }
            
            if ($request->filled('gender')) {
                $updateData['gender'] = $request->input('gender');
            }
            
            if ($request->filled('address')) {
                $updateData['address'] = $request->input('address');
            }
            
            // Handle beneficiaries array
            if ($request->has('beneficiaries')) {
                $updateData['beneficiaries'] = json_encode($request->input('beneficiaries'));
            }
            
            if ($request->filled('policy_type')) {
                $updateData['policy_type'] = $request->input('policy_type');
            }
            
            if ($request->filled('policy_number')) {
                $updateData['policy_number'] = $request->input('policy_number');
            }
            
            if ($request->filled('account_title')) {
                $updateData['account_title'] = $request->input('account_title');
            }
            
            if ($request->filled('carrier_name')) {
                $updateData['carrier_name'] = $request->input('carrier_name');
            }
            
            if ($request->filled('coverage_amount')) {
                $updateData['coverage_amount'] = $request->input('coverage_amount');
            }
            
            if ($request->filled('monthly_premium')) {
                $updateData['monthly_premium'] = $request->input('monthly_premium');
            }
            
            if ($request->filled('initial_draft_date')) {
                $updateData['initial_draft_date'] = $request->input('initial_draft_date');
            }
            
            if ($request->filled('bank_name')) {
                $updateData['bank_name'] = $request->input('bank_name');
            }
            
            if ($request->filled('account_type')) {
                $updateData['account_type'] = $request->input('account_type');
            }
            
            if ($request->filled('routing_number')) {
                $updateData['routing_number'] = $request->input('routing_number');
            }
            
            if ($request->filled('account_number')) {
                $updateData['acc_number'] = $request->input('account_number');
            }
            
            if ($request->filled('account_verified_by')) {
                $updateData['account_verified_by'] = $request->input('account_verified_by');
            }
            
            if ($request->filled('bank_balance')) {
                $updateData['bank_balance'] = $request->input('bank_balance');
            }
            
            if ($request->filled('source')) {
                $updateData['source'] = $request->input('source');
            }
            
            if ($request->filled('closer_name')) {
                $updateData['closer_name'] = $request->input('closer_name');
            }
            
            if ($request->filled('height')) {
                $updateData['height'] = $request->input('height');
            }
            
            if ($request->filled('weight')) {
                $updateData['weight'] = $request->input('weight');
            }
            
            if ($request->filled('secondary_phone_number')) {
                $updateData['secondary_phone_number'] = $request->input('secondary_phone_number');
            }
            
            if ($request->filled('emergency_contact')) {
                $updateData['emergency_contact'] = $request->input('emergency_contact');
            }
            
            if ($request->filled('driving_license')) {
                $updateData['driving_license'] = $request->input('driving_license');
            }
            
            if ($request->filled('birth_place')) {
                $updateData['birth_place'] = $request->input('birth_place');
            }
            
            if ($request->filled('smoker')) {
                $updateData['smoker'] = $request->input('smoker') ? 'yes' : 'no';
            }
            
            if ($request->filled('medical_issue')) {
                $updateData['medical_issue'] = $request->input('medical_issue');
            }
            
            if ($request->filled('medications')) {
                $updateData['medications'] = $request->input('medications');
            }
            
            if ($request->filled('doctor_name')) {
                $updateData['doctor_name'] = $request->input('doctor_name');
            }
            
            if ($request->filled('doctor_number')) {
                $updateData['doctor_number'] = $request->input('doctor_number');
            }
            
            if ($request->filled('doctor_address')) {
                $updateData['doctor_address'] = $request->input('doctor_address');
            }
            
            if ($request->filled('future_draft_date')) {
                $updateData['future_draft_date'] = $request->input('future_draft_date');
            }
            
            if ($request->filled('card_number')) {
                $updateData['card_number'] = $request->input('card_number');
            }
            
            if ($request->filled('cvv')) {
                $updateData['cvv'] = $request->input('cvv');
            }
            
            if ($request->filled('expiry_date')) {
                $updateData['expiry_date'] = $request->input('expiry_date');
            }
            
            if ($request->filled('zip_code')) {
                $updateData['zip_code'] = $request->input('zip_code');
            }

            if (!empty($updateData)) {
                $lead->update($updateData);
                
                \Log::info('Ravens lead saved', [
                    'lead_id' => $leadId,
                    'updated_fields' => array_keys($updateData),
                    'user_id' => Auth::id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead information saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save lead information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit sale - Mark lead as sold and send to sales section
     */
    public function submitSale(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::findOrFail($leadId);

            // Check for duplicate sales within 3 months using phone number and SSN
            $phone = $request->input('phone_number') ?? $lead->phone_number;
            $ssn = $request->input('ssn') ?? $lead->ssn;
            
            $repeatSale = $this->checkRepeatSale($phone, $ssn, $leadId);
            
            if ($repeatSale) {
                // Log repeat sale for admin review
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'repeat_sale_detected',
                    'model' => 'Lead',
                    'model_id' => $leadId,
                    'changes' => json_encode([
                        'message' => 'Repeat sale detected within 3 months',
                        'previous_sale_id' => $repeatSale->id,
                        'previous_sale_date' => $repeatSale->sale_at,
                        'previous_closer' => $repeatSale->closer_name,
                        'phone' => $phone,
                        'ssn' => $ssn,
                    ]),
                    'ip_address' => $request->ip(),
                ]);
            }

            // Update lead with all form data
            $updateData = [];
            
            if ($request->filled('cn_name')) {
                $updateData['cn_name'] = $request->input('cn_name');
            }
            
            if ($request->filled('phone_number')) {
                $updateData['phone_number'] = $request->input('phone_number');
            }
            
            if ($request->filled('date_of_birth')) {
                $updateData['date_of_birth'] = $request->input('date_of_birth');
            }
            
            if ($request->filled('ssn')) {
                $updateData['ssn'] = $request->input('ssn');
            }
            
            if ($request->filled('gender')) {
                $updateData['gender'] = $request->input('gender');
            }
            
            if ($request->filled('address')) {
                $updateData['address'] = $request->input('address');
            }
            
            // Handle beneficiaries array
            if ($request->has('beneficiaries')) {
                $updateData['beneficiaries'] = json_encode($request->input('beneficiaries'));
            }
            
            if ($request->filled('policy_type')) {
                $updateData['policy_type'] = $request->input('policy_type');
            }
            
            if ($request->filled('policy_number')) {
                $updateData['policy_number'] = $request->input('policy_number');
            }
            
            if ($request->filled('account_title')) {
                $updateData['account_title'] = $request->input('account_title');
            }
            
            if ($request->filled('carrier_name')) {
                $updateData['carrier_name'] = $request->input('carrier_name');
            }
            
            if ($request->filled('coverage_amount')) {
                $updateData['coverage_amount'] = $request->input('coverage_amount');
            }
            
            if ($request->filled('monthly_premium')) {
                $updateData['monthly_premium'] = $request->input('monthly_premium');
            }
            
            if ($request->filled('initial_draft_date')) {
                $updateData['initial_draft_date'] = $request->input('initial_draft_date');
            }
            
            if ($request->filled('bank_name')) {
                $updateData['bank_name'] = $request->input('bank_name');
            }
            
            if ($request->filled('account_type')) {
                $updateData['account_type'] = $request->input('account_type');
            }
            
            if ($request->filled('routing_number')) {
                $updateData['routing_number'] = $request->input('routing_number');
            }
            
            if ($request->filled('account_number')) {
                $updateData['acc_number'] = $request->input('account_number');
            }
            
            if ($request->filled('account_verified_by')) {
                $updateData['account_verified_by'] = $request->input('account_verified_by');
            }
            
            if ($request->filled('bank_balance')) {
                $updateData['bank_balance'] = $request->input('bank_balance');
            }
            
            if ($request->filled('source')) {
                $updateData['source'] = $request->input('source');
            }
            
            if ($request->filled('closer_name')) {
                $updateData['closer_name'] = $request->input('closer_name');
            }
            
            if ($request->filled('state')) {
                $updateData['state'] = $request->input('state');
            }
            
            if ($request->filled('zip_code')) {
                $updateData['zip_code'] = $request->input('zip_code');
            }
            
            if ($request->filled('height')) {
                $updateData['height'] = $request->input('height');
            }
            
            if ($request->filled('weight')) {
                $updateData['weight'] = $request->input('weight');
            }
            
            if ($request->filled('secondary_phone_number')) {
                $updateData['secondary_phone_number'] = $request->input('secondary_phone_number');
            }
            
            if ($request->filled('emergency_contact')) {
                $updateData['emergency_contact'] = $request->input('emergency_contact');
            }
            
            if ($request->filled('driving_license')) {
                $updateData['driving_license'] = $request->input('driving_license');
            }
            
            if ($request->filled('birth_place')) {
                $updateData['birth_place'] = $request->input('birth_place');
            }
            
            if ($request->filled('smoker')) {
                $updateData['smoker'] = $request->input('smoker') ? 'yes' : 'no';
            }
            
            if ($request->filled('medical_issue')) {
                $updateData['medical_issue'] = $request->input('medical_issue');
            }
            
            if ($request->filled('medications')) {
                $updateData['medications'] = $request->input('medications');
            }
            
            if ($request->filled('doctor_name')) {
                $updateData['doctor_name'] = $request->input('doctor_name');
            }
            
            if ($request->filled('doctor_number')) {
                $updateData['doctor_number'] = $request->input('doctor_number');
            }
            
            if ($request->filled('doctor_address')) {
                $updateData['doctor_address'] = $request->input('doctor_address');
            }
            
            if ($request->filled('future_draft_date')) {
                $updateData['future_draft_date'] = $request->input('future_draft_date');
            }
            
            if ($request->filled('card_number')) {
                $updateData['card_number'] = $request->input('card_number');
            }
            
            if ($request->filled('cvv')) {
                $updateData['cvv'] = $request->input('cvv');
            }
            
            if ($request->filled('expiry_date')) {
                $updateData['expiry_date'] = $request->input('expiry_date');
            }

            // Mark as sold - pending status for QA review
            $updateData['status'] = 'pending'; // Sale status - pending QA review
            $updateData['sale_at'] = now();
            $updateData['sale_date'] = now()->format('Y-m-d');
            $updateData['team'] = Teams::RAVENS; // Mark as Ravens team sale
            $updateData['closer_id'] = Auth::id(); // Store user ID for reliable matching

            $lead->update($updateData);

            // Send notifications to QA and Managers
            $this->sendSaleNotifications($lead);

            // Log the sale submission
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'sale_submitted',
                'model' => 'Lead',
                'model_id' => $leadId,
                'changes' => json_encode([
                    'closer_name' => $request->input('closer_name'),
                    'sale_at' => now(),
                    'customer_name' => $request->input('cn_name'),
                ]),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sale submitted successfully! Notifications sent to QA and Managers.',
                'is_repeat_sale' => !is_null($repeatSale),
                'repeat_sale_message' => $repeatSale 
                    ? "Warning: This customer had a previous sale on " . $repeatSale->sale_at->format('M d, Y') . " by " . $repeatSale->closer_name 
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting sale: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for repeat sales within 3 months
     */
    private function checkRepeatSale($phone, $ssn, $currentLeadId)
    {
        $threeMonthsAgo = now()->subMonths(3);

        // Search for previous sales by phone or SSN within 3 months
        $query = Lead::where('id', '!=', $currentLeadId)
            ->whereNotNull('sale_at')
            ->where('sale_at', '>=', $threeMonthsAgo);

        // Check by phone OR SSN
        $query->where(function($q) use ($phone, $ssn) {
            if ($phone) {
                $q->where('phone_number', $phone);
            }
            if ($ssn) {
                $q->orWhere('ssn', $ssn);
            }
        });

        return $query->first();
    }

    /**
     * Send notifications to QA and Managers about new sale
     */
    private function sendSaleNotifications($lead)
    {
        $notificationService = app(NotificationService::class);

        // Get QA users
        $qaUsers = User::role(Roles::QA)->get();
        
        // Get Managers
        $managers = User::role(Roles::MANAGER)->get();

        $message = "New sale submitted by " . Auth::user()->name . " for customer: " . $lead->cn_name;

        // Send to QA
        foreach ($qaUsers as $qaUser) {
            $notificationService->createForUser(
                $qaUser,
                'New Sale Submitted',
                $message,
                [
                    'icon' => 'bx-dollar-circle',
                    'color' => 'success',
                    'type' => 'success',
                    'url' => route('sales.index'),
                ]
            );
        }

        // Send to Managers
        foreach ($managers as $manager) {
            $notificationService->createForUser(
                $manager,
                'New Sale Submitted',
                $message,
                [
                    'icon' => 'bx-dollar-circle',
                    'color' => 'success',
                    'type' => 'success',
                    'url' => route('sales.index'),
                ]
            );
        }
    }

    /**
     * Dispose a lead with a reason (no answer, wrong number, wrong details)
     */
    public function disposeLead(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'disposition' => 'required|in:no_answer,wrong_number,wrong_details',
                'notes' => 'nullable|string|max:500'
            ]);

            $lead = Lead::findOrFail($request->input('lead_id'));

            // Create a snapshot of lead data before disposing
            $leadSnapshot = $lead->only([
                'cn_name', 'phone_number', 'date_of_birth', 'ssn', 'gender',
                'address', 'state', 'policy_type', 'carrier_name', 'source'
            ]);

            // Create bad lead record
            $badLead = BadLead::create([
                'lead_id' => $lead->id,
                'disposed_by' => Auth::id(),
                'disposition' => $request->input('disposition'),
                'notes' => $request->input('notes'),
                'lead_name' => $lead->cn_name,
                'lead_phone' => $lead->phone_number,
                'lead_ssn' => $lead->ssn,
            ]);

            // Mark original lead AND all duplicates with the same phone number as disposed
            // This prevents other closers from seeing/disposing the same contact again
            $duplicateCount = Lead::where('phone_number', $lead->phone_number)
                ->where('status', '!=', 'disposed')
                ->update([
                    'status' => 'disposed',
                    'disposed_at' => now(),
                    'disposed_by' => Auth::id(),
                    'disposition_reason' => $request->input('disposition'),
                ]);

            // Log the disposition
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'lead_disposed',
                'model' => 'Lead',
                'model_id' => $lead->id,
                'changes' => json_encode([
                    'disposition' => $request->input('disposition'),
                    'notes' => $request->input('notes'),
                    'customer_name' => $lead->cn_name,
                    'duplicates_disposed' => $duplicateCount,
                ]),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead disposed successfully',
                'disposition' => BadLead::getDispositionLabel($request->input('disposition'))
            ]);
        } catch (\Exception $e) {
            Log::error('Error disposing lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to dispose lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View all bad/disposed leads
     */
    public function badLeads(Request $request)
    {
        $filter = $request->input('filter', 'today');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $search = $request->input('search');
        $timezone = 'America/Denver';

        if ($filter === 'custom' && $customStart && $customEnd) {
            try {
                $startDate = \Carbon\Carbon::parse($customStart, $timezone)->startOfDay();
                $endDate = \Carbon\Carbon::parse($customEnd, $timezone)->endOfDay();
            } catch (\Exception $e) {
                $startDate = \Carbon\Carbon::today($timezone)->startOfDay();
                $endDate = \Carbon\Carbon::today($timezone)->endOfDay();
            }
        } else {
            $startDate = \Carbon\Carbon::today($timezone)->startOfDay();
            $endDate = \Carbon\Carbon::today($timezone)->endOfDay();
        }

        $query = BadLead::with(['lead', 'disposedBy'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('lead_name', 'like', '%'.$search.'%')
                  ->orWhere('lead_phone', 'like', '%'.$search.'%')
                  ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        $badLeads = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->appends($request->query());

        // KPI stats for the filtered period
        $allBadInRange = BadLead::whereBetween('created_at', [$startDate, $endDate]);
        $totalBad = (clone $allBadInRange)->count();
        $dispositionCounts = BadLead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('disposition, COUNT(*) as cnt')
            ->groupBy('disposition')
            ->pluck('cnt', 'disposition')
            ->toArray();

        $badStats = [
            'total' => $totalBad,
            'no_answer' => $dispositionCounts['no_answer'] ?? 0,
            'wrong_number' => $dispositionCounts['wrong_number'] ?? 0,
            'not_interested' => $dispositionCounts['not_interested'] ?? 0,
            'other' => $totalBad - ($dispositionCounts['no_answer'] ?? 0) - ($dispositionCounts['wrong_number'] ?? 0) - ($dispositionCounts['not_interested'] ?? 0),
        ];

        return view('ravens.bad-leads', compact('badLeads', 'badStats', 'filter', 'search'));
    }

    /**
     * Restore a disposed lead back to the calling system
     */
    public function restoreLead(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
            ]);

            $lead = Lead::findOrFail($request->input('lead_id'));

            // Verify the lead is actually disposed
            if ($lead->status !== 'disposed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This lead is not disposed'
                ], 400);
            }

            $phoneNumber = $lead->phone_number;

            // Restore ALL leads with the same phone number (undo the mass disposal)
            $restoredCount = Lead::where('phone_number', $phoneNumber)
                ->where('status', 'disposed')
                ->update([
                    'status' => 'closed', // Restore to previous status
                    'disposed_at' => null,
                    'disposed_by' => null,
                    'disposition_reason' => null,
                ]);

            // Delete the bad lead records for this phone number
            BadLead::where('lead_phone', $phoneNumber)->delete();

            // Log the restoration
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'lead_restored',
                'model' => 'Lead',
                'model_id' => $lead->id,
                'changes' => json_encode([
                    'customer_name' => $lead->cn_name,
                    'phone_number' => $phoneNumber,
                    'restored_count' => $restoredCount,
                ]),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Lead restored successfully (" . $restoredCount . " record(s) restored)",
                'restored_count' => $restoredCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save callback note for a lead (auto-clears after 3 days)
     */
    public function saveCallbackNote(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'note' => 'nullable|string|max:500',
            ]);

            $lead = Lead::findOrFail($request->input('lead_id'));
            $note = trim($request->input('note'));

            // Update the callback note and timestamp
            $lead->update([
                'callback_note' => $note ?: null,
                'callback_note_updated_at' => $note ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => $note ? 'Callback note saved' : 'Callback note cleared',
                'note' => $note,
                'updated_at' => $note ? now()->diffForHumans() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving callback note: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record that the current user dialed a lead.
     * Each dial attempt creates a NEW row so every call is counted.
     */
    public function recordDial(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'outcome' => 'nullable|string|in:dialed,no_answer,callback,connected,not_interested',
            ]);

            $dial = LeadDial::create([
                'lead_id' => $request->input('lead_id'),
                'user_id' => Auth::id(),
                'dialed_at' => now(),
                'outcome' => $request->input('outcome', 'dialed'),
            ]);

            return response()->json([
                'success' => true,
                'dial_id' => $dial->id,
                'dialed_at' => $dial->dialed_at->diffForHumans(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording dial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record dial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dial status for all leads visible in the calling system.
     * Returns a map of lead_id => [user_name, user_initials, dialed_at, color] for each dial.
     */
    public function getDialStatus()
    {
        try {
            $currentUserId = Auth::id();

            // Get all dials from today (we only show today's dials to keep it relevant)
            $dials = LeadDial::with('user:id,name')
                ->whereDate('dialed_at', today())
                ->orderBy('dialed_at', 'desc')
                ->get();

            // Assign a consistent color to each user based on their ID
            $userColors = [];
            $colorPalette = [
                '#4e73df', // blue
                '#e74a3b', // red
                '#1cc88a', // green
                '#f6c23e', // yellow
                '#36b9cc', // cyan
                '#6f42c1', // purple
                '#fd7e14', // orange
                '#20c997', // teal
                '#e83e8c', // pink
                '#6610f2', // indigo
            ];

            // Group dials by lead+user: show one badge per user per lead
            // with a count of how many times and the latest timestamp.
            $raw = [];
            foreach ($dials as $dial) {
                $userId = $dial->user_id;
                $leadId = $dial->lead_id;
                $key = $leadId . '-' . $userId;

                if (!isset($raw[$key])) {
                    $userName = $dial->user->name ?? 'Unknown';
                    if (!isset($userColors[$userId])) {
                        $userColors[$userId] = $colorPalette[$userId % count($colorPalette)];
                    }
                    $parts = explode(' ', $userName);
                    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

                    $raw[$key] = [
                        'lead_id' => $leadId,
                        'user_id' => $userId,
                        'user_name' => $userName,
                        'initials' => $initials,
                        'color' => $userColors[$userId],
                        'is_mine' => $userId === $currentUserId,
                        'dialed_at' => $dial->dialed_at->format('g:i A'),
                        'outcome' => $dial->outcome,
                        'count' => 0,
                    ];
                }
                $raw[$key]['count']++;
                // Keep the latest time
                if ($dial->dialed_at->format('g:i A') !== $raw[$key]['dialed_at']) {
                    $raw[$key]['dialed_at'] = $dial->dialed_at->format('g:i A');
                }
            }

            // Restructure into per-lead map
            $dialMap = [];
            foreach ($raw as $entry) {
                $dialMap[$entry['lead_id']][] = $entry;
            }

            return response()->json([
                'success' => true,
                'dials' => $dialMap,
                'current_user_id' => $currentUserId,
                'user_colors' => $userColors,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting dial status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dial status'
            ], 500);
        }
    }
}
