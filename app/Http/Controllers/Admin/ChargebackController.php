<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LedgerEntry;
use App\Support\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChargebackController extends Controller
{
    /**
     * Display chargebacks page
     */
    public function index(Request $request)
    {
        // Get search and filter parameters
        $search = $request->get('search');
        $policySearch = $request->get('policy_search');
        $month = $request->get('month');
        $year = $request->get('year');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        // Reusable filter closure
        $applyFilters = function($query) use ($search, $policySearch, $month, $year, $date_from, $date_to) {
            // Policy number search bypasses all date/period filters
            if ($policySearch) {
                $query->where('policy_number', 'like', "%{$policySearch}%");
                return $query;
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cn_name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('carrier_name', 'like', "%{$search}%")
                      ->orWhere('closer_name', 'like', "%{$search}%")
                      ->orWhere('policy_number', 'like', "%{$search}%");
                });
            }

            if ($date_from) {
                $query->whereDate('sale_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('sale_date', '<=', $date_to);
            }

            if (!$date_from && !$date_to) {
                if ($month && $year) {
                    $query->whereMonth('sale_date', $month)
                          ->whereYear('sale_date', $year);
                } elseif ($year) {
                    $query->whereYear('sale_date', $year);
                }
            }

            return $query;
        };

        // Get chargebacks from leads table
        $query = Lead::where('status', 'chargeback')
            ->with(['insuranceCarrier', 'managedBy', 'chargebackMarkedBy', 'chargebackPaidBy']);
        $applyFilters($query);

        $chargebacks = $query->latest('sale_date')->paginate(50);

        // Calculate totals for the selected period
        $total_count = $chargebacks->total();
        
        // Get total amount based on the same filter
        $totalQuery = Lead::where('status', 'chargeback');
        $applyFilters($totalQuery);
        
        $total_amount = $totalQuery->sum('monthly_premium');

        $fdfpTypes     = Statuses::FDFP_TYPES;
        $niDispositions = Statuses::NOT_ISSUED_DISPOSITIONS;

        return view('admin.chargebacks.index', compact(
            'chargebacks',
            'search',
            'policySearch',
            'month',
            'year',
            'date_from',
            'date_to',
            'total_count',
            'total_amount',
            'fdfpTypes',
            'niDispositions'
        ));
    }

    /**
     * Show chargeback details
     */
    public function show($id)
    {
        $chargeback = LedgerEntry::with(['lead', 'user'])
            ->findOrFail($id);

        return view('admin.chargebacks.show', compact('chargeback'));
    }

    /**
     * Send a chargebacked lead to the Retention queue.
     * Resets retention_status to 'pending' and records who sent it.
     */
    public function sendToRetention(Request $request, int $id)
    {
        $request->validate([
            'fdfp_type'        => 'required|in:unstable_to_locate,insufficient_fund,unauthorized_payments,manual_action',
            'manual_disp'      => 'nullable|string|max:100',
            'comment'          => 'nullable|string|max:1000',
        ]);

        $lead = Lead::findOrFail($id);

        if ($lead->status !== Statuses::LEAD_CHARGEBACK) {
            return response()->json(['success' => false, 'message' => 'Lead is not in chargeback status.'], 422);
        }

        $lead->retention_status             = Statuses::RETENTION_PENDING;
        $lead->not_paid_fdfp_type           = $request->input('fdfp_type');
        $lead->not_paid_manual_disposition  = $request->input('fdfp_type') === 'manual_action' ? $request->input('manual_disp') : null;
        $lead->not_paid_comment             = $request->input('comment');
        $lead->ret_action_status            = null;
        $lead->ret_action_updated_at        = now();
        $lead->ret_action_updated_by        = Auth::id();
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Lead sent to Retention queue.']);
    }
}
