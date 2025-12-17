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
        
        $applyFilters($yet_to_retain_query);
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
        
        $applyFilters($retained_query);
        $retained_leads = $retained_query->latest('retained_at')->paginate(50, ['*'], 'retained_page');

        // 3. REWRITE - Chargebacks older than 30 days (from all chargebacks including pending)
        $rewrite_query = Lead::where('status', 'chargeback')
            ->where('is_rewrite', true)
            ->with(['insuranceCarrier', 'retentionOfficer', 'managedBy']);
        
        $applyFilters($rewrite_query);
        $rewrite_leads = $rewrite_query->latest('sale_date')->paginate(50, ['*'], 'rewrite_page');

        // Calculate stats
        $cb_count = Lead::where('status', 'chargeback')->count();
        $yet_to_retain_count = Lead::where('status', 'chargeback')
            ->where(function($q) {
                $q->whereNull('retention_status')
                  ->orWhere('retention_status', 'pending');
            })->count();
        $retained_count = Lead::where('retention_status', 'retained')->count();
        $rewrite_count = Lead::where('status', 'chargeback')->where('is_rewrite', true)->count();

        return view('admin.retention.index', compact(
            'yet_to_retain_leads',
            'retained_leads',
            'rewrite_leads',
            'search',
            'month',
            'year',
            'cb_count',
            'rewrite_count',
            'yet_to_retain_count',
            'retained_count'
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
            $newSale->qa_status = 'In Review'; // Reset QA status
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
            $newSale->qa_status = 'In Review'; // Reset QA status
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
}
