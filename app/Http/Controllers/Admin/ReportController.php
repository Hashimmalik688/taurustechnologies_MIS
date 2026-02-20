<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\User;
use App\Support\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the reports hub page.
     */
    public function index()
    {
        // Get filter options
        $closers = User::role(['Ravens Closer', 'Peregrine Closer'])
            ->orderBy('name')
            ->pluck('name', 'id');

        $managers = User::role(['Manager', 'Super Admin', 'Co-ordinator'])
            ->orderBy('name')
            ->pluck('name', 'id');

        $carriers = InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        $partners = Partner::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        $verifiers = User::role(['Verifier'])
            ->orderBy('name')
            ->pluck('name', 'id');

        $statuses = [
            Statuses::LEAD_PENDING      => 'Pending',
            Statuses::LEAD_ACCEPTED     => 'Accepted',
            Statuses::LEAD_SALE         => 'Sale',
            Statuses::LEAD_DECLINED     => 'Declined',
            Statuses::LEAD_RETURNED     => 'Returned',
            Statuses::LEAD_CHARGEBACK   => 'Chargeback',
            Statuses::LEAD_UNDERWRITTEN => 'Underwritten',
            Statuses::LEAD_TRANSFERRED  => 'Transferred',
            Statuses::LEAD_CLOSED       => 'Closed',
        ];

        $teams = Lead::select('team')
            ->whereNotNull('team')
            ->where('team', '!=', '')
            ->distinct()
            ->orderBy('team')
            ->pluck('team');

        $sources = Lead::select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        $states = Lead::select('state')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        return view('admin.reports.index', compact(
            'closers',
            'managers',
            'carriers',
            'partners',
            'verifiers',
            'statuses',
            'teams',
            'sources',
            'states'
        ));
    }

    /**
     * Generate report data based on filters.
     */
    public function generate(Request $request)
    {
        $query = Lead::query()
            ->leftJoin('users as closer_user', 'leads.managed_by', '=', 'closer_user.id')
            ->leftJoin('users as verifier_user', 'leads.verified_by', '=', 'verifier_user.id')
            ->leftJoin('insurance_carriers', 'leads.insurance_carrier_id', '=', 'insurance_carriers.id')
            ->leftJoin('partners', 'leads.partner_id', '=', 'partners.id')
            ->select([
                'leads.id',
                'leads.cn_name',
                'leads.phone_number',
                'leads.state',
                'leads.status',
                'leads.carrier_name',
                'leads.coverage_amount',
                'leads.monthly_premium',
                'leads.policy_type',
                'leads.policy_number',
                'leads.source',
                'leads.team',
                'leads.closer_name',
                'leads.sale_date',
                'leads.sale_at',
                'leads.issuance_status',
                'leads.qa_status',
                'leads.manager_status',
                'leads.retention_status',
                'leads.agent_commission',
                'leads.agent_revenue',
                'leads.settlement_percentage',
                'leads.created_at',
                'closer_user.name as closer_user_name',
                'verifier_user.name as verifier_user_name',
                'insurance_carriers.name as insurance_carrier_name',
                'partners.name as partner_name',
            ]);

        // Apply Report Type preset filters
        $reportType = $request->input('report_type', 'all');

        switch ($reportType) {
            case 'sales':
                $query->where('leads.status', Statuses::LEAD_SALE);
                break;
            case 'partner':
                $query->whereNotNull('leads.partner_id');
                break;
            case 'submissions':
                $query->whereIn('leads.manager_status', [
                    Statuses::MGR_APPROVED,
                    Statuses::MGR_PENDING,
                    Statuses::MGR_UNDERWRITING,
                ]);
                break;
            case 'chargebacks':
                $query->where('leads.status', Statuses::LEAD_CHARGEBACK);
                break;
            case 'retention':
                $query->whereNotNull('leads.retention_status');
                break;
            case 'issuance':
                $query->whereNotNull('leads.issuance_status');
                break;
        }

        // Apply individual filters
        if ($request->filled('closer_id')) {
            $query->where('leads.managed_by', $request->closer_id);
        }

        if ($request->filled('manager_id')) {
            // Leads where the manager approved/reviewed
            $query->where('leads.manager_user_id', $request->manager_id);
        }

        if ($request->filled('carrier_id')) {
            $query->where('leads.insurance_carrier_id', $request->carrier_id);
        }

        if ($request->filled('partner_id')) {
            $query->where('leads.partner_id', $request->partner_id);
        }

        if ($request->filled('verifier_id')) {
            $query->where('leads.verified_by', $request->verifier_id);
        }

        if ($request->filled('status')) {
            $query->where('leads.status', $request->status);
        }

        if ($request->filled('team')) {
            $query->where('leads.team', $request->team);
        }

        if ($request->filled('source')) {
            $query->where('leads.source', $request->source);
        }

        if ($request->filled('state')) {
            $query->where('leads.state', $request->state);
        }

        if ($request->filled('qa_status')) {
            $query->where('leads.qa_status', $request->qa_status);
        }

        if ($request->filled('manager_status')) {
            $query->where('leads.manager_status', $request->manager_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('leads.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('leads.created_at', '<=', $request->date_to);
        }

        if ($request->filled('sale_date_from')) {
            $query->whereDate('leads.sale_date', '>=', $request->sale_date_from);
        }

        if ($request->filled('sale_date_to')) {
            $query->whereDate('leads.sale_date', '<=', $request->sale_date_to);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'leads.created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = [
            'leads.created_at', 'leads.cn_name', 'leads.status', 'leads.sale_date',
            'leads.coverage_amount', 'leads.monthly_premium', 'closer_user.name',
            'insurance_carriers.name', 'partners.name',
        ];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('leads.created_at', 'desc');
        }

        // Summary stats
        $summaryQuery = clone $query;
        $summary = [
            'total_records'    => $summaryQuery->count(),
            'total_premium'    => (clone $summaryQuery)->sum('leads.monthly_premium') ?? 0,
            'total_coverage'   => (clone $summaryQuery)->sum('leads.coverage_amount') ?? 0,
            'total_commission' => (clone $summaryQuery)->sum('leads.agent_commission') ?? 0,
            'total_revenue'    => (clone $summaryQuery)->sum('leads.agent_revenue') ?? 0,
        ];

        // Paginate results
        $perPage = $request->input('per_page', 25);
        $results = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            return response()->json([
                'html'    => view('admin.reports._results', compact('results', 'summary'))->render(),
                'summary' => $summary,
            ]);
        }

        return view('admin.reports.index', compact('results', 'summary'));
    }

    /**
     * Export report data to CSV.
     */
    public function export(Request $request)
    {
        // Re-run the query without pagination
        $query = Lead::query()
            ->leftJoin('users as closer_user', 'leads.managed_by', '=', 'closer_user.id')
            ->leftJoin('users as verifier_user', 'leads.verified_by', '=', 'verifier_user.id')
            ->leftJoin('insurance_carriers', 'leads.insurance_carrier_id', '=', 'insurance_carriers.id')
            ->leftJoin('partners', 'leads.partner_id', '=', 'partners.id')
            ->select([
                'leads.id',
                'leads.cn_name',
                'leads.phone_number',
                'leads.state',
                'leads.status',
                'leads.carrier_name',
                'leads.coverage_amount',
                'leads.monthly_premium',
                'leads.policy_type',
                'leads.policy_number',
                'leads.source',
                'leads.team',
                'leads.closer_name',
                'leads.sale_date',
                'leads.qa_status',
                'leads.manager_status',
                'leads.retention_status',
                'leads.issuance_status',
                'leads.agent_commission',
                'leads.agent_revenue',
                'leads.created_at',
                'closer_user.name as closer_user_name',
                'verifier_user.name as verifier_user_name',
                'insurance_carriers.name as insurance_carrier_name',
                'partners.name as partner_name',
            ]);

        // Apply same filters as generate()
        $this->applyFilters($query, $request);

        $results = $query->orderBy('leads.created_at', 'desc')->get();

        $filename = 'report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'Client Name', 'Phone', 'State', 'Status',
                'Carrier', 'Coverage', 'Premium', 'Policy Type', 'Policy #',
                'Source', 'Team', 'Closer', 'Partner',
                'Sale Date', 'QA Status', 'Manager Status',
                'Retention', 'Issuance', 'Commission', 'Revenue', 'Created',
            ]);

            foreach ($results as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->cn_name,
                    $row->phone_number,
                    $row->state,
                    $row->status,
                    $row->insurance_carrier_name ?? $row->carrier_name,
                    $row->coverage_amount,
                    $row->monthly_premium,
                    $row->policy_type,
                    $row->policy_number,
                    $row->source,
                    $row->team,
                    $row->closer_user_name ?? $row->closer_name,
                    $row->partner_name,
                    $row->sale_date,
                    $row->qa_status,
                    $row->manager_status,
                    $row->retention_status,
                    $row->issuance_status,
                    $row->agent_commission,
                    $row->agent_revenue,
                    $row->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply filters to a query (shared between generate and export).
     */
    private function applyFilters($query, Request $request)
    {
        $reportType = $request->input('report_type', 'all');

        switch ($reportType) {
            case 'sales':
                $query->where('leads.status', Statuses::LEAD_SALE);
                break;
            case 'partner':
                $query->whereNotNull('leads.partner_id');
                break;
            case 'submissions':
                $query->whereIn('leads.manager_status', [
                    Statuses::MGR_APPROVED,
                    Statuses::MGR_PENDING,
                    Statuses::MGR_UNDERWRITING,
                ]);
                break;
            case 'chargebacks':
                $query->where('leads.status', Statuses::LEAD_CHARGEBACK);
                break;
            case 'retention':
                $query->whereNotNull('leads.retention_status');
                break;
            case 'issuance':
                $query->whereNotNull('leads.issuance_status');
                break;
        }

        if ($request->filled('closer_id')) {
            $query->where('leads.managed_by', $request->closer_id);
        }
        if ($request->filled('manager_id')) {
            $query->where('leads.manager_user_id', $request->manager_id);
        }
        if ($request->filled('carrier_id')) {
            $query->where('leads.insurance_carrier_id', $request->carrier_id);
        }
        if ($request->filled('partner_id')) {
            $query->where('leads.partner_id', $request->partner_id);
        }
        if ($request->filled('verifier_id')) {
            $query->where('leads.verified_by', $request->verifier_id);
        }
        if ($request->filled('status')) {
            $query->where('leads.status', $request->status);
        }
        if ($request->filled('team')) {
            $query->where('leads.team', $request->team);
        }
        if ($request->filled('source')) {
            $query->where('leads.source', $request->source);
        }
        if ($request->filled('state')) {
            $query->where('leads.state', $request->state);
        }
        if ($request->filled('qa_status')) {
            $query->where('leads.qa_status', $request->qa_status);
        }
        if ($request->filled('manager_status')) {
            $query->where('leads.manager_status', $request->manager_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('leads.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('leads.created_at', '<=', $request->date_to);
        }
        if ($request->filled('sale_date_from')) {
            $query->whereDate('leads.sale_date', '>=', $request->sale_date_from);
        }
        if ($request->filled('sale_date_to')) {
            $query->whereDate('leads.sale_date', '<=', $request->sale_date_to);
        }
    }
}
