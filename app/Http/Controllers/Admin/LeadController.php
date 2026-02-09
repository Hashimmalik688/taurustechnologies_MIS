<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Events\LeadCreated;
use App\Events\SaleCreated;
use App\Services\CommissionCalculationService;
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
        // All Leads section - Only show leads closed by Paraguin closers or accepted by managers
        // Verifier forms (pending) and declined/transferred leads are excluded
        $query = Lead::query();
        
        // Only show closed and accepted leads
        $query->whereIn('status', ['closed', 'accepted']);
        
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
        
        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        
        $leads = $query->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.leads.index_simple', compact('leads'));
    }

    public function sales(Request $request)
    {
        // Sales section - show all sales that have been made by closers
        // Sales are leads that have a closer assigned and sale timestamp
        $query = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
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
        
        // Filter by status
        // Note: 'pending' filter includes both 'pending' and 'sale' status
        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->whereIn('status', ['pending', 'sale']);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Filter by policy type
        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }
        
        // Month filter for sale_date
        if ($request->filled('month')) {
            $query->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('sale_date', $request->year);
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
        
        // Get KPI statistics for manager_status (Sales Management uses manager_status, not status)
        $statusCounts = [
            'pending' => Lead::whereNotNull('closer_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->where('manager_status', 'pending')->count(),
            'accepted' => Lead::whereNotNull('closer_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->where('manager_status', 'approved')->count(),
            'rejected' => Lead::whereNotNull('closer_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->where('manager_status', 'declined')->count(),
            'underwritten' => Lead::whereNotNull('closer_name')
                ->where(function($q) {
                    $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
                })
                ->where('manager_status', 'underwriting')->count(),
        ];
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        $statusColors = $this->getStatusColors();
        return view('admin.sales.index', compact('leads', 'carriers', 'insuranceCarriers', 'statusCounts', 'statusColors'));
    }

    /**
     * Store manually entered sales record
     */
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
            'status' => 'nullable|string|in:pending,accepted,rejected,chargeback,verified',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ]);

        try {
            $lead = Lead::create([
                ...$validated,
                'sale_at' => $validated['sale_date'],
                'status' => $validated['status'] ?? 'accepted',
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
            $lead->doctor_name = $request->input('doctor_name');
            $lead->policy_type = $request->input('policy_type');
            $lead->coverage_amount = $request->input('coverage_amount');
            $lead->monthly_premium = $request->input('monthly_premium');
            $lead->carrier_name = $request->input('carrier_name');
            $lead->beneficiary = $request->input('beneficiary');
            $lead->smoker = $request->input('smoker');
            $lead->status = 'pending';
            $lead->updated_at = now();

            if ($request->input('action') === 'forward') {
                $lead->status = 'forwarded';
                $lead->forwarded_by = auth()->id();
            } else {
                $lead->status = 'active';
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
            $lead->status = 'forwarded';  // Or another status that makes sense in your workflow
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

        return view('admin.leads.edit', compact('lead'));
    }

    public function update(UpdateLeadRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $lead->update($request->validated());

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
        $validFields = ['carrier', 'policy_type', 'coverage', 'premium'];
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
            'premium' => 'monthly_premium'
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
        Lead::destroy($id);

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
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
        if (! in_array($lead->status, ['pending', 'forwarded', 'active'])) {
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
        // Validate the request - Allow up to 50MB files for large bulk imports
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv|max:51200',
        ]);

        try {
            $beforeCount = Lead::count();
            
            // Import the file using the LeadsImport class
            Excel::import(new LeadsImport, $request->file('import_file'));

            $afterCount = Lead::count();
            $imported = $afterCount - $beforeCount;

            return redirect()->route('leads.index')->with('success', "Successfully imported {$imported} leads! Total leads: {$afterCount}");
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
                'file' => $request->file('import_file')->getClientOriginalName(),
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
            'manager_status' => 'required|in:pending,approved,declined,underwriting,chargeback',
            'manager_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        
        $lead->manager_status = $request->manager_status;
        $lead->manager_reason = $request->manager_reason;
        $lead->manager_user_id = auth()->id();
        
        // When manager marks as chargeback, update the main status too
        // This ensures it appears in Chargebacks page and Retention "Yet to Retain"
        if ($request->manager_status === 'chargeback') {
            $lead->status = 'chargeback';
            $lead->chargeback_marked_date = now();
            // Set retention_status to pending so it appears in "Yet to Retain"
            $lead->retention_status = 'pending';
        }
        // When manager approves, update status to accepted/underwritten
        elseif ($request->manager_status === 'approved') {
            $lead->status = 'accepted';
        }
        elseif ($request->manager_status === 'underwriting') {
            $lead->status = 'underwritten';
        }
        // When manager declines, update main status so it moves to Failed Leads section
        elseif ($request->manager_status === 'declined') {
            $lead->status = 'declined';
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Manager status updated successfully'
        ]);
    }

    /**
     * Issuance page - shows manager-approved sales ready for issuance
     */
    public function issuance(Request $request)
    {
        // Issuance section - show all sales that have been approved by manager
        // These are leads ready to be issued
        $query = Lead::with(['insuranceCarrier', 'partner'])
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
            $query->where('insurance_carrier_id', $request->carrier);
        }
        
        // Filter by issuance status
        if ($request->filled('issuance_status')) {
            $query->where('issuance_status', $request->issuance_status);
        }
        
        // Filter by followup status
        if ($request->filled('followup_status')) {
            $query->where('followup_status', $request->followup_status);
        }
        
        // Filter by policy type
        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }
        
        // Month filter for sale_date
        if ($request->filled('month')) {
            $query->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('sale_date', $request->year);
        }
        
        // Get unique carriers for filter dropdown from InsuranceCarrier model
        $carriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Get all active partners for dropdown
        $partners = \App\Models\Partner::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
        
        // Get all users for followup assignment dropdown except partners (Agent role users)
        // Include all employees, closers, verifiers, QA, etc.
        $followupUsers = \App\Models\User::whereDoesntHave('roles', function($query) {
            $query->whereIn('name', ['Agent', 'Vendor', 'US Agent']);
        })->orderBy('name')->get(['id', 'name']);
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        return view('admin.issuance.index', compact('leads', 'carriers', 'partners', 'followupUsers'));
    }

    /**
     * Update issuance status
     */
    public function updateIssuanceStatus(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        
        // Check if status has already been set (edit-once logic) - only Super Admin can change
        if (!empty($lead->issuance_date) && !auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'Issuance status has already been set and can only be changed by Super Admin.');
        }
        
        $request->validate([
            'issuance_status' => 'required|in:Issued,Incomplete',
            'issuance_reason' => 'nullable|string|max:1000',
            'issued_policy_number' => 'required|string|max:255',
            'partner_id' => 'required|exists:partners,id'
        ]);

        $commissionService = new CommissionCalculationService();
        
        // Check if policy number has already been set (edit-once logic)
        if (!empty($lead->policy_number_set_at) && $request->issued_policy_number != $lead->issued_policy_number) {
            if (!auth()->user()->hasRole('Super Admin')) {
                return back()->with('error', 'Policy number has already been set and cannot be changed.');
            }
        }
        
        // Check if assigned partner has already been set (edit-once logic)
        if (!empty($lead->partner_set_at) && $request->partner_id != $lead->partner_id) {
            if (!auth()->user()->hasRole('Super Admin')) {
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
        
        // Set assigned partner and timestamp if not already set
        if (empty($lead->partner_set_at)) {
            $lead->partner_id = $request->partner_id;
            $lead->partner_set_at = now();
        }
        
        if ($request->issuance_status === 'Issued') {
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
     * QA Review page - clone of sales management for QA users
     */
    public function qaReview(Request $request)
    {
        // QA Review section - show all sales that have been made by closers
        // Sales are leads that have a closer assigned and sale timestamp
        $query = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at');
        
        // Create a separate query for analytics with all filters applied (except qa_status filter to show all statuses)
        $analyticsQuery = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at');
        
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
        
        // Filter by carrier
        if ($request->filled('carrier')) {
            $analyticsQuery->where('carrier_name', $request->carrier);
        }
        
        // Apply month/year filters to analytics (using sale_date as primary filter)
        if ($request->filled('month')) {
            $analyticsQuery->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $analyticsQuery->whereYear('sale_date', $request->year);
        }
        
        // Get analytics counts
        $qaAnalytics = [
            'total' => $analyticsQuery->count(),
            'pending' => (clone $analyticsQuery)->where('qa_status', 'Pending')->count(),
            'good' => (clone $analyticsQuery)->where('qa_status', 'Good')->count(),
            'avg' => (clone $analyticsQuery)->where('qa_status', 'Avg')->count(),
            'bad' => (clone $analyticsQuery)->where('qa_status', 'Bad')->count(),
        ];
        
        // Calculate percentages
        $qaAnalytics['pending_percent'] = $qaAnalytics['total'] > 0 ? 
            round(($qaAnalytics['pending'] / $qaAnalytics['total']) * 100) : 0;
        $qaAnalytics['good_percent'] = $qaAnalytics['total'] > 0 ? 
            round(($qaAnalytics['good'] / $qaAnalytics['total']) * 100) : 0;
        $qaAnalytics['avg_percent'] = $qaAnalytics['total'] > 0 ? 
            round(($qaAnalytics['avg'] / $qaAnalytics['total']) * 100) : 0;
        $qaAnalytics['bad_percent'] = $qaAnalytics['total'] > 0 ? 
            round(($qaAnalytics['bad'] / $qaAnalytics['total']) * 100) : 0;
        $qaAnalytics['issues_percent'] = $qaAnalytics['total'] > 0 ? 
            round((($qaAnalytics['avg'] + $qaAnalytics['bad']) / $qaAnalytics['total']) * 100) : 0;
        
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
        
        // Month filter for sale_date
        if ($request->filled('month')) {
            $query->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('sale_date', $request->year);
        }
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::distinct()->pluck('carrier_name')->filter();
        
        // Order by sale_date, then sale_at, then created_at (fallback)
        $leads = $query->orderByRaw('COALESCE(sale_date, sale_at, created_at) DESC')->paginate(50);
        return view('admin.qa.review', compact('leads', 'carriers', 'qaAnalytics'));
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
        if ($lead->status !== 'chargeback') {
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
        $newSale->status = 'pending';
        $newSale->retention_status = null;
        $newSale->is_rewrite = !$isRetained; // If not retained, it's a rewrite
        $newSale->chargeback_marked_date = null;
        $newSale->qa_status = 'Pending';
        $newSale->qa_reason = null;
        $newSale->qa_user_id = null;
        $newSale->manager_status = 'pending';
        $newSale->manager_reason = null;
        $newSale->manager_user_id = null;
        $newSale->comments = ($isRetained ? 'Retained' : 'Rewritten') . " from chargeback by {$retentionOfficer} ({$daysDifference} days after chargeback)";
        $newSale->save();

        // Update the original chargeback
        $lead->retention_status = $isRetained ? 'retained' : 'rewrite';
        $lead->retained_at = $isRetained ? now() : null;
        $lead->is_rewrite = !$isRetained;
        $lead->retention_officer_id = auth()->id();
        $lead->status = $isRetained ? 'accepted' : 'chargeback';
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
            'manager_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->manager_reason = $request->manager_reason;
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
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can reset QA status.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        
        $lead->qa_status = 'Pending';
        $lead->qa_reason = null;
        $lead->qa_user_id = null;
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
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can reset Manager status.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        
        $lead->manager_status = 'pending';
        $lead->manager_reason = null;
        $lead->manager_user_id = null;
        // If it was marked as chargeback, revert those changes too
        if ($lead->status === 'chargeback') {
            $lead->status = 'pending';
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
        if (!auth()->user()->hasRole('Super Admin')) {
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
        if (!auth()->user()->hasRole('Super Admin')) {
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
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can recalculate commission.'
            ], 403);
        }

        $lead = Lead::findOrFail($id);
        $commissionService = new CommissionCalculationService();
        
        if ($lead->issuance_status === 'Issued' && $lead->partner_id && $lead->monthly_premium > 0) {
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
        if (!auth()->user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Only Super Admin can recalculate commissions.');
        }

        $commissionService = new CommissionCalculationService();
        $leads = Lead::where('issuance_status', 'Issued')
            ->whereNotNull('assigned_agent_id')
            ->where('monthly_premium', '>', 0)
            ->get();
        
        $processed = 0;
        $failed = 0;
        
        foreach ($leads as $lead) {
            // Get carrier ID
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
                    agentId: $lead->assigned_agent_id,
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
