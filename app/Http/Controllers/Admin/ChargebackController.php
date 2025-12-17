<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
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
        $month = $request->get('month');
        $year = $request->get('year');

        // Get chargebacks from leads table
        $query = Lead::where('status', 'chargeback')
            ->with(['insuranceCarrier', 'managedBy']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }

        // Apply month/year filter based on sale_date only if specified
        if ($month && $year) {
            $query->whereMonth('sale_date', $month)
                  ->whereYear('sale_date', $year);
        } elseif ($year) {
            $query->whereYear('sale_date', $year);
        }

        $chargebacks = $query->latest('sale_date')->paginate(50);

        // Calculate totals for the selected period
        $total_count = $chargebacks->total();
        
        // Get total amount based on the same filter
        $totalQuery = Lead::where('status', 'chargeback');
        
        if ($search) {
            $totalQuery->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }
        
        if ($month && $year) {
            $totalQuery->whereMonth('sale_date', $month)
                       ->whereYear('sale_date', $year);
        } elseif ($year) {
            $totalQuery->whereYear('sale_date', $year);
        }
        
        $total_amount = $totalQuery->sum('monthly_premium');

        return view('admin.chargebacks.index', compact(
            'chargebacks',
            'search',
            'month',
            'year',
            'total_count',
            'total_amount'
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
}
