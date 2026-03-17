<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use Illuminate\Http\Request;

/**
 * Paid Sales
 *
 * Stage 7 (final) in the sales pipeline.
 * Read-only view of leads where paid_at IS NOT NULL.
 */
class PaidSalesController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        $query = Lead::with(['insuranceCarrier', 'paidBy', 'issuedByUser', 'partner'])
            ->whereNotNull('paid_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cn_name',      'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name',  'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $query->where('insurance_carrier_id', $carrier);
        }

        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        // Stats
        $totalCount         = (clone $query)->count();
        $totalPremium       = (clone $query)->sum('monthly_premium');
        $totalCoverage      = (clone $query)->sum('coverage_amount');

        $leads    = $query->orderBy('paid_at', 'desc')->paginate(50);
        $carriers = InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.paid-sales.index', compact(
            'leads', 'carriers', 'search', 'carrier',
            'dateFrom', 'dateTo',
            'totalCount', 'totalPremium', 'totalCoverage'
        ));
    }
}
