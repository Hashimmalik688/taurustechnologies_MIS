<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Events\LeadCreated;
use App\Events\SaleCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
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
            ->whereNotNull('sale_at');
        
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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::distinct()->pluck('carrier_name')->filter();
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        return view('admin.sales.index', compact('leads', 'carriers'));
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
        $insurance = Lead::with('carriers')->findOrFail($id);

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

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy($id)
    {
        Lead::destroy($id);

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,underwriting,forwarded,chargeback,approved,declined'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;
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
            'qa_status' => 'required|in:In Review,Approved,Rejected',
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
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Manager status updated successfully'
        ]);
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
        return view('admin.qa.review', compact('leads', 'carriers'));
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
        $newSale->qa_status = 'In Review';
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
}
