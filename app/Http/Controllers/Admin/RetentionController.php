<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetentionController extends Controller
{
    /**
     * Display retention management page (for admins/managers)
     */
    public function index(Request $request)
    {
        // Get search and filter parameters
        $search = $request->get('search');
        $month = $request->get('month');
        $year = $request->get('year');

        // Base query builder helper
        $applyFilters = function($query) use ($search, $month, $year) {
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cn_name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('carrier_name', 'like', "%{$search}%")
                      ->orWhere('closer_name', 'like', "%{$search}%");
                });
            }

            if ($month && $year) {
                $query->whereMonth('sale_date', $month)
                      ->whereYear('sale_date', $year);
            } elseif ($year) {
                $query->whereYear('sale_date', $year);
            }

            return $query;
        };

        // 1. YET TO RETAIN - Active chargebacks that need retention (pending or null status)
        $yet_to_retain_query = Lead::where('status', 'chargeback')
            ->where(function($q) {
                $q->whereNull('retention_status')
                  ->orWhere('retention_status', 'pending');
            })
            ->with(['insuranceCarrier', 'retentionOfficer', 'managedBy']);
        
        $yet_to_retain_query = $applyFilters($yet_to_retain_query);
        $yet_to_retain_leads = $yet_to_retain_query->latest('sale_date')->paginate(50, ['*'], 'yet_to_retain_page');

        // Auto-calculate is_rewrite for yet to retain leads
        foreach ($yet_to_retain_leads as $lead) {
            if ($lead->chargeback_marked_date && $lead->sale_date) {
                $daysDiff = $lead->chargeback_marked_date->diffInDays($lead->sale_date);
                $lead->is_rewrite = $daysDiff >= 30;
                $lead->save();
            }
        }

        // 2. RETAINED - Successfully retained sales (regardless of current status)
        $retained_query = Lead::where('retention_status', 'retained')
            ->with(['insuranceCarrier', 'retentionOfficer', 'managedBy']);
        
        $retained_query = $applyFilters($retained_query);
        $retained_leads = $retained_query->latest('retained_at')->paginate(50, ['*'], 'retained_page');

        // 3. REWRITE - Chargebacks older than 30 days (from all chargebacks including pending)
        $rewrite_query = Lead::where('status', 'chargeback')
            ->where('is_rewrite', true)
            ->with(['insuranceCarrier', 'retentionOfficer', 'managedBy']);
        
        $rewrite_query = $applyFilters($rewrite_query);
        $rewrite_leads = $rewrite_query->latest('sale_date')->paginate(50, ['*'], 'rewrite_page');

        // 4. DISPOSITION - Incomplete issuances waiting for disposition
        $disposition_query = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Incomplete');
        
        $disposition_query = $applyFilters($disposition_query);
        $disposition_leads = $disposition_query->latest('sale_date')->paginate(50, ['*'], 'disposition_page');
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::distinct()->pluck('carrier_name')->filter();

        // Calculate stats
        $cb_count = Lead::where('status', 'chargeback')->count();
        $yet_to_retain_count = Lead::where('status', 'chargeback')
            ->where(function($q) {
                $q->whereNull('retention_status')
                  ->orWhere('retention_status', 'pending');
            })->count();
        $retained_count = Lead::where('retention_status', 'retained')->count();
        $rewrite_count = Lead::where('status', 'chargeback')->where('is_rewrite', true)->count();
        $disposition_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Incomplete')
            ->count();

        return view('admin.retention.index', compact(
            'yet_to_retain_leads',
            'retained_leads',
            'rewrite_leads',
            'disposition_leads',
            'carriers',
            'search',
            'month',
            'year',
            'cb_count',
            'rewrite_count',
            'yet_to_retain_count',
            'retained_count',
            'disposition_count'
        ));
    }

    /**
     * Update retention status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,retained,rewrite'
        ]);

        $lead = Lead::findOrFail($id);
        
        // Get the retention officer's name
        $retentionOfficer = auth()->user()->name;

        // Update retention status
        $lead->retention_status = $request->status;

        // Update based on retention status
        if ($request->status == 'retained') {
            // When a chargeback is retained (< 30 days), create a NEW sale
            // Retention officer successfully re-sold the policy
            
            // Create a new sale record (duplicate of the chargeback but as new sale)
            $newSale = $lead->replicate();
            $newSale->closer_name = $retentionOfficer; // Retention officer becomes the closer
            $newSale->sale_at = now();
            $newSale->sale_date = now()->format('Y-m-d');
            $newSale->status = 'pending'; // New sale goes through approval process
            $newSale->retention_status = null; // Clear retention status for new sale
            $newSale->is_rewrite = false;
            $newSale->chargeback_marked_date = null; // Not a chargeback anymore
            $newSale->qa_status = 'Pending'; // Reset QA status
            $newSale->qa_reason = null;
            $newSale->qa_user_id = null;
            $newSale->manager_status = 'pending'; // Reset manager status
            $newSale->manager_reason = null;
            $newSale->manager_user_id = null;
            $newSale->comments = 'Retained from chargeback by ' . $retentionOfficer;
            $newSale->save();
            
            // Mark the original chargeback as retained
            $lead->status = 'accepted';
            $lead->retained_at = now();
            $lead->retention_officer_id = auth()->id();
        } elseif ($request->status == 'rewrite') {
            // When marked as rewrite (>= 30 days), create a NEW sale
            // This is essentially a new policy since it's been too long
            
            // Create a new sale record
            $newSale = $lead->replicate();
            $newSale->closer_name = $retentionOfficer; // Retention officer becomes the closer
            $newSale->sale_at = now();
            $newSale->sale_date = now()->format('Y-m-d');
            $newSale->status = 'pending'; // New sale goes through approval process
            $newSale->retention_status = null; // Clear retention status for new sale
            $newSale->is_rewrite = false; // This is a fresh sale now
            $newSale->chargeback_marked_date = null; // Not a chargeback anymore
            $newSale->qa_status = 'Pending'; // Reset QA status
            $newSale->qa_reason = null;
            $newSale->qa_user_id = null;
            $newSale->manager_status = 'pending'; // Reset manager status
            $newSale->manager_reason = null;
            $newSale->manager_user_id = null;
            $newSale->comments = 'Rewritten from chargeback by ' . $retentionOfficer;
            $newSale->save();
            
            // Mark the original as rewrite
            $lead->is_rewrite = true;
            $lead->retained_at = null;
            $lead->retention_officer_id = auth()->id();
        } elseif ($request->status == 'pending') {
            // When marked as pending/yet to retain, clear flags
            $lead->is_rewrite = false;
            $lead->retained_at = null;
        }

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Retention status updated successfully. New sale created.'
        ]);
    }

    /**
     * Display incomplete issuance list (incomplete issuances sent to retention)
     * NOTE: This is now integrated into the main retention.index view as "Disposition" tab
     * Keeping this method for backwards compatibility if needed
     */
    public function incompleteIssuance(Request $request)
    {
        $query = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Incomplete');
        
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
        
        // Filter by disposition status
        if ($request->filled('disposition')) {
            $query->where('issuance_disposition', $request->disposition);
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
        return view('admin.retention.incomplete', compact('leads', 'carriers'));
    }

    /**
     * Show full details of incomplete issuance lead
     */
    public function showIncompleteDetails($id)
    {
        $lead = Lead::with('insuranceCarrier')->findOrFail($id);
        
        if ($lead->issuance_status !== 'Incomplete') {
            return redirect()->route('retention.incomplete')->with('error', 'This lead is not marked as incomplete issuance.');
        }
        
        return view('admin.retention.incomplete-details', compact('lead'));
    }

    /**
     * Save disposition for incomplete issuance
     */
    public function saveDisposition(Request $request, $id)
    {
        $request->validate([
            'issuance_disposition' => 'required|in:Via Portal,Via Email,By Carrier,By Bank',
            'issuance_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        
        if ($lead->issuance_status !== 'Incomplete') {
            return response()->json([
                'success' => false,
                'message' => 'This lead is not marked as incomplete issuance.'
            ], 422);
        }
        
        $lead->issuance_disposition = $request->issuance_disposition;
        $lead->issuance_reason = $request->issuance_reason;
        $lead->disposition_officer_id = auth()->id();
        $lead->issuance_disposition_date = now();
        
        // Check for other insurances if applicable
        if (in_array($request->issuance_disposition, ['By Carrier', 'By Bank'])) {
            $otherInsurances = $this->checkOtherInsurancesCount($lead, $request->issuance_disposition);
            $lead->has_other_insurances = $otherInsurances > 0;
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Disposition saved successfully'
        ]);
    }

    /**
     * Check for other insurances for a lead (AJAX endpoint)
     */
    public function checkOtherInsurances(Request $request, $id)
    {
        $request->validate([
            'disposition' => 'required|in:By Carrier,By Bank'
        ]);

        $lead = Lead::findOrFail($id);
        $otherInsurances = [];
        
        if ($request->disposition === 'By Carrier') {
            // Check for other insurances with the same carrier
            $otherInsurances = Lead::where('phone_number', $lead->phone_number)
                ->where('carrier_name', $lead->carrier_name)
                ->where('id', '!=', $id)
                ->where(function($q) {
                    $q->where('status', 'accepted')
                      ->orWhere('status', 'verified')
                      ->orWhere('status', 'closed');
                })
                ->select('id', 'cn_name', 'carrier_name', 'policy_type', 'sale_date', 'policy_number')
                ->get();
        } elseif ($request->disposition === 'By Bank') {
            // Check for other insurances with the same bank account
            $otherInsurances = Lead::where('account_number', $lead->account_number)
                ->where('id', '!=', $id)
                ->where(function($q) {
                    $q->where('status', 'accepted')
                      ->orWhere('status', 'verified')
                      ->orWhere('status', 'closed');
                })
                ->select('id', 'cn_name', 'carrier_name', 'policy_type', 'sale_date', 'policy_number', 'bank_name')
                ->get();
        }

        return response()->json([
            'count' => $otherInsurances->count(),
            'insurances' => $otherInsurances
        ]);
    }

    /**
     * Helper method to count other insurances
     */
    private function checkOtherInsurancesCount($lead, $disposition)
    {
        if ($disposition === 'By Carrier') {
            return Lead::where('phone_number', $lead->phone_number)
                ->where('carrier_name', $lead->carrier_name)
                ->where('id', '!=', $lead->id)
                ->where(function($q) {
                    $q->where('status', 'accepted')
                      ->orWhere('status', 'verified')
                      ->orWhere('status', 'closed');
                })
                ->count();
        } elseif ($disposition === 'By Bank') {
            return Lead::where('account_number', $lead->account_number)
                ->where('id', '!=', $lead->id)
                ->where(function($q) {
                    $q->where('status', 'accepted')
                      ->orWhere('status', 'verified')
                      ->orWhere('status', 'closed');
                })
                ->count();
        }
        
        return 0;
    }
}
