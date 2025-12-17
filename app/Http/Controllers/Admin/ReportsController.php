<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Models\Vendor;
use App\Models\LedgerEntry;
use App\Models\Attendance;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::role('Employee')->get();

        if ($request->employee) {
            $fromDate = $request->from_date ? \Carbon\Carbon::parse($request->from_date)->startOfDay() : null;
            $toDate = $request->to_date ? \Carbon\Carbon::parse($request->to_date)->endOfDay() : null;
            $leads = Lead::where('forwarded_by', $request->employee)
                ->when($fromDate, function ($query) use ($fromDate) {
                    return $query->where('created_at', '>=', $fromDate);
                })
                ->when($toDate, function ($query) use ($toDate) {
                    return $query->where('created_at', '<=', $toDate);
                })
                ->get();

            $totalLeads = $leads->count();

            return view('admin.reports.index', compact('employees', 'leads', 'totalLeads', 'fromDate', 'toDate'));

        }

        return view('admin.reports.index', compact('employees'));
    }

    /**
     * Display sales analytics report.
     */
    public function salesAnalytics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Sales by period - fixed GROUP BY
        $salesByPeriod = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'), 'status')
            ->orderBy('date')
            ->get();

        // Sales by agent
        $salesByAgent = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('forwarded_by as agent_id, COUNT(*) as total_leads, status')
            ->groupBy('forwarded_by', 'status')
            ->with('forwardedBy')
            ->get()
            ->groupBy('agent_id');

        // Sales by status
        $salesByStatus = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Total summary
        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalSold = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'sold')
            ->count();

        return view('admin.reports.sales-analytics', compact(
            'salesByPeriod',
            'salesByAgent',
            'salesByStatus',
            'totalLeads',
            'totalSold',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display agent performance report.
     */
    public function agentPerformance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $agents = User::role('Employee')
            ->withCount([
                'forwardedLeads' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'forwardedLeads as sold_leads_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'sold');
                },
                'forwardedLeads as pending_leads_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'pending');
                }
            ])
            ->get()
            ->map(function ($agent) {
                $agent->conversion_rate = $agent->forwarded_leads_count > 0
                    ? round(($agent->sold_leads_count / $agent->forwarded_leads_count) * 100, 2)
                    : 0;
                return $agent;
            });

        return view('admin.reports.agent-performance', compact('agents', 'startDate', 'endDate'));
    }

    /**
     * Display revenue tracking report.
     */
    public function revenueTracking(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfYear()->format('Y-m-d'));

        // Monthly revenue from ledger credits
        $monthlyRevenue = LedgerEntry::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'credit')
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Monthly expenses from ledger debits
        $monthlyExpenses = LedgerEntry::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'debit')
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as expense')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Total revenue and expenses
        $totalRevenue = LedgerEntry::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'credit')
            ->sum('amount');

        $totalExpenses = LedgerEntry::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('type', 'debit')
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        return view('admin.reports.revenue-tracking', compact(
            'monthlyRevenue',
            'monthlyExpenses',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display conversion rates report.
     */
    public function conversionRates(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $leads = Lead::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalLeads = $leads->count();
        $soldLeads = $leads->where('status', 'sold')->count();
        $pendingLeads = $leads->where('status', 'pending')->count();
        $followUpLeads = $leads->where('status', 'follow_up')->count();
        $lostLeads = $leads->where('status', 'lost')->count();

        $conversionRate = $totalLeads > 0 ? round(($soldLeads / $totalLeads) * 100, 2) : 0;

        // Conversion by source
        $conversionBySource = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('source, COUNT(*) as total,
                         SUM(CASE WHEN status = "sold" THEN 1 ELSE 0 END) as sold')
            ->groupBy('source')
            ->get()
            ->map(function ($item) {
                $item->conversion_rate = $item->total > 0
                    ? round(($item->sold / $item->total) * 100, 2)
                    : 0;
                return $item;
            });

        // Conversion by agent
        $conversionByAgent = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('forwarded_by, COUNT(*) as total,
                         SUM(CASE WHEN status = "sold" THEN 1 ELSE 0 END) as sold')
            ->groupBy('forwarded_by')
            ->with('forwardedBy')
            ->get()
            ->map(function ($item) {
                $item->conversion_rate = $item->total > 0
                    ? round(($item->sold / $item->total) * 100, 2)
                    : 0;
                return $item;
            });

        return view('admin.reports.conversion-rates', compact(
            'totalLeads',
            'soldLeads',
            'pendingLeads',
            'followUpLeads',
            'lostLeads',
            'conversionRate',
            'conversionBySource',
            'conversionByAgent',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display vendor commissions report.
     */
    public function vendorCommissions(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $vendors = Vendor::with(['ledgerEntries' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function ($vendor) use ($startDate, $endDate) {
            $entries = $vendor->ledgerEntries;

            $vendor->period_credits = $entries->where('type', 'credit')->sum('amount');
            $vendor->period_debits = $entries->where('type', 'debit')->sum('amount');
            $vendor->period_balance = $vendor->period_credits - $vendor->period_debits;

            // Commission entries only
            $vendor->commissions = $entries->where('category', 'commission')->sum('amount');
            $vendor->payments = $entries->where('category', 'payment')->sum('amount');

            return $vendor;
        });

        $totalCommissions = $vendors->sum('commissions');
        $totalPayments = $vendors->sum('payments');
        $totalOutstanding = $vendors->sum('period_balance');

        return view('admin.reports.vendor-commissions', compact(
            'vendors',
            'totalCommissions',
            'totalPayments',
            'totalOutstanding',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display custom report with various filters.
     */
    public function customReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'leads');

        $data = [];

        switch ($reportType) {
            case 'leads':
                $query = Lead::whereBetween('created_at', [$startDate, $endDate]);

                if ($request->has('status') && $request->status) {
                    $query->where('status', $request->status);
                }

                if ($request->has('source') && $request->source) {
                    $query->where('source', $request->source);
                }

                if ($request->has('agent') && $request->agent) {
                    $query->where('forwarded_by', $request->agent);
                }

                $data['leads'] = $query->with('forwardedBy')->get();
                break;

            case 'ledger':
                $query = LedgerEntry::whereBetween('transaction_date', [$startDate, $endDate]);

                if ($request->has('vendor_id') && $request->vendor_id) {
                    $query->where('vendor_id', $request->vendor_id);
                }

                if ($request->has('type') && $request->type) {
                    $query->where('type', $request->type);
                }

                if ($request->has('category') && $request->category) {
                    $query->where('category', $request->category);
                }

                $data['entries'] = $query->with(['vendor', 'user'])->get();
                break;

            case 'vendors':
                $data['vendors'] = Vendor::with('ledgerEntries')->get();
                break;
        }

        $agents = User::role('Employee')->get();
        $vendors = Vendor::active()->get();

        return view('admin.reports.custom', compact(
            'data',
            'reportType',
            'startDate',
            'endDate',
            'agents',
            'vendors'
        ));
    }

    /**
     * Export comprehensive CRM report as CSV
     */
    public function export(Request $request)
    {
        $reportType = $request->input('type', 'leads');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $filename = $reportType . '_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reportType, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            switch ($reportType) {
                case 'leads':
                    $this->exportLeads($file, $startDate, $endDate);
                    break;
                case 'sales':
                    $this->exportSales($file, $startDate, $endDate);
                    break;
                case 'attendance':
                    $this->exportAttendance($file, $startDate, $endDate);
                    break;
                case 'salary':
                    $this->exportSalary($file, $startDate, $endDate);
                    break;
                case 'employees':
                    $this->exportEmployees($file);
                    break;
                case 'agents':
                    $this->exportAgents($file);
                    break;
                default:
                    $this->exportLeads($file, $startDate, $endDate);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportLeads($file, $startDate, $endDate)
    {
        // Header
        fputcsv($file, [
            'ID', 'Date', 'Client Name', 'Phone', 'DOB', 'Gender', 'Address', 'SSN',
            'Carrier', 'Coverage Amount', 'Monthly Premium', 'Beneficiary', 'Status',
            'Closer', 'Created At'
        ]);

        // Data
        Lead::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->chunk(100, function($leads) use ($file) {
                foreach ($leads as $lead) {
                    fputcsv($file, [
                        $lead->id,
                        $lead->date,
                        $lead->cn_name,
                        $lead->phone_number,
                        $lead->date_of_birth,
                        $lead->gender,
                        $lead->address,
                        $lead->ssn,
                        $lead->carrier_name,
                        $lead->coverage_amount,
                        $lead->monthly_premium,
                        $lead->beneficiary,
                        $lead->status,
                        $lead->closer_name,
                        $lead->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });
    }

    private function exportSales($file, $startDate, $endDate)
    {
        // Header
        fputcsv($file, [
            'ID', 'Client Name', 'Phone', 'Carrier', 'Coverage Amount',
            'Monthly Premium', 'Status', 'Closer', 'Sale Date', 'Created At'
        ]);

        // Data
        Lead::whereIn('status', ['accepted', 'underwritten'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->chunk(100, function($leads) use ($file) {
                foreach ($leads as $lead) {
                    fputcsv($file, [
                        $lead->id,
                        $lead->cn_name,
                        $lead->phone_number,
                        $lead->carrier_name,
                        $lead->coverage_amount,
                        $lead->monthly_premium,
                        $lead->status,
                        $lead->closer_name,
                        $lead->sale_at ? $lead->sale_at->format('Y-m-d') : '',
                        $lead->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });
    }

    private function exportAttendance($file, $startDate, $endDate)
    {
        // Header
        fputcsv($file, [
            'ID', 'Employee Name', 'Date', 'Login Time', 'Logout Time',
            'IP Address', 'Status'
        ]);

        // Data
        Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->chunk(100, function($attendances) use ($file) {
                foreach ($attendances as $attendance) {
                    fputcsv($file, [
                        $attendance->id,
                        $attendance->user->name ?? 'N/A',
                        $attendance->date,
                        $attendance->login_time ? Carbon::parse($attendance->login_time)->format('H:i:s') : '',
                        $attendance->logout_time ? Carbon::parse($attendance->logout_time)->format('H:i:s') : '',
                        $attendance->ip_address,
                        $attendance->status,
                    ]);
                }
            });
    }

    private function exportSalary($file, $startDate, $endDate)
    {
        // Header
        fputcsv($file, [
            'ID', 'Employee Name', 'Month', 'Year', 'Basic Salary', 'Target Sales',
            'Actual Sales', 'Extra Sales', 'Total Bonus', 'Attendance Bonus',
            'Total Deductions', 'Gross Salary', 'Net Salary', 'Status'
        ]);

        // Data
        SalaryRecord::with('user')
            ->whereRaw('CONCAT(salary_year, "-", LPAD(salary_month, 2, "0"), "-01") BETWEEN ? AND ?', [$startDate, $endDate])
            ->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->chunk(100, function($salaries) use ($file) {
                foreach ($salaries as $salary) {
                    fputcsv($file, [
                        $salary->id,
                        $salary->user->name ?? 'N/A',
                        $salary->month_name,
                        $salary->salary_year,
                        $salary->basic_salary,
                        $salary->target_sales,
                        $salary->actual_sales,
                        $salary->extra_sales,
                        $salary->total_bonus,
                        $salary->attendance_bonus,
                        $salary->total_deductions,
                        $salary->gross_salary,
                        $salary->net_salary,
                        $salary->status,
                    ]);
                }
            });
    }

    private function exportEmployees($file)
    {
        // Header
        fputcsv($file, [
            'ID', 'Name', 'Email', 'Basic Salary', 'Target Sales',
            'Bonus Per Extra Sale', 'Created At'
        ]);

        // Data
        User::role('Employee')
            ->orderBy('name')
            ->chunk(100, function($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->basic_salary,
                        $user->target_sales,
                        $user->bonus_per_extra_sale,
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });
    }

    private function exportAgents($file)
    {
        // Header
        fputcsv($file, [
            'ID', 'Name', 'Email', 'Target Sales', 'Bonus Per Extra Sale', 'Created At'
        ]);

        // Data
        User::role(['Agent'])
            ->orderBy('name')
            ->chunk(100, function($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->target_sales,
                        $user->bonus_per_extra_sale,
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });
    }
}
