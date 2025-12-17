<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\User;
use App\Services\SalaryCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryCalculationService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * Display salary records
     */
    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $salaries = SalaryRecord::with(['user', 'deductions'])
            ->where('salary_year', $year)
            ->where('salary_month', $month)
            ->latest()
            ->paginate(20);

        $summary = $this->salaryService->getSalarySummary($year, $month);

        return view('salary.index', compact('salaries', 'summary', 'year', 'month'));
    }

    /**
     * Show salary calculation form
     */
    public function create(Request $request)
    {
        $users = User::where('employment_status', 'active')
            ->whereNotNull('basic_salary')
            ->where('basic_salary', '>', 0)
            ->orderBy('name')
            ->get();

        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        return view('salary.create', compact('users', 'year', 'month'));
    }

    /**
     * Calculate salary for specific user
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $salaryRecord = $this->salaryService->calculateSalary(
                $user,
                $request->year,
                $request->month
            );

            return redirect()
                ->route('salary.show', $salaryRecord->id)
                ->with('success', "Salary calculated successfully for {$user->name}.");
        } catch (\Exception $e) {
            Log::error('Salary calculation error: ' . $e->getMessage());
            return back()->with('error', 'Error calculating salary: ' . $e->getMessage());
        }
    }

    /**
     * Calculate salary for all employees
     */
    public function calculateAll(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $results = $this->salaryService->calculateSalaryForAll(
                $request->year,
                $request->month
            );

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            $message = "Salary calculated for {$successCount} employee(s).";
            if ($failedCount > 0) {
                $message .= " {$failedCount} employee(s) failed.";
            }

            return redirect()
                ->route('salary.index', ['year' => $request->year, 'month' => $request->month])
                ->with('success', $message)
                ->with('calculation_results', $results);
        } catch (\Exception $e) {
            Log::error('Batch salary calculation error: ' . $e->getMessage());
            return back()->with('error', 'Error calculating salaries: ' . $e->getMessage());
        }
    }

    /**
     * Show salary record details
     */
    public function show(SalaryRecord $salaryRecord)
    {
        $salaryRecord->load(['user', 'deductions']);
        return view('salary.show', compact('salaryRecord'));
    }

    /**
     * Approve salary record
     */
    public function approve(SalaryRecord $salaryRecord)
    {
        try {
            $this->salaryService->approveSalary($salaryRecord);
            return back()->with('success', 'Salary approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving salary: ' . $e->getMessage());
        }
    }

    /**
     * Mark salary as paid
     */
    public function markPaid(SalaryRecord $salaryRecord)
    {
        try {
            $this->salaryService->markAsPaid($salaryRecord);
            return back()->with('success', 'Salary marked as paid.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error marking salary as paid: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate salary
     */
    public function recalculate(SalaryRecord $salaryRecord)
    {
        try {
            $newRecord = $this->salaryService->recalculateSalary($salaryRecord);
            return redirect()
                ->route('salary.show', $newRecord->id)
                ->with('success', 'Salary recalculated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error recalculating salary: ' . $e->getMessage());
        }
    }

    /**
     * Delete salary record
     */
    public function destroy(SalaryRecord $salaryRecord)
    {
        if ($salaryRecord->status === 'paid') {
            return back()->with('error', 'Cannot delete a paid salary record.');
        }

        $salaryRecord->delete();
        return redirect()
            ->route('salary.index')
            ->with('success', 'Salary record deleted successfully.');
    }

    /**
     * Show user salary settings page
     */
    public function settings()
    {
        $users = User::where('employment_status', 'active')
            ->orderBy('name')
            ->get();

        return view('salary.settings', compact('users'));
    }

    /**
     * Update user salary settings
     */
    public function updateSettings(Request $request, User $user)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'target_sales' => 'nullable|integer|min:0',
            'bonus_per_extra_sale' => 'nullable|numeric|min:0',
            'punctuality_bonus' => 'nullable|numeric|min:0',
            'is_sales_employee' => 'boolean',
        ]);

        $user->update([
            'basic_salary' => $request->basic_salary,
            'target_sales' => $request->target_sales ?? 20,
            'bonus_per_extra_sale' => $request->bonus_per_extra_sale ?? 0,
            'punctuality_bonus' => $request->punctuality_bonus ?? 0,
            'is_sales_employee' => $request->has('is_sales_employee'),
        ]);

        return back()->with('success', "Salary settings updated for {$user->name}.");
    }

    /**
     * Export salary report
     */
    public function export(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $salaries = SalaryRecord::with(['user', 'deductions'])
            ->where('salary_year', $year)
            ->where('salary_month', $month)
            ->get();

        // Return CSV download
        $filename = "salary_report_{$year}_{$month}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($salaries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Basic Salary',
                'Target Sales',
                'Actual Sales',
                'Extra Sales',
                'Sales Bonus',
                'Punctuality Bonus',
                'Total Bonus',
                'Working Days',
                'Present Days',
                'Leave Days',
                'Late Days',
                'Attendance Deductions',
                'Total Deductions',
                'Gross Salary',
                'Net Salary',
                'Status',
            ]);

            foreach ($salaries as $salary) {
                fputcsv($file, [
                    $salary->user->name,
                    $salary->user->employee_id ?? 'N/A',
                    number_format($salary->basic_salary, 2),
                    $salary->target_sales,
                    $salary->actual_sales,
                    $salary->extra_sales,
                    number_format($salary->extra_sales * $salary->bonus_per_extra_sale, 2),
                    number_format($salary->attendance_bonus, 2),
                    number_format($salary->total_bonus, 2),
                    $salary->working_days,
                    $salary->present_days,
                    $salary->leave_days,
                    $salary->late_days,
                    number_format(abs($salary->attendance_deduction), 2),
                    number_format($salary->total_deductions, 2),
                    number_format($salary->gross_salary, 2),
                    number_format($salary->net_salary, 2),
                    ucfirst($salary->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
