<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Imports\LeadsImport;
use App\Models\AuditLog;
use App\Models\Lead;
use App\Events\LeadCreated;
use App\Events\SaleCreated;
use App\Services\CommissionCalculationService;
use App\Support\Roles;
use App\Support\Statuses;
use App\Support\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    /**
     * Get status color configuration
     */
    private function getStatusColors()
    {
        return [
            'pending' => [
                'label' => 'Pending',
                'gradient' => 'linear-gradient(135deg, #ffc107 0%, #ffb300 100%)',
                'icon' => 'mdi-clock-outline'
            ],
            'accepted' => [
                'label' => 'Approved',
                'gradient' => 'linear-gradient(135deg, #28a745 0%, #25a644 100%)',
                'icon' => 'mdi-check-circle'
            ],
            'rejected' => [
                'label' => 'Declined',
                'gradient' => 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)',
                'icon' => 'mdi-close-circle'
            ],
            'underwritten' => [
                'label' => 'Underwriting',
                'gradient' => 'linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%)',
                'icon' => 'mdi-file-document-edit'
            ],
        ];
    }

    public function index(Request $request)
    {
        // Raven Leads — deduplicates by phone (latest entry wins)
        $query = Lead::where('team', Teams::RAVENS);

        // Deduplication: for leads with a valid phone number, only show the latest (max id) per phone.
        // Leads with no/empty/N/A phone are always shown (cannot be deduplicated).
        $query->where(function($q) {
            $q->whereIn('id', function($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('leads')
                    ->where('team', Teams::RAVENS)
                    ->whereNotNull('phone_number')
                    ->where('phone_number', '!=', '')
                    ->where('phone_number', '!=', 'N/A')
                    ->groupBy('phone_number');
            })
            ->orWhereNull('phone_number')
            ->orWhere('phone_number', '')
            ->orWhere('phone_number', 'N/A');
        });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('ssn', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Carrier filter
        if ($request->filled('carrier')) {
            $query->where('carrier_name', $request->carrier);
        }

        // State filter
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Closer filter
        if ($request->filled('closer')) {
            $query->where('closer_name', $request->closer);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(50);

        // Count of duplicate leads (for tab badge)
        $duplicateCount = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('phone_number')->where('phone_number', '!=', '')->where('phone_number', '!=', 'N/A')
            ->whereNotIn('id', function($sub) {
                $sub->selectRaw('MAX(id)')->from('leads')
                    ->where('team', Teams::RAVENS)
                    ->whereNotNull('phone_number')->where('phone_number', '!=', '')->where('phone_number', '!=', 'N/A')
                    ->groupBy('phone_number');
            })
            ->count();

        // Get Peregrine closer names for tagging
        $peregrineClosers = \App\Models\User::role(Roles::PEREGRINE_CLOSER)->pluck('name')->toArray();

        // Get filter options
        $carriers = \App\Models\InsuranceCarrier::whereHas('agentStates')->orderBy('name')->pluck('name');
        if ($carriers->isEmpty()) {
            $carriers = \App\Models\InsuranceCarrier::orderBy('name')->pluck('name');
        }
        $states = Lead::whereNotNull('state')->where('state', '!=', '')->where('state', '!=', 'N/A')->distinct()->orderBy('state')->pluck('state');
        $closerNames = Lead::whereNotNull('closer_name')->where('closer_name', '!=', '')
            ->distinct()->orderBy('closer_name')->pluck('closer_name');
        $allStatuses = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('admin.leads.index_simple', compact('leads', 'peregrineClosers', 'carriers', 'states', 'closerNames', 'allStatuses', 'duplicateCount'));
    }

    /**
     * Show duplicate leads — entries that share a phone number with a newer record.
     * The main index shows only the latest entry per phone; this shows what was hidden.
     */
    public function duplicates(Request $request)
    {
        // The canonical set: MAX(id) per phone in Raven scope
        $latestIdSubquery = function($sub) {
            $sub->selectRaw('MAX(id)')->from('leads')
                ->where('team', Teams::RAVENS)
                ->whereNotNull('phone_number')->where('phone_number', '!=', '')->where('phone_number', '!=', 'N/A')
                ->groupBy('phone_number');
        };

        // Duplicates: Raven leads with a valid phone that are NOT the latest entry
        $query = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('phone_number')->where('phone_number', '!=', '')->where('phone_number', '!=', 'N/A')
            ->whereNotIn('id', $latestIdSubquery);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%")
                  ->orWhere('ssn', 'like', "%{$search}%");
            });
        }

        $duplicates = $query->orderBy('phone_number')->orderBy('id', 'asc')->paginate(50);

        // For each phone on this page, fetch the canonical (current) lead to link back to
        $phones = $duplicates->pluck('phone_number')->unique()->filter()->values();
        $canonicalLeads = Lead::whereIn('phone_number', $phones)
            ->where('team', Teams::RAVENS)
            ->orderByDesc('id')
            ->get()
            ->groupBy('phone_number')
            ->map(fn($group) => $group->first()); // first = highest id (ordered desc)

        $duplicateCount = Lead::where('team', Teams::RAVENS)
            ->whereNotNull('phone_number')->where('phone_number', '!=', '')->where('phone_number', '!=', 'N/A')
            ->whereNotIn('id', $latestIdSubquery)
            ->count();

        return view('admin.leads.duplicates', compact('duplicates', 'canonicalLeads', 'duplicateCount'));
    }

    /**
     * Show only Peregrine leads (verifier, peregrine closer, peregrine validator)
     */
    public function peregrineLeads(Request $request)
    {
        // Show leads from Peregrine team
        $query = Lead::where('team', Teams::PEREGRINE);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%")
                  ->orWhere('ssn', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Carrier filter
        if ($request->filled('carrier')) {
            $query->where('carrier_name', $request->carrier);
        }
        
        // Closer filter
        if ($request->filled('closer')) {
            $query->where('closer_name', $request->closer);
        }
        
        // State filter
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $leads = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Get Peregrine closer names for tagging
        $peregrineClosers = \App\Models\User::role(Roles::PEREGRINE_CLOSER)->pluck('name')->toArray();
        
        // Get filter options
        $carriers = \App\Models\InsuranceCarrier::whereHas('agentStates')->orderBy('name')->pluck('name');
        if ($carriers->isEmpty()) {
            $carriers = \App\Models\InsuranceCarrier::orderBy('name')->pluck('name');
        }
        $closerNames = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('closer_name')->where('closer_name', '!=', '')
            ->distinct()->orderBy('closer_name')->pluck('closer_name');
        $states = Lead::where('team', Teams::PEREGRINE)
            ->whereNotNull('state')->where('state', '!=', '')->where('state', '!=', 'N/A')
            ->distinct()->orderBy('state')->pluck('state');
        
        return view('admin.leads.peregrine', compact('leads', 'peregrineClosers', 'carriers', 'closerNames', 'states'));
    }

    public function sales(Request $request)
    {
        // Default to current month only when no other specific filters are active.
        // If the user is filtering by partner, carrier, or searching, show all-time
        // so the results aren't silently cut off by the month boundary.
        $hasSpecificFilter = $request->filled('partner')
            || $request->filled('carrier')
            || $request->filled('search')
            || $request->filled('policy_type');

        if (!$request->filled('date_from') && !$request->filled('date_to') && !$hasSpecificFilter) {
            $request->merge([
                'date_from' => now()->startOfMonth()->toDateString(),
                'date_to'   => now()->endOfMonth()->toDateString(),
            ]);
        }

        // Sales section - show ALL sales (each closer gets credit for their sale)
        // Resale badge indicates when same customer was sold multiple times
        // Dedup is only applied in Pending Submission to prevent duplicate processing
        $query = Lead::with(['insuranceCarrier', 'qaUser', 'submissionReviewer', 'pendingContractBy', 'pendingDraftBy'])
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->whereNull('rewrite_sent_back_at') // hide rewrite sales that have been sent back to retention
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where(function($q) {
                // At least one real sale field must be filled (not just verifier basics)
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            });
        
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
        
        // Tab-based status filter
        $activeTab = $request->get('status', 'all');
        if ($activeTab === 'pending_validation') {
            $query->whereNull('ravens_validated_at');
        } elseif ($activeTab === 'validated') {
            $query->where('ravens_validation_status', 'valid');
        } elseif ($activeTab === 'not_valid') {
            $query->where('ravens_validation_status', 'not_valid');
        } elseif ($activeTab === 'callback') {
            $query->whereNotNull('recall_requested_at');
        }
        // 'all' = no additional filter

        // Filter by partner
        if ($request->filled('partner')) {
            $query->where('partner_id', $request->partner);
        }

        // Filter by policy type
        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }
        
        // Date range filter for sale_date
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        // Get insurance carriers that have partner assignments (from agent_carrier_states table)
        $insuranceCarriers = \App\Models\InsuranceCarrier::whereHas('agentStates')
            ->orderBy('name')
            ->pluck('name');
        
        // If no carriers with partners, fall back to all carriers
        if ($insuranceCarriers->isEmpty()) {
            $insuranceCarriers = \App\Models\InsuranceCarrier::orderBy('name')->pluck('name');
        }
        
        $carriers = $insuranceCarriers; // Use insurance carriers for filter
        
        // KPI statistics - count ALL sales (no dedup, each closer gets credit)
        $statsBase = Lead::whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->whereNull('rewrite_sent_back_at')
            ->where(function($q) {
                $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
            })
            ->where(function($q) {
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            })
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('sale_date', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('sale_date', '<=', $request->date_to));

        $statusCounts = [
            'all'                => (clone $statsBase)->count(),
            'pending_validation' => (clone $statsBase)->whereNull('ravens_validated_at')->count(),
            'validated'          => (clone $statsBase)->where('ravens_validation_status', 'valid')->count(),
            'not_valid'          => (clone $statsBase)->where('ravens_validation_status', 'not_valid')->count(),
            'callback'           => (clone $statsBase)->whereNotNull('recall_requested_at')->count(),
        ];
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        
        // Calculate resale history for each lead (detailed info for modal)
        $phoneNumbers = $leads->pluck('phone_number')->unique()->filter();
        if ($phoneNumbers->isNotEmpty()) {
            $resaleHistory = Lead::select('id', 'phone_number', 'cn_name', 'closer_name', 'carrier_name', 
                    'coverage_amount', 'monthly_premium', 'sale_date', 'created_at', 'team', 'status')
                ->whereIn('phone_number', $phoneNumbers)
                ->whereNotNull('closer_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('phone_number');
            
            foreach ($leads as $lead) {
                $history = $resaleHistory->get($lead->phone_number, collect());
                // Count excludes current lead (for "Re-sold ×N" badge)
                $previousSales = $history->where('id', '!=', $lead->id);
                $lead->resale_count = $previousSales->count();
                // Log includes ALL sales (for detailed modal view)
                $lead->resale_log = $history->map(function($sale) use ($lead) {
                    return [
                        'id' => $sale->id,
                        'is_current' => $sale->id === $lead->id,
                        'closer_name' => $sale->closer_name,
                        'carrier_name' => $sale->carrier_name,
                        'coverage_amount' => $sale->coverage_amount,
                        'monthly_premium' => $sale->monthly_premium,
                        'sale_date' => $sale->sale_date,
                        'created_at' => $sale->created_at?->format('M d, Y'),
                        'team' => $sale->team,
                        'status' => $sale->status,
                    ];
                })->values()->toArray();
            }
        }
        
        $statusColors = $this->getStatusColors();
        
        // Get Peregrine closer names for tagging
        $peregrineClosers = \App\Models\User::role(Roles::PEREGRINE_CLOSER)->pluck('name')->toArray();
        
        $partners = \App\Models\Partner::where('is_active', true)->orderBy('code')->get(['id', 'name', 'code']);

        return view('admin.sales.index', compact('leads', 'carriers', 'insuranceCarriers', 'statusCounts', 'statusColors', 'peregrineClosers', 'partners', 'activeTab'));
    }

    public function storeManualSale(Request $request)
    {
        $validated = $request->validate([
            'cn_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'carrier_name' => 'required|string|max:255',
            'coverage_amount' => 'required|numeric|min:0',
            'monthly_premium' => 'required|numeric|min:0',
            'policy_type' => 'required|string|in:Term,Whole Life,Universal',
            'policy_number' => 'nullable|string|max:100',
            'sale_date' => 'required|date',
            'closer_name' => 'required|string|max:255',
            'status' => 'nullable|string|in:pending,accepted,rejected,verified',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ]);

        try {
            // Determine source_type based on user role
            $user = Auth::user();
            $sourceType = null;
            if ($user->hasAnyRole([Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR])) {
                $sourceType = Teams::PEREGRINE;
            }

            // Convert smoker to 'yes'/'no' for ENUM
            if (isset($validated['smoker'])) {
                $validated['smoker'] = $validated['smoker'] ? 'yes' : 'no';
            }

            $lead = Lead::create([
                ...$validated,
                'sale_at' => $validated['sale_date'],
                'status' => $validated['status'] ?? Statuses::LEAD_ACCEPTED,
                'source_type' => $sourceType,
                'is_manual_sale' => true,
            ]);

            return redirect()->route('sales.index')
                ->with('success', "Manual sale entry for {$lead->cn_name} created successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating manual sale entry: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.leads.create');
    }

    public function store(StoreLeadRequest $request)
    {
        // Create new lead (excluding carrier-specific fields from direct assignment)
        $leadData = $request->validated();

        // Remove carrier-related fields from lead data since they'll go in the carrier table
        $carrierData = [
            'name' => $leadData['carrier_name'] ?? null,
            'coverage_amount' => $leadData['coverage_amount'] ?? null,
            'premium_amount' => $leadData['monthly_premium'] ?? null,
            'status' => 'pending',
        ];

        // Remove carrier fields from lead data to avoid issues
        unset($leadData['carrier_name']);

        // Mark lead as Teams::PEREGRINE if created by a peregrine team member
        $user = Auth::user();
        if ($user->hasAnyRole([Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR])) {
            $leadData['source_type'] = Teams::PEREGRINE;
            $leadData['team'] = Teams::PEREGRINE;
        } elseif ($user->hasAnyRole([Roles::RAVENS_CLOSER])) {
            $leadData['team'] = Teams::RAVENS;
        }

        // Set proper status and closer name for manually created leads
        // Note: 'closed' status = lead form submitted by closer, NOT a completed sale.
        // sale_at is set later when submitSale() is called from the calling system.
        $leadData['status'] = Statuses::LEAD_CLOSED;
        $leadData['closer_name'] = $user->name;
        $leadData['closer_id'] = $user->id;
        $leadData['verified_by'] = null;

        // Convert smoker to 'yes'/'no' for ENUM
        if (isset($leadData['smoker'])) {
            $leadData['smoker'] = $leadData['smoker'] ? 'yes' : 'no';
        }

        // Create the lead
        $lead = Lead::create($leadData);

        // Create associated carrier if carrier name is provided
        if (! empty($carrierData['name'])) {
            $lead->carriers()->create($carrierData);
        }

        // Dispatch event to notify managers and assigned person
        event(new LeadCreated($lead, Auth::user()->name));

        // Redirect with success message
        return redirect()->route('leads.create')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Update lead during active call
     */
    public function updateDuringCall(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::with('carriers')->findOrFail($leadId);

            // Update lead fields
            $lead->date_of_birth = $request->input('date_of_birth');
            $lead->ssn = $request->input('ssn');
            $lead->address = $request->input('address');
            $lead->birth_place = $request->input('birth_place');
            $lead->medical_issue = $request->input('medical_issue');
            $lead->medications = $request->input('medications');
            $lead->height_weight = $request->input('height_weight');
            $lead->height = $request->input('height');
            $lead->weight = $request->input('weight');
            $lead->doctor_name = $request->input('doctor_name');
            $lead->policy_type = $request->input('policy_type');
            $lead->coverage_amount = $request->input('coverage_amount');
            $lead->monthly_premium = $request->input('monthly_premium');
            $lead->carrier_name = $request->input('carrier_name');
            $lead->beneficiary = $request->input('beneficiary');
            $smokerValue = $request->input('smoker');
            $lead->smoker = $smokerValue ? 'yes' : 'no';
            $lead->status = Statuses::LEAD_PENDING;
            $lead->updated_at = now();

            if ($request->input('action') === 'forward') {
                $lead->status = Statuses::LEAD_FORWARDED;
                $lead->forwarded_by = auth()->id();
            } else {
                $lead->status = Statuses::LEAD_ACTIVE;
            }

            // Save the lead
            $lead->save();

            // Handle carrier updates
            if ($request->has('carriers')) {
                $carrierData = $request->input('carriers');

                foreach ($carrierData as $carrierId => $carrierInfo) {
                    if ($carrierId === 'new') {
                        // Create new carrier
                        $lead->carriers()->create([
                            'name' => $carrierInfo['name'],
                            'policy_number' => $carrierInfo['policy_number'],
                            'premium_amount' => $carrierInfo['premium_amount'],
                            'coverage_amount' => $carrierInfo['coverage_amount'],
                            'phone' => $carrierInfo['phone'],
                            'email' => $carrierInfo['email'],
                            'website' => $carrierInfo['website'],
                            // 'status' => $carrierInfo['status'] ?? 'active',
                            'notes' => $carrierInfo['notes'],
                            'forwarded_by' => auth()->id(),
                            'sale_at' => now(),
                        ]);
                    } else {
                        // Update existing carrier
                        $carrier = $lead->carriers()->find($carrierId);
                        if ($carrier) {
                            $carrier->update([
                                'name' => $carrierInfo['name'],
                                'policy_number' => $carrierInfo['policy_number'],
                                'premium_amount' => $carrierInfo['premium_amount'],
                                'coverage_amount' => $carrierInfo['coverage_amount'],
                                'phone' => $carrierInfo['phone'],
                                'email' => $carrierInfo['email'],
                                'website' => $carrierInfo['website'],
                                // 'status' => $carrierInfo['status'],
                                'notes' => $carrierInfo['notes'],
                                'forwarded_by' => count($carrierData) == 1 ? auth()->id() : $carrier->forwarded_by,
                            ]);
                        }
                    }
                }
            }

            // Handle carrier deletions
            // if ($request->has('deleted_carriers')) {
            //     $deletedCarriers = $request->input('deleted_carriers');
            //     foreach ($deletedCarriers as $carrierId) {
            //         $lead->carriers()->where('id', $carrierId)->delete();
            //     }
            // }

            return redirect()->back()->with('success', 'Lead updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating lead during call', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forward lead to next agent/process
     */
    public function forwardLead(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $lead = Lead::findOrFail($leadId);

            // Logic for forwarding lead
            // This could involve assigning to another agent, changing status, etc.
            // For example:
            $lead->status = Statuses::LEAD_FORWARDED;  // Or another status that makes sense in your workflow
            $lead->forwarded_at = now();
            $lead->forwarded_by = auth()->id();
            $lead->save();

            // Log the forwarding action
            \Log::info('Lead forwarded', [
                'lead_id' => $leadId,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead has been forwarded successfully',
                'lead' => $lead,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error forwarding lead', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $insurance = Lead::with(['carriers', 'assignedValidator', 'assignedCloser', 'validator', 'verifier', 'qaUser'])->findOrFail($id);

        return view('admin.leads.show', compact('insurance'));
    }

    public function edit($id)
    {
        $lead = Lead::findOrFail($id);

        $closers = \App\Models\User::role([Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER])
            ->orderBy('name')
            ->get(['id', 'name']);

        $partners = \App\Models\Partner::where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'name', 'code']);

        return view('admin.leads.edit', compact('lead', 'closers', 'partners'));
    }

    public function update(UpdateLeadRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $identityFields = ['cn_name', 'phone_number', 'ssn', 'date_of_birth', 'address',
            'carrier_name', 'coverage_amount', 'monthly_premium', 'bank_name',
            'routing_number', 'acc_number', 'beneficiary', 'status',
            'closer_name', 'sale_at', 'sale_date', 'assigned_agent_id'];
        $before = $lead->only($identityFields);

        $validated = $request->validated();

        // Auto-populate sale_date from sale_at if sale_at is set but sale_date is not
        if (!empty($validated['sale_at']) && empty($validated['sale_date'])) {
            $validated['sale_date'] = \Carbon\Carbon::parse($validated['sale_at'])->toDateString();
        }

        // Prominently log any change to name or phone on an established lead
        if (!empty($lead->cn_name) && !empty($lead->phone_number)) {
            if (isset($validated['cn_name']) && $validated['cn_name'] !== $lead->cn_name) {
                AuditLog::logAction('lead_identity_change', auth()->user(), 'Lead', (int) $lead->id, [
                    'field'  => 'cn_name',
                    'before' => $lead->cn_name,
                    'after'  => $validated['cn_name'],
                ], 'Lead name changed on an established lead');
            }
            if (isset($validated['phone_number']) && $validated['phone_number'] !== $lead->phone_number) {
                AuditLog::logAction('lead_identity_change', auth()->user(), 'Lead', (int) $lead->id, [
                    'field'  => 'phone_number',
                    'before' => $lead->phone_number,
                    'after'  => $validated['phone_number'],
                ], 'Lead phone changed on an established lead');
            }
        }

        $lead->update($validated);

        // Sync beneficiaries JSON → scalar beneficiary/beneficiary_dob fields
        $bens = array_values(array_filter($request->input('beneficiaries', []), fn($b) => !empty(trim($b['name'] ?? ''))));
        $lead->beneficiaries   = $bens ?: null;
        $lead->beneficiary     = $bens[0]['name'] ?? null;
        $lead->beneficiary_dob = !empty($bens[0]['dob']) ? $bens[0]['dob'] : null;

        // Sync partner_id → assigned_partner text
        if (array_key_exists('partner_id', $validated)) {
            if (!empty($validated['partner_id'])) {
                $p = \App\Models\Partner::find($validated['partner_id']);
                $lead->assigned_partner = $p ? ($p->code ?: $p->name) : null;
            } else {
                $lead->assigned_partner = null;
            }
        }

        $lead->saveQuietly();

        $after   = $lead->fresh()->only($identityFields);
        $changed = array_filter($after, fn($v, $k) => $v !== $before[$k], ARRAY_FILTER_USE_BOTH);

        if (!empty($changed)) {
            AuditLog::logAction('lead_updated', auth()->user(), 'Lead', (int) $lead->id, [
                'before' => array_intersect_key($before, $changed),
                'after'  => $changed,
            ], 'Lead fields updated via edit form');
        }

        // Redirect back to the appropriate page based on the current route
        $redirectRoute = request()->route()->getName() === 'sales.update' ? 'sales.index' : 'leads.index';

        return redirect()->route($redirectRoute)->with('success', 'Lead updated successfully.');
    }

    /**
     * Update sales fields inline (carrier, policy type, coverage, premium)
     */
    public function updateSalesField(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        
        $field = $request->field;
        $value = $request->value;
        
        // Validate based on field type
        // Handle partner update specially (sets partner_id + syncs assigned_partner text)
        if ($field === 'partner') {
            $lead->partner_id = $value ?: null;
            $lead->assigned_partner = null;
            if ($value) {
                $p = \App\Models\Partner::find((int) $value);
                if (!$p) {
                    return response()->json(['success' => false, 'message' => 'Partner not found'], 400);
                }
                $lead->assigned_partner = $p->code ?: $p->name;
            }
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Partner updated']);
        }

        if ($field === 'followup_required') {
            $lead->followup_required = $value !== '' ? (bool)(int)$value : null;
            if (!(bool)(int)$value) {
                $lead->followup_scheduled_at = null;
            }
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Follow-up updated']);
        }

        if ($field === 'followup_scheduled_at') {
            if ($value && !strtotime($value)) {
                return response()->json(['success' => false, 'message' => 'Invalid date/time'], 400);
            }
            $lead->followup_scheduled_at = $value ?: null;
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Follow-up scheduled time updated']);
        }

        $validFields = ['carrier', 'policy_type', 'coverage', 'premium', 'initial_draft', 'future_draft'];

        if (!in_array($field, $validFields)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid field'
            ], 400);
        }
        
        // Map field names to database columns
        $fieldMap = [
            'carrier' => 'carrier_name',
            'policy_type' => 'policy_type',
            'coverage' => 'coverage_amount',
            'premium' => 'monthly_premium',
            'initial_draft' => 'initial_draft_date',
            'future_draft' => 'future_draft_date',
        ];
        
        $dbField = $fieldMap[$field];
        
        // Validate numeric fields
        if (in_array($field, ['coverage', 'premium'])) {
            if (!is_numeric($value) || $value < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Value must be a positive number'
                ], 400);
            }
        }

        // Validate date fields
        if (in_array($field, ['initial_draft', 'future_draft'])) {
            if (!strtotime($value)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date value'
                ], 400);
            }
        }

        // Sanity-check policy_type: must be short (plan names are never full customer names)
        if ($field === 'policy_type' && strlen($value) > 60) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid policy type value'
            ], 400);
        }
        
        // Update the field
        $lead->$dbField = $value;
        $lead->save();
        
        // Log the update in audit log if available
        \Log::info("Sales field updated", [
            'lead_id' => $id,
            'field' => $field,
            'value' => $value,
            'updated_by' => auth()->user()->name
        ]);
        
        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);

        // Remove sale data only — do NOT delete the lead record.
        $lead->update([
            'sale_at'         => null,
            'sale_date'       => null,
            'closer_name'     => null,
            'closer_user_id'  => null,
            'carrier_name'    => null,
            'monthly_premium' => null,
            'policy_number'   => null,
            'status'          => 'unassigned',
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale removed successfully. Lead record preserved.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,underwriting,forwarded,chargeback,approved,declined,unassigned'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;
        
        // If status is changed to unassigned, clear the partner assignment
        if ($request->status === 'unassigned') {
            $lead->partner_id = null;
            $lead->assigned_partner = null;
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully.'
        ]);
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'comments' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->comments = $request->comments;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully.',
            'comment' => $lead->comments
        ]);
    }
    
    public function unassignPartner($id)
    {
        $lead = Lead::findOrFail($id);
        
        // Clear partner assignment
        $lead->partner_id = null;
        $lead->assigned_partner = null;
        $lead->partner_set_at = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Partner has been unassigned from this lead successfully.'
        ]);
    }

    public function updateCarrierStatus(Request $request, $carrierId)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected,under-writing',
        ]);

        $carrier = \App\Models\Carrier::findOrFail($carrierId);
        $lead = $carrier->lead;

        if (! $lead) {
            return response()->json(['error' => 'Associated lead not found.'], 404);
        }

        // Ensure the lead is in a state that allows status updates
        if (! in_array($lead->status, [Statuses::LEAD_PENDING, Statuses::LEAD_FORWARDED, Statuses::LEAD_ACTIVE])) {
            return response()->json(['error' => 'Cannot update carrier status for this lead at its current state.'], 403);
        }

        // Check if the carrier belongs to the lead
        if ($carrier->lead_id !== $lead->id) {
            return response()->json(['error' => 'Carrier does not belong to the specified lead.'], 403);
        }

        // Additional permission checks can be added here
        // For example, only certain roles can update carrier statuses

        // Log the status update attempt
        \Log::info('Updating carrier status', [
            'carrier_id' => $carrierId,
            'lead_id' => $lead->id,
            'new_status' => $request->input('status'),
            'updated_by' => auth()->id(),
        ]);

        // Update the carrier status
        $carrier->status = $request->input('status');
        $carrier->save();

        return response()->json(['success' => 'Carrier status updated successfully.']);
    }

    public function import(Request $request)
    {
        \Log::info('=== WEB IMPORT REQUEST RECEIVED ===', [
            'user' => auth()->user()->name ?? 'unknown',
            'has_file' => $request->hasFile('import_file'),
            'file_name' => $request->hasFile('import_file') ? $request->file('import_file')->getClientOriginalName() : 'none',
            'file_size' => $request->hasFile('import_file') ? $request->file('import_file')->getSize() : 0,
            'file_mime' => $request->hasFile('import_file') ? $request->file('import_file')->getMimeType() : 'none',
            'file_extension' => $request->hasFile('import_file') ? $request->file('import_file')->getClientOriginalExtension() : 'none',
        ]);

        // Validate file exists
        if (!$request->hasFile('import_file')) {
            \Log::error('=== WEB IMPORT FAILED: No file uploaded ===');
            return redirect()->back()->with('error', 'No file was uploaded. Please select a file and try again.');
        }

        $file = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['csv', 'xlsx', 'xls'];

        if (!in_array($extension, $allowedExtensions)) {
            \Log::error('=== WEB IMPORT FAILED: Invalid extension ===', ['extension' => $extension]);
            return redirect()->back()->with('error', "Invalid file type '.{$extension}'. Only .csv, .xlsx, .xls files are accepted.");
        }

        // Check file size (100MB max)
        if ($file->getSize() > 100 * 1024 * 1024) {
            \Log::error('=== WEB IMPORT FAILED: File too large ===', ['size' => $file->getSize()]);
            return redirect()->back()->with('error', 'File is too large. Maximum size is 100MB.');
        }

        try {
            $beforeCount = Lead::count();

            AuditLog::logAction('lead_import_started', auth()->user(), null, null, [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ], 'CSV lead import started');

            // Import the file using the LeadsImport class
            $importer = new LeadsImport;
            Excel::import($importer, $request->file('import_file'));

            $afterCount  = Lead::count();
            $created     = $importer->createdCount;
            $updated     = $importer->updatedCount;
            $errors      = $importer->errorCount;

            AuditLog::logAction('lead_import_completed', auth()->user(), null, null, [
                'file_name'    => $file->getClientOriginalName(),
                'leads_before' => $beforeCount,
                'leads_after'  => $afterCount,
                'new_leads'    => $created,
                'duplicates'   => $updated,
                'errors'       => $errors,
            ], "CSV import created {$created} new leads, merged {$updated} duplicates");

            \Log::info('=== WEB IMPORT COMPLETED ===', [
                'before'     => $beforeCount,
                'after'      => $afterCount,
                'created'    => $created,
                'duplicates' => $updated,
                'errors'     => $errors,
            ]);

            $message = "Import complete — {$created} new leads added, {$updated} duplicates merged";
            if ($errors > 0) {
                $message .= ", {$errors} rows skipped due to errors";
            }
            $message .= ". Total leads: {$afterCount}.";

            return redirect()->route('leads.index')->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = "Import validation failed. Errors: ";
            foreach ($failures as $failure) {
                $errorMessage .= "Row {$failure->row()}: " . implode(', ', $failure->errors()) . "; ";
            }
            \Log::error('Lead import validation failed', ['errors' => $errorMessage]);
            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            \Log::error('Lead import failed: ' . $e->getMessage(), [
                'file' => $request->hasFile('import_file') ? $request->file('import_file')->getClientOriginalName() : 'unknown',
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Update QA status for a lead
     */
    public function updateQaStatus(Request $request, $id)
    {
        $request->validate([
            'qa_status' => 'required|in:Pending,Good,Avg,Bad',
            'qa_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        
        $lead->qa_status = $request->qa_status;
        $lead->qa_reason = $request->qa_reason;
        $lead->qa_user_id = auth()->id();
        $lead->qa_reviewed_at = now();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'QA status updated successfully'
        ]);
    }

    /**
     * Update Manager status for a lead
     */
    public function updateManagerStatus(Request $request, $id)
    {
        $request->validate([
            'submission_status' => 'required|in:pending,approved,declined,underwriting',
            'submission_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        
        $lead->submission_status = $request->submission_status;
        $lead->submission_reason = $request->submission_reason;
        $lead->submission_by = auth()->id();
        $lead->submission_at = now();
        
        // When manager approves, update status to accepted/underwritten
        if ($request->submission_status === Statuses::SUB_APPROVED) {
            $lead->status = Statuses::LEAD_ACCEPTED;
        }
        elseif ($request->submission_status === Statuses::SUB_UNDERWRITING) {
            $lead->status = Statuses::LEAD_UNDERWRITTEN;
        }
        // When manager declines, update main status so it moves to Failed Leads section
        elseif ($request->submission_status === 'declined') {
            $lead->status      = 'declined';
            $lead->declined_at = now();
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Manager status updated successfully'
        ]);
    }

    /**
     * Assign Back — reset a declined/invalid sale to Ravens queue for closer callback.
     * Clears validation state so the lead re-appears in Ravens Pending Validation,
     * and stamps recall_requested_at so ravens can see the "Callback" badge.
     */
    public function assignBack(Request $request, $id)
    {
        $request->validate([
            'recall_note' => 'nullable|string|max:500',
        ]);

        $lead = Lead::findOrFail($id);

        $lead->recall_requested_at     = now();
        $lead->recall_requested_by     = auth()->id();
        $lead->recall_note             = $request->recall_note;

        // Reset validation so the lead re-enters the Ravens pending queue
        $lead->ravens_validated_at      = null;
        $lead->ravens_validation_status = null;

        // Reset manager decision back to pending
        $lead->submission_status           = Statuses::SUB_PENDING;
        $lead->submission_reason           = null;

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => "Lead assigned back for callback — it will reappear in Ravens validation queue.",
        ]);
    }

    /**
     * Pending Contract page (formerly Issuance / Policy Submission).
     * (pending_contract_at IS NOT NULL). These are actively being submitted to carriers.
     */
    public function issuance(Request $request)
    {
        // Default to current month if no date filter is applied
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'date_from' => now()->startOfMonth()->toDateString(),
                'date_to'   => now()->endOfMonth()->toDateString(),
            ]);
        }

        // Only show leads that have been explicitly moved to Pending Contract
        $query = Lead::with(['insuranceCarrier', 'partner', 'issuedByUser', 'followupAssignedByUser'])
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereNotNull('pending_contract_at');  // Stage gate: must be sent from Submissions
        
        // Search functionality — when searching, bypass date/carrier/status filters
        // so users can find any lead by policy number, name, phone etc. across all time
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%")
                  ->orWhere('app_id', 'like', "%{$search}%");
            });
            // Skip all other filters when searching
            goto build_view;
        }
        
        // Filter by carrier
        if ($request->filled('carrier')) {
            $query->where('insurance_carrier_id', $request->carrier);
        }

        // Filter by issuance status
        if ($request->filled('issuance_status')) {
            if ($request->issuance_status === 'pending') {
                // Handle pending filter - includes NULL, Pending, Incomplete
                $query->where(function($q) {
                    $q->whereNull('issuance_status')
                      ->orWhere('issuance_status', 'Pending')
                      ->orWhere('issuance_status', 'Incomplete');
                });
            } elseif ($request->issuance_status === 'Issued') {
                // Issued filter - exclude leads that have been sent to draft
                $query->where('issuance_status', 'Issued')
                      ->whereNull('pending_draft_at');
            } else {
                $query->where('issuance_status', $request->issuance_status);
            }
        }
        
        // Filter by followup status
        if ($request->filled('followup_status')) {
            $query->where('followup_status', $request->followup_status);
        }
        
        // Filter by policy type
        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }
        
        // Date range filter for sale_date
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        build_view:
        
        // Get unique carriers for filter dropdown from InsuranceCarrier model
        $carriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Get all active partners for dropdown
        $partners = \App\Models\Partner::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
        
        // Get all users for followup assignment dropdown
        // Partners are managed separately in the Partner system
        $followupUsers = \App\Models\User::orderBy('name')->get(['id', 'name']);
        
        // Get Not Issued dispositions for the modal
        $niDispositions = Statuses::NOT_ISSUED_DISPOSITIONS;

        // Base date-scoped query for all KPIs (consistent date filter)
        $baseKpiQuery = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereNotNull('pending_contract_at');

        if ($request->filled('date_from')) {
            $baseKpiQuery->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseKpiQuery->whereDate('sale_date', '<=', $request->date_to);
        }

        // Sent-to-draft KPI: date-scoped, only leads that were actually issued then drafted
        // (Not Issued leads cannot be sent to draft — excluded by issuance_status check)
        $sentToDraftKpi = (clone $baseKpiQuery)
            ->whereNotNull('pending_draft_at')
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->count();

        // Sent-to-draft list for the detail table (date-scoped)
        $sentToDraft = (clone $baseKpiQuery)
            ->with(['pendingDraftBy'])
            ->whereNotNull('pending_draft_at')
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->orderByDesc('pending_draft_at')
            ->get(['id', 'cn_name', 'phone_number', 'pending_draft_at', 'pending_draft_by_id', 'sale_date']);

        // Non-drafted leads KPI base (pending/issued/not-issued counters)
        $baseActivKpiQuery = (clone $baseKpiQuery)->whereNull('pending_draft_at');

        $kpiCounts = [
            'pending' => (clone $baseActivKpiQuery)->where(function($q) {
                $q->whereNull('issuance_status')
                  ->orWhere('issuance_status', 'Pending')
                  ->orWhere('issuance_status', 'Incomplete');
            })->count(),
            'issued' => (clone $baseActivKpiQuery)->where('issuance_status', Statuses::ISSUANCE_ISSUED)->count(),
            'not_issued' => (clone $baseActivKpiQuery)->where('issuance_status', 'Not Issued')->count(),
            'ready_for_draft' => (clone $baseActivKpiQuery)
                ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
                ->where('followup_status', Statuses::MIS_YES)
                ->count(),
            'sent_to_draft' => $sentToDraftKpi,
        ];
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        return view('admin.issuance.index', compact('leads', 'carriers', 'partners', 'followupUsers', 'sentToDraft', 'niDispositions', 'kpiCounts'));
    }

    /**
     * Update issuance status
     */
    public function updateIssuanceStatus(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        
        // Check if status has already been set (edit-once logic) - only Super Admin can change
        // Allow Pending status to bypass this check (for unassigning partner)
        if (!empty($lead->issuance_date) && !auth()->user()->hasRole(Roles::SUPER_ADMIN) && $request->issuance_status !== Statuses::ISSUANCE_PENDING) {
            return back()->with('error', 'Issuance status has already been set and can only be changed by Super Admin.');
        }
        
        // Convert empty partner_id to null for easier handling
        if (empty($request->partner_id)) {
            $request->merge(['partner_id' => null]);
        }
        
        // Validate partner_id only if status is not Pending
        $partnerValidation = $request->issuance_status === Statuses::ISSUANCE_PENDING 
            ? 'nullable' 
            : 'required|integer|exists:partners,id';
        
        $request->validate([
            'issuance_status' => 'required|in:Issued,Not Issued,Pending',
            'issuance_reason' => 'nullable|string|max:1000',
            'issued_policy_number' => 'nullable|string|max:255',
            'partner_id' => 'nullable|integer|exists:partners,id'
        ]);

        $commissionService = new CommissionCalculationService();
        
        // Check if policy number has already been set (edit-once logic)
        if (!empty($lead->policy_number_set_at) && $request->issued_policy_number != $lead->issued_policy_number) {
            if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
                return back()->with('error', 'Policy number has already been set and cannot be changed.');
            }
        }
        
        // Check if assigned partner has already been set (edit-once logic)
        // Allow Pending status to bypass this check (for unassigning partner)
        if (!empty($lead->partner_set_at) && $request->partner_id != $lead->partner_id && $request->issuance_status !== 'Pending') {
            if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
                return back()->with('error', 'Assigned partner has already been set and cannot be changed.');
            }
        }
        
        $lead->issuance_status = $request->issuance_status;
        $lead->issuance_reason = $request->issuance_reason;
        $lead->issued_by = auth()->id();
        
        // Set policy number and timestamp if not already set
        if (empty($lead->policy_number_set_at)) {
            $lead->issued_policy_number = $request->issued_policy_number;
            $lead->policy_number_set_at = now();
        }
        
        // Handle Pending status - unassign partner
        if ($request->issuance_status === Statuses::ISSUANCE_PENDING) {
            $lead->partner_id = null;
            $lead->partner_set_at = null;
            $lead->issuance_date = null; // Clear the issued date when marking as Pending
        } else {
            // Set assigned partner and timestamp if not already set (for non-Pending statuses)
            if (empty($lead->partner_set_at)) {
                $lead->partner_id = $request->partner_id;
                $lead->partner_set_at = now();
            }
        }
        
        if ($request->issuance_status === Statuses::ISSUANCE_ISSUED) {
            $lead->issuance_date = now();
            
            // Calculate commission when status is Issued and partner is assigned
            if ($lead->partner_id && $lead->monthly_premium > 0) {
                // Get carrier ID - lookup by name if insurance_carrier_id is not set
                $carrierId = $lead->insurance_carrier_id;
                if (!$carrierId && $lead->carrier_name) {
                    $carrier = \App\Models\InsuranceCarrier::where('name', $lead->carrier_name)->first();
                    if ($carrier) {
                        $carrierId = $carrier->id;
                        // Update lead with carrier ID for future reference
                        $lead->insurance_carrier_id = $carrierId;
                    }
                }
                
                if ($carrierId) {
                    // Get settlement type from lead (convert policy_type to settlement type if needed)
                    $settlementType = $this->getSettlementType($lead->settlement_type ?? $lead->policy_type);
                    
                    // Calculate commission using the service
                    $commissionResult = $commissionService->calculateCommission(
                        partnerId: $lead->partner_id,
                        carrierId: $carrierId,
                        state: $lead->state ?? 'Unknown',
                        settlementType: $settlementType,
                        monthlyPremium: (float) $lead->monthly_premium
                    );
                    
                    if ($commissionResult['success']) {
                        $lead->agent_commission = $commissionResult['commission'];
                        $lead->agent_revenue = $commissionResult['commission']; // Can be adjusted later if needed
                        $lead->settlement_percentage = $commissionResult['settlement_pct'];
                        $lead->commission_calculation_notes = $commissionResult['message'];
                        $lead->commission_calculated_at = now();
                    } else {
                        // Log warning but don't block issuance
                        $lead->commission_calculation_notes = 'Failed: ' . $commissionResult['message'];
                    }
                } else {
                    $lead->commission_calculation_notes = 'Failed: Carrier not found in system';
                }
            }
        }
        
        $lead->save();

        return redirect()->route('issuance.index')->with('success', 'Issuance status updated successfully');
    }

    /**
     * Send a lead to Pending Draft (requires Issued status and Followup Yes)
     */
    public function sendToPendingDraft(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);
        
        // Validate conditions
        if ($lead->issuance_status !== Statuses::ISSUANCE_ISSUED) {
            return response()->json([
                'success' => false,
                'message' => 'Lead must be Issued first.'
            ], 422);
        }
        
        if ($lead->followup_status !== Statuses::MIS_YES) {
            return response()->json([
                'success' => false,
                'message' => 'Followup must be Yes.'
            ], 422);
        }
        
        // Set pending draft timestamp
        $lead->pending_draft_at = now();
        $lead->pending_draft_by_id = auth()->id();
        $lead->followup_done_at = now();
        $lead->followup_done_by_id = auth()->id();
        $lead->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Lead sent to Pending Draft successfully.'
        ]);
    }

    /**
     * Mark a lead as Issued from Pending Contracts page.
     * Simple action that sets issuance_status to Issued.
     */
    public function markAsIssued(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);
        
        // Don't allow if already issued
        if ($lead->issuance_status === Statuses::ISSUANCE_ISSUED) {
            return response()->json([
                'success' => false,
                'message' => 'Lead is already marked as Issued.'
            ], 422);
        }
        
        $lead->issuance_status = Statuses::ISSUANCE_ISSUED;
        $lead->issuance_date = now();
        $lead->issued_by = auth()->id();
        $lead->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Issued successfully.'
        ]);
    }

    /**
     * Mark a lead as Not Issued from Pending Contracts page.
     * Sets disposition and sends to retention for resolution.
     */
    public function markAsNotIssued(Request $request, int $id)
    {
        $request->validate([
            'not_issued_disposition' => 'required|in:' . implode(',', array_keys(Statuses::NOT_ISSUED_DISPOSITIONS)),
            'not_issued_comment'     => 'nullable|string|max:500|required_if:not_issued_disposition,' . Statuses::NI_OTHER_REASON,
        ]);
        
        $lead = Lead::findOrFail($id);
        
        $lead->issuance_status = 'Not Issued';
        $lead->not_issued_disposition = $request->not_issued_disposition;
        $lead->not_issued_comment = ($request->not_issued_disposition === Statuses::NI_OTHER_REASON)
            ? trim($request->not_issued_comment)
            : null;
        $lead->not_issued_at = now();
        $lead->not_issued_by_id = auth()->id();
        $lead->not_issued_resolved_at = null;
        $lead->not_issued_resolved_by_id = null;
        $lead->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Not Issued. Sent to Retention.',
            'disposition_label' => Statuses::NOT_ISSUED_DISPOSITIONS[$request->not_issued_disposition],
        ]);
    }

    /**
     * Send a lead back to the previous pipeline stage.
     * Determines current stage automatically and clears appropriate fields.
     * 
     * Pipeline stages (in order):
     * 1. Sales (base)
     * 2. Ravens Validation
     * 3. Submissions (Pendings Approved)
     * 4. Pending Contracts
     * 5. Pending Draft
     * 6. Paid Sales
     */
    public function sendToPreviousStage(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);
        $currentStage = $this->determineCurrentStage($lead);
        
        switch ($currentStage) {
            case 'paid_sales':
                // Back to Pending Draft
                $lead->paid_at = null;
                $lead->paid_by_id = null;
                // Clear not-paid retention flags so lead leaves Retention page
                $lead->not_paid_at = null;
                $lead->not_paid_by_id = null;
                $lead->not_paid_fdfp_type = null;
                $lead->not_paid_manual_disposition = null;
                $previousStage = 'Pending Draft';
                break;
                
            case 'pending_draft':
                // Back to Followup (clear followup_done_at)
                $lead->followup_done_at = null;
                $lead->followup_done_by_id = null;
                $lead->pending_draft_at = null;
                $lead->pending_draft_by_id = null;
                // Clear FDFP/not paid fields if any
                $lead->not_paid_at = null;
                $lead->not_paid_by_id = null;
                $lead->not_paid_fdfp_type = null;
                $lead->not_paid_manual_disposition = null;
                $previousStage = 'Followup';
                break;
                
            case 'followup':
                // Back to Pending Contracts (clear followup assignment and issuance)
                $lead->assigned_followup_person = null;
                $lead->followup_assigned_by = null;
                $lead->followup_assigned_at = null;
                $lead->issuance_status = Statuses::ISSUANCE_PENDING;
                $lead->issuance_date = null;
                $lead->followup_status = 'No';
                // Clear not-issued retention flags so lead leaves Retention page
                $lead->not_issued_at = null;
                $lead->not_issued_by_id = null;
                $lead->not_issued_disposition = null;
                $lead->not_issued_resolved_at = null;
                $previousStage = 'Pending Contracts';
                break;
                
            case 'pending_contracts':
                // Back to Submissions - clear all pending contract stage fields
                $lead->pending_contract_at = null;
                $lead->pending_contract_by_id = null;
                // Clear issuance fields
                $lead->issuance_status = Statuses::ISSUANCE_PENDING;
                $lead->issuance_date = null;
                $lead->issuance_reason = null;
                $lead->issued_by = null;
                // Clear issued policy number and partner assignment
                $lead->issued_policy_number = null;
                $lead->policy_number_set_at = null;
                $lead->partner_id = null;
                $lead->partner_set_at = null;
                // Clear followup assignment
                $lead->assigned_followup_person = null;
                $lead->followup_assigned_by = null;
                $lead->followup_assigned_at = null;
                $lead->followup_status = 'No';
                // Clear not-issued retention flags so lead leaves Retention page
                $lead->not_issued_at = null;
                $lead->not_issued_by_id = null;
                $lead->not_issued_disposition = null;
                $lead->not_issued_resolved_at = null;
                // Reset submission to pending so it shows in Submissions
                $lead->submission_status = Statuses::SUB_PENDING;
                $lead->submission_by = null;
                $lead->submission_at = null;
                $lead->submission_reason = null;
                $previousStage = 'Submissions';
                break;
                
            case 'submissions':
                // Back to Ravens Validation - clear all submission stage fields
                $lead->ravens_validation_status = null;
                $lead->ravens_validated_at = null;
                $lead->ravens_validated_by = null;
                $lead->submission_status = Statuses::SUB_PENDING;
                $lead->submission_reason = null;
                $lead->submission_by = null;
                $lead->submission_at = null;
                $lead->app_id = null;
                $lead->policy_number = null;
                $lead->assigned_partner = null;
                $lead->partner_id = null;
                $lead->partner_set_at = null;
                $previousStage = 'Ravens Validation';
                break;
                
            case 'ravens_validation':
                // Back to Sales
                $lead->ravens_validated_at = null;
                $lead->ravens_validated_by = null;
                $lead->ravens_validation_status = null;
                $previousStage = 'Sales';
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Lead is already at the first stage (Sales).'
                ], 422);
        }
        
        $lead->save();
        
        // Log the action
        \App\Models\AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => "Sent lead back to {$previousStage}",
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['stage' => $currentStage]),
            'new_values' => json_encode(['stage' => $previousStage]),
            'ip_address' => $request->ip(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Lead sent back to {$previousStage}."
        ]);
    }
    
    /**
     * Determine the current pipeline stage for a lead.
     */
    private function determineCurrentStage(Lead $lead): string
    {
        if ($lead->paid_at) {
            return 'paid_sales';
        }
        if ($lead->followup_done_at || $lead->pending_draft_at) {
            return 'pending_draft';
        }
        // Followup stage: Issued AND has a followup person assigned (actively being followed up)
        if ($lead->issuance_status === Statuses::ISSUANCE_ISSUED && $lead->assigned_followup_person) {
            return 'followup';
        }
        // Pending Contracts: has been sent from Submissions (pending_contract_at is set)
        // Includes both pending and issued leads that haven't been assigned for followup yet
        if ($lead->pending_contract_at) {
            return 'pending_contracts';
        }
        if ($lead->ravens_validation_status === 'valid') {
            return 'submissions';
        }
        if ($lead->ravens_validated_at) {
            return 'ravens_validation';
        }
        return 'sales';
    }

    /**
     * QA Review page - clone of sales management for QA users
     */
    public function qaReview(Request $request)
    {
        // Default to current month if no date filter is applied
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'date_from' => now()->startOfMonth()->toDateString(),
                'date_to'   => now()->endOfMonth()->toDateString(),
            ]);
        }

        // QA Review section - show all sales that have been made by closers
        // Sales are leads that have a closer assigned and sale timestamp
        // Exclude incomplete leads (verifier-only forms) that lack actual sale data
        $query = Lead::with(['insuranceCarrier', 'qaUser'])
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->whereNotNull('sale_at')
            ->whereNull('rewrite_sent_back_at') // hide rewrite sales sent back to retention
            ->where(function($q) {
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            });
        
        // Build analytics query (no eager loading needed — counts only)
        $analyticsQuery = Lead::whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->whereNotNull('sale_at')
            ->whereNull('rewrite_sent_back_at')
            ->where(function($q) {
                $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            });
        
        // Apply all filters to analytics query (except qa_status to see breakdown of all statuses)
        if ($request->filled('search')) {
            $search = $request->search;
            $analyticsQuery->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('carrier')) {
            $analyticsQuery->where('carrier_name', $request->carrier);
        }

        if ($request->filled('date_from')) {
            $analyticsQuery->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $analyticsQuery->whereDate('sale_date', '<=', $request->date_to);
        }
        
        // Single query for all QA analytics counts instead of 5 separate COUNT queries
        $qaAgg = $analyticsQuery->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN qa_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN qa_status = 'Good' THEN 1 ELSE 0 END) as good_count,
            SUM(CASE WHEN qa_status = 'Avg' THEN 1 ELSE 0 END) as avg_count,
            SUM(CASE WHEN qa_status = 'Bad' THEN 1 ELSE 0 END) as bad_count
        ")->first();
        
        $total = (int) ($qaAgg->total ?? 0);
        $qaAnalytics = [
            'total' => $total,
            'pending' => (int) ($qaAgg->pending_count ?? 0),
            'good' => (int) ($qaAgg->good_count ?? 0),
            'avg' => (int) ($qaAgg->avg_count ?? 0),
            'bad' => (int) ($qaAgg->bad_count ?? 0),
        ];
        
        // Calculate percentages
        $qaAnalytics['pending_percent'] = $total > 0 ? round(($qaAnalytics['pending'] / $total) * 100) : 0;
        $qaAnalytics['good_percent'] = $total > 0 ? round(($qaAnalytics['good'] / $total) * 100) : 0;
        $qaAnalytics['avg_percent'] = $total > 0 ? round(($qaAnalytics['avg'] / $total) * 100) : 0;
        $qaAnalytics['bad_percent'] = $total > 0 ? round(($qaAnalytics['bad'] / $total) * 100) : 0;
        $qaAnalytics['issues_percent'] = $total > 0 ? 
            round((($qaAnalytics['avg'] + $qaAnalytics['bad']) / $total) * 100) : 0;
        
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
        
        // Filter by QA status
        if ($request->filled('qa_status')) {
            $query->where('qa_status', $request->qa_status);
        }
        
        // Filter by closer
        if ($request->filled('closer')) {
            $query->where('closer_name', $request->closer);
        }
        
        // Date range filter for sale_date
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        // Get insurance carriers that have partner assignments (like Sales page)
        $insuranceCarriers = \App\Models\InsuranceCarrier::whereHas('agentStates')
            ->orderBy('name')
            ->pluck('name');
        if ($insuranceCarriers->isEmpty()) {
            $insuranceCarriers = \App\Models\InsuranceCarrier::orderBy('name')->pluck('name');
        }
        $carriers = $insuranceCarriers;

        // --- Chart data: QA status per closer (top 10) ---
        $closerStats = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->select('closer_name')
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN qa_status = 'Good' THEN 1 ELSE 0 END) as good")
            ->selectRaw("SUM(CASE WHEN qa_status = 'Pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN qa_status IN ('Avg','Bad') THEN 1 ELSE 0 END) as issues")
            ->groupBy('closer_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // --- Chart data: QA status per carrier (top 8) ---
        $carrierStats = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereNotNull('carrier_name')
            ->where('carrier_name', '!=', '')
            ->select('carrier_name')
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN qa_status = 'Good' THEN 1 ELSE 0 END) as good")
            ->selectRaw("SUM(CASE WHEN qa_status = 'Pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN qa_status IN ('Avg','Bad') THEN 1 ELSE 0 END) as issues")
            ->groupBy('carrier_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // --- Chart data: Daily trend (last 14 days) ---
        $dailyTrend = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereNotNull('sale_date')
            ->where('sale_date', '>=', now()->subDays(13)->toDateString())
            ->selectRaw("DATE(sale_date) as day")
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN qa_status = 'Good' THEN 1 ELSE 0 END) as good")
            ->selectRaw("SUM(CASE WHEN qa_status IN ('Avg','Bad') THEN 1 ELSE 0 END) as issues")
            ->groupByRaw("DATE(sale_date)")
            ->orderBy('day')
            ->get();

        // Get unique closer names for closer filter
        $closers = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->distinct()
            ->orderBy('closer_name')
            ->pluck('closer_name');

        // Order by sale_date, then sale_at, then created_at (fallback)
        $leads = $query->orderByRaw('COALESCE(sale_date, sale_at, created_at) DESC')->paginate(50);
        return view('admin.qa.review', compact('leads', 'carriers', 'qaAnalytics', 'closerStats', 'carrierStats', 'dailyTrend', 'closers'));
    }

    /**
     * Mark a chargeback as a retention sale (when retention officer makes a sale)
     * Automatically determines if it's retained (<30 days) or rewrite (>=30 days)
     */
    public function markRetentionSale(Request $request, $id)
    {
        $request->validate([
            'sale_date' => 'required|date',
        ]);

        $lead = Lead::findOrFail($id);
        
        // Verify it's a chargeback
        if ($lead->status !== Statuses::LEAD_CHARGEBACK) {
            return response()->json([
                'success' => false,
                'message' => 'This lead is not a chargeback.'
            ], 400);
        }

        $retentionOfficer = auth()->user()->name;
        $chargebackDate = \Carbon\Carbon::parse($lead->chargeback_marked_date);
        $saleDate = \Carbon\Carbon::parse($request->sale_date);
        $daysDifference = $chargebackDate->diffInDays($saleDate);

        // Determine if retained (<30 days) or rewrite (>=30 days)
        $isRetained = $daysDifference < 30;

        // Create a new sale record
        $newSale = $lead->replicate();
        $newSale->closer_name = $retentionOfficer;
        $newSale->sale_at = $saleDate;
        $newSale->sale_date = $saleDate->format('Y-m-d');
        $newSale->status = Statuses::LEAD_PENDING;
        $newSale->retention_status = null;
        $newSale->is_rewrite = !$isRetained; // If not retained, it's a rewrite
        $newSale->chargeback_marked_date = null;
        $newSale->qa_status = Statuses::QA_PENDING;
        $newSale->qa_reason = null;
        $newSale->qa_user_id = null;
        $newSale->submission_status = Statuses::SUB_PENDING;
        $newSale->submission_reason = null;
        $newSale->submission_by = null;
        $newSale->comments = ($isRetained ? 'Retained' : 'Rewritten') . " from chargeback by {$retentionOfficer} ({$daysDifference} days after chargeback)";
        $newSale->save();

        // Update the original chargeback
        $lead->retention_status = $isRetained ? Statuses::RETENTION_RETAINED : Statuses::RETENTION_REWRITE;
        $lead->retained_at = $isRetained ? now() : null;
        $lead->is_rewrite = !$isRetained;
        $lead->retention_officer_id = auth()->id();
        $lead->status = $isRetained ? Statuses::LEAD_ACCEPTED : Statuses::LEAD_CHARGEBACK;
        $lead->save();

        // Dispatch event to notify managers and assigned person about the new sale
        event(new SaleCreated($newSale, $retentionOfficer));

        return response()->json([
            'success' => true,
            'message' => $isRetained 
                ? "Sale marked as RETAINED ({$daysDifference} days). New sale created." 
                : "Sale marked as REWRITE ({$daysDifference} days). New sale created for approval.",
            'type' => $isRetained ? 'retained' : 'rewrite',
            'days' => $daysDifference
        ]);
    }

    /**
     * Update only the manager reason field
     */
    public function updateManagerReason(Request $request, $id)
    {
        $request->validate([
            'submission_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->submission_reason = $request->submission_reason;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Manager reason updated successfully'
        ]);
    }

    /**
     * Pretty print view for a sale
     */
    public function prettyPrint($id)
    {
        $lead = Lead::with([
            'insuranceCarrier',
            'verifier',
            'validator',
            'assignedValidator',
            'assignedCloser'
        ])->findOrFail($id);
        
        return view('admin.sales.pretty-print', compact('lead'));
    }

    /**
     * Reset QA status for a lead (Super Admin only)
     */
    public function resetQaStatus(Request $request, $id)
    {
        // Check if user is Super Admin
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can reset QA status.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        
        $lead->qa_status = Statuses::QA_PENDING;
        $lead->qa_reason = null;
        $lead->qa_user_id = null;
        $lead->qa_reviewed_at = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'QA status has been reset to Pending by Super Admin.'
        ]);
    }

    /**
     * Reset Manager status for a lead (Super Admin only)
     */
    public function resetManagerStatus(Request $request, $id)
    {
        // Check if user is Super Admin
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can reset Manager status.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        
        $lead->submission_status = Statuses::SUB_PENDING;
        $lead->submission_reason = null;
        $lead->submission_by = null;
        $lead->submission_at = null;
        // If it was marked as chargeback, revert those changes too
        if ($lead->status === Statuses::LEAD_CHARGEBACK) {
            $lead->status = Statuses::LEAD_PENDING;
            $lead->chargeback_marked_date = null;
            $lead->retention_status = null;
        }
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Manager status has been reset to Pending by Super Admin.'
        ]);
    }

    /**
     * Reset Issuance status for a lead (Super Admin only)
     */
    public function resetIssuanceStatus(Request $request, $id)
    {
        // Check if user is Super Admin
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can reset Issuance status.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        
        $lead->issuance_status = null;
        $lead->issuance_reason = null;
        $lead->issued_policy_number = null;
        $lead->assigned_agent_id = null;
        $lead->partner_id = null;
        $lead->issued_by = null;
        $lead->policy_number_set_at = null;
        $lead->assigned_agent_set_at = null;
        $lead->partner_set_at = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Issuance status has been reset by Super Admin.'
        ]);
    }

    /**
     * Unlock a specific issuance field (Super Admin only)
     */
    public function unlockIssuanceField(Request $request, $id)
    {
        // Check if user is Super Admin
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can unlock issuance fields.'
            ], 403);
        }

        $request->validate([
            'field' => 'required|in:policy_number,partner,status'
        ]);

        $lead = Lead::findOrFail($id);
        $field = $request->field;

        // Clear the lock timestamp for the requested field
        if ($field === 'policy_number') {
            $lead->policy_number_set_at = null;
        } elseif ($field === 'partner') {
            $lead->partner_set_at = null;
        } elseif ($field === 'status') {
            $lead->issuance_date = null;
            $lead->issuance_status = 'unverified';
        }

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' has been unlocked. You can now edit it.'
        ]);
    }
    
    /**
     * Recalculate commission for a specific lead (Super Admin only)
     */
    public function recalculateCommission($id)
    {
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can recalculate commission.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        $commissionService = new CommissionCalculationService();
        
        if ($lead->issuance_status === Statuses::ISSUANCE_ISSUED && $lead->partner_id && $lead->monthly_premium > 0) {
            // Get carrier ID - lookup by name if insurance_carrier_id is not set
            $carrierId = $lead->insurance_carrier_id;
            if (!$carrierId && $lead->carrier_name) {
                $carrier = \App\Models\InsuranceCarrier::where('name', $lead->carrier_name)->first();
                if ($carrier) {
                    $carrierId = $carrier->id;
                    $lead->insurance_carrier_id = $carrierId;
                }
            }
            
            if ($carrierId) {
                $settlementType = $this->getSettlementType($lead->settlement_type ?? $lead->policy_type);
                
                $commissionResult = $commissionService->calculateCommission(
                    partnerId: $lead->partner_id,
                    carrierId: $carrierId,
                    state: $lead->state ?? 'Unknown',
                    settlementType: $settlementType,
                    monthlyPremium: (float) $lead->monthly_premium
                );
                
                if ($commissionResult['success']) {
                    $lead->agent_commission = $commissionResult['commission'];
                    $lead->agent_revenue = $commissionResult['commission'];
                    $lead->settlement_percentage = $commissionResult['settlement_pct'];
                    $lead->commission_calculation_notes = $commissionResult['message'];
                    $lead->commission_calculated_at = now();
                    $lead->save();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Commission recalculated successfully',
                        'data' => [
                            'agent_commission' => $lead->agent_commission,
                            'agent_revenue' => $lead->agent_revenue,
                            'settlement_percentage' => $lead->settlement_percentage,
                            'notes' => $lead->commission_calculation_notes
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Calculation failed: ' . $commissionResult['message']
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Carrier not found in system'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Lead must be issued with agent assigned and premium set'
            ]);
        }
    }
    
    /**
     * Bulk recalculate commission for all issued leads (Super Admin only)
     */
    public function bulkRecalculateCommission()
    {
        if (!auth()->user()->hasRole(Roles::SUPER_ADMIN)) {
            return redirect()->back()->with('error', 'Only Super Admin can recalculate commissions.');
        }

        $commissionService = new CommissionCalculationService();
        
        // Use chunking instead of loading ALL leads into memory at once
        // Pre-load carrier name→id map to avoid N+1 queries inside the loop
        $carrierMap = \App\Models\InsuranceCarrier::pluck('id', 'name')->toArray();
        
        $processed = 0;
        $failed = 0;
        
        Lead::where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->whereNotNull('partner_id')
            ->where('monthly_premium', '>', 0)
            ->chunk(100, function ($leads) use ($commissionService, $carrierMap, &$processed, &$failed) {
                foreach ($leads as $lead) {
                    // Get carrier ID — use pre-loaded map instead of per-lead query
                    $carrierId = $lead->insurance_carrier_id;
                    if (!$carrierId && $lead->carrier_name) {
                        $carrierId = $carrierMap[$lead->carrier_name] ?? null;
                        if ($carrierId) {
                            $lead->insurance_carrier_id = $carrierId;
                        }
                    }
                    
                    if ($carrierId) {
                        $settlementType = $this->getSettlementType($lead->settlement_type ?? $lead->policy_type);
                        
                        $commissionResult = $commissionService->calculateCommission(
                            partnerId: (int) $lead->partner_id,
                            carrierId: $carrierId,
                            state: $lead->state ?? 'Unknown',
                            settlementType: $settlementType,
                            monthlyPremium: (float) $lead->monthly_premium
                        );
                        
                        if ($commissionResult['success']) {
                            $lead->agent_commission = $commissionResult['commission'];
                            $lead->agent_revenue = $commissionResult['commission'];
                            $lead->settlement_percentage = $commissionResult['settlement_pct'];
                            $lead->commission_calculation_notes = $commissionResult['message'];
                            $lead->commission_calculated_at = now();
                            $lead->save();
                            $processed++;
                        } else {
                            $failed++;
                        }
                    } else {
                        $failed++;
                    }
                }
            });
        
        return redirect()->back()->with('success', "Recalculated commissions for {$processed} leads. {$failed} failed.");
    }
    
    /**
     * Helper method to convert policy type to settlement type for commission calculation
     */
    private function getSettlementType($policyType)
    {
        if (!$policyType) {
            return 'level'; // Default
        }
        
        // Normalize the policy type to settlement type format
        $normalized = strtolower(trim($policyType));
        
        // Map common variations
        $mapping = [
            'g.i' => 'gi',
            'gi' => 'gi',
            'guaranteed issue' => 'gi',
            'graded' => 'graded',
            'level' => 'level',
            'modified' => 'modified',
            'term' => 'level',
            'whole life' => 'level',
            'universal' => 'level',
        ];
        
        return $mapping[$normalized] ?? 'level';
    }
}
