<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Carrier;
use App\Models\Lead;
use App\Models\ManualPayrollEntry;
use App\Models\SalaryDeduction;
use App\Models\SalaryRecord;
use App\Models\SalaryComponent;
use App\Models\Setting;
use App\Models\User;
use App\Models\PayrollSetting;
use App\Services\AttendanceService;
use App\Services\RevenueCalculationService;
use App\Services\SalaryService;
use App\Traits\PayrollMonthCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    use PayrollMonthCalculation;
    
    protected $attendanceService;
    protected $revenueService;
    protected $salaryService;

    // Salary calculation constants
    const WORKING_DAYS_PER_MONTH = 22;

    // Punctuality Rules:
    // - 1 off OR 2 half days OR 4+ late arrivals = NO punctuality bonus
    // - Late threshold: 7:15 AM (configured in settings)
    const PUNCTUALITY_DISQUALIFY_OFFS = 1;
    const PUNCTUALITY_DISQUALIFY_HALF_DAYS = 2;
    const PUNCTUALITY_DISQUALIFY_LATE_DAYS = 4;

    public function __construct(AttendanceService $attendanceService, RevenueCalculationService $revenueService, SalaryService $salaryService)
    {
        $this->attendanceService = $attendanceService;
        $this->revenueService = $revenueService;
        $this->salaryService = $salaryService;
    }

    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all users (show everyone, not just those with salary configured)
        $employees = User::with('roles')
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();

        // Get existing salary records for current month
        $existingRecords = SalaryRecord::where('salary_month', $currentMonth)
            ->where('salary_year', $currentYear)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Get revenue summary for dashboard
        $revenueSummary = $this->revenueService->getDashboardSummary($currentYear, $currentMonth);

        return view('admin.salary.index', compact('employees', 'existingRecords', 'currentMonth', 'currentYear', 'revenueSummary'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $month = $request->month;
        $year = $request->year;
        $userIds = $request->user_ids;

        DB::transaction(function () use ($month, $year, $userIds) {
            foreach ($userIds as $userId) {
                try {
                    // NEW: Use SalaryService to create split salary components
                    $components = $this->salaryService->createSalaryComponents($userId, $month, $year);
                    
                    // Log success
                    $user = User::find($userId);
                    \Log::info("Salary components created for {$user->name}: " . implode(', ', array_keys($components)));
                } catch (\Exception $e) {
                    \Log::error("Failed to calculate salary for user {$userId}: " . $e->getMessage());
                    throw $e;
                }
            }
        });

        return redirect()->route('salary.index')
            ->with('success', 'Salaries calculated successfully for selected employees!');
    }

    /**
     * View salary components (basic + bonus sheets)
     * This is the new view for the two-payment structure
     */
    public function components(Request $request)
    {
        $query = SalaryComponent::with('user');

        // Apply filters
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        if ($request->filled('month')) {
            $query->where('salary_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('salary_year', $request->year);
        }

        if ($request->filled('component_type')) {
            $query->where('component_type', $request->component_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Group by employee, month, component
        $components = $query->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->orderBy('payment_date', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Get list of employees for filter dropdown
        $employees = User::where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();

        return view('admin.salary.components', compact('components', 'employees'));
    }

    /**
     * Show single salary component with details
     */
    public function showComponent($componentId)
    {
        $component = SalaryComponent::with('user', 'deductions')->findOrFail($componentId);
        
        return view('admin.salary.component-detail', compact('component'));
    }

    /**
     * View payroll sheet for all employees
     * Shows: Sr#, Employee, DOJ, Basic Salary, Punctuality, Total, Working Days, Qualified, Bonus, Gross
     * 
     * ============================================================================
     * IMPORTANT: HOW PAYROLL MONTH-TO-MONTH FILTERING WORKS
     * ============================================================================
     * 
     * PAYROLL CYCLE: 26th of current month to 25th of next month
     * - Example: Selecting "January 2026" means the period from Dec 26, 2025 to Jan 25, 2026
     * - This is NOT a calendar month, but a custom payroll period
     * 
     * HOW MONTH FILTERING WORKS:
     * - When you select a month/year and click "Filter", the system DYNAMICALLY CALCULATES
     *   all payroll data in real-time for that specific period
     * - NO new database entries are created automatically when you filter
     * - The system queries attendance records, sales data, and deductions for that period
     * - All figures (working days, punctuality, bonus, deductions) are calculated on-the-fly
     * 
     * WHY FIGURES KEEP CHANGING:
     * - Attendance data changes daily (employees mark attendance)
     * - Sales/leads get added throughout the month
     * - Deductions (docks) can be added/modified by HR
     * - Therefore, the same month's payroll will show DIFFERENT figures if viewed at
     *   different times during the month
     * 
     * FINALIZATION PROCESS:
     * - At month-end (around 26th), HR reviews the final calculated figures
     * - If needed, manual adjustments can be made via "updatePayroll()" method
     * - Once approved, payroll is processed and employees get paid
     * - Historical payroll data is preserved in salary_records table for auditing
     * 
     * DATA PERSISTENCE & MONTH SEPARATION:
     * - This method is VIEW-ONLY - it calculates on-the-fly but doesn't save anything
     * - When you click "Edit" and save an employee's payroll, it creates/updates
     *   a record in the `salary_records` table with that month/year
     * - Each month's SalaryRecord is SEPARATE - changing Feb 2026 values will NOT
     *   affect Jan 2026 saved records because they have different month/year values
     * - Query pattern: SalaryRecord::where('user_id', X)->where('salary_month', 1)->where('salary_year', 2026)
     * - This ensures historical payroll integrity - last month's paid salaries are locked
     * 
     * DOCK AMOUNTS:
     * - Dock amounts ARE being calculated and displayed in the view (line 245-249 in Blade)
     * - They use DockRecord::where('dock_month', M)->where('dock_year', Y)->sum('amount')
     * - If docks aren't showing, check: 1) DockRecord entries exist, 2) dock_month/dock_year
     *   are set correctly (use PayrollMonthCalculation trait for 26-25 cycle)
     * ============================================================================
     */
    public function payroll(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        // Calculate payroll period (26th to 25th cycle)
        $payrollPeriod = $this->getPayrollPeriod($month, $year);
        $startDate = $payrollPeriod['start'];
        $endDate = $payrollPeriod['end'];
        
        // Format period for display
        $periodDisplay = $this->formatPayrollPeriod($month, $year);

        // Get ACTIVE employees only (who get paid) - EXCLUDE Shawn
        $query = User::with(['roles', 'userDetail'])
            ->where('status', 'Active')  // Only active employees
            ->where('name', '!=', 'Shawn')  // Exclude Shawn
            ->orderBy('name');

        $employees = $query->get();

        // Calculate totals
        $totalBasicSalary = $employees->sum('basic_salary');
        $totalBonus = 0;
        $qualifiedForBonus = 0;

        foreach ($employees as $employee) {
            if ($employee->is_sales_employee) {
                // Count sales for this employee using DATE RANGE (26th-25th)
                $actualSales = Lead::where(function($q) use ($employee) {
                    $q->where('managed_by', $employee->id)
                      ->orWhere('closer_name', $employee->name);
                })
                ->where('status', 'accepted')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->count();

                $target = $employee->target_sales ?? 20;
                $bonusPerSale = $employee->bonus_per_extra_sale ?? 0;

                if ($actualSales > $target) {
                    $bonus = ($actualSales - $target) * $bonusPerSale;
                    $totalBonus += $bonus;
                    $qualifiedForBonus++;
                }
            }
        }

        // Get total working days from settings (or auto-calculate as fallback)
        $totalWorkingDays = PayrollSetting::getTotalWorkingDays($month, $year);

        // Load manual payroll entries for this period (e.g., ex-employees)
        $manualEntries = ManualPayrollEntry::where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->orderBy('employee_name')
            ->get();

        // Add manual entries' basic salary to total
        $totalBasicSalary += $manualEntries->sum('basic_salary');

        return view('admin.payroll.index', compact('employees', 'totalBasicSalary', 'totalBonus', 'qualifiedForBonus', 'month', 'year', 'startDate', 'endDate', 'periodDisplay', 'totalWorkingDays', 'manualEntries'));
    }

    /**
     * Update total working days for a payroll month
     */
    public function updateWorkingDays(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;
        $workingDays = (int) $request->working_days;

        // Store working days in settings
        PayrollSetting::setTotalWorkingDays($month, $year, $workingDays);

        $monthName = Carbon::create($year, $month, 1)->format('F');
        
        return back()->with('success', "Working days updated to {$workingDays} for {$monthName} {$year}");
    }

    /**
     * Update payroll entry with enhanced functionality
     */
    public function updatePayroll(Request $request, $userId)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0|max:999999.99',
            'punctuality_bonus' => 'nullable|numeric|min:0|max:99999.99',
            'full_days' => 'nullable|integer|min:0|max:31',
            'half_days' => 'nullable|integer|min:0|max:31',
            'late_days' => 'nullable|integer|min:0|max:31',
            'working_days_monthly' => 'nullable|integer|min:0|max:31',
            'override_punctuality_bonus' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'salary_advance' => 'nullable|numeric|min:0',
            'payroll_notes' => 'nullable|string|max:500',
            'current_month' => 'nullable|integer|min:1|max:12',
            'current_year' => 'nullable|integer|min:2020',
        ]);

        $user = User::findOrFail($userId);
        
        // Auto-calculate punctuality qualification based on attendance
        $fullDays = $request->full_days ?? $user->full_days ?? 0;
        $halfDays = $request->half_days ?? $user->half_days ?? 0;
        $lateDays = $request->late_days ?? $user->late_days ?? 0;
        $workingDaysMonthly = $request->working_days_monthly ?? $user->working_days_monthly ?? 22;
        
        // Punctuality logic: 
        // - 2+ half days = disqualified (2 half days = 1 absent)
        // - 4+ late days = disqualified
        // - Otherwise: User is qualified if they have at least (working_days - 1) full days and max 1 half day
        $isQualifiedForPunctuality = ($fullDays >= ($workingDaysMonthly - 1)) && ($halfDays <= 1) && ($lateDays < 4);
        
        $user->update([
            'basic_salary' => $request->basic_salary,
            'punctuality_bonus' => $request->punctuality_bonus ?? $user->punctuality_bonus ?? 0,
            'full_days' => $fullDays,
            'half_days' => $halfDays,
            'late_days' => $lateDays,
            'is_qualified_for_punctuality' => $isQualifiedForPunctuality,
            'working_days_monthly' => $workingDaysMonthly,
            'override_punctuality_bonus' => $request->override_punctuality_bonus ?? $user->override_punctuality_bonus ?? 0,
            'tax_deduction' => $request->tax_deduction ?? $user->tax_deduction ?? 0,
            'other_deductions' => $request->other_deductions ?? $user->other_deductions ?? 0,
            'other_allowances' => $request->other_allowances ?? $user->other_allowances ?? 0,
            'salary_advance' => $request->salary_advance ?? $user->salary_advance ?? 0,
            'payroll_notes' => $request->payroll_notes ?? null,
        ]);

        return back()->with('success', 'Payroll updated successfully for ' . $user->name . '!');
    }

    /**
     * Store a new manual payroll entry (for non-system users like ex-employees)
     */
    public function storeManualEntry(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'join_date' => 'nullable|date',
            'payroll_month' => 'required|integer|min:1|max:12',
            'payroll_year' => 'required|integer|min:2020',
            'basic_salary' => 'required|numeric|min:0|max:999999.99',
            'punctuality_bonus' => 'nullable|numeric|min:0|max:99999.99',
            'full_days' => 'nullable|integer|min:0|max:31',
            'half_days' => 'nullable|integer|min:0|max:31',
            'late_days' => 'nullable|integer|min:0|max:31',
            'is_qualified' => 'nullable|boolean',
            'dock_amount' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'salary_advance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Set defaults
        $validated['punctuality_bonus'] = $validated['punctuality_bonus'] ?? 0;
        $validated['full_days'] = $validated['full_days'] ?? 0;
        $validated['half_days'] = $validated['half_days'] ?? 0;
        $validated['late_days'] = $validated['late_days'] ?? 0;
        $validated['is_qualified'] = $validated['is_qualified'] ?? false;
        $validated['dock_amount'] = $validated['dock_amount'] ?? 0;
        $validated['other_deductions'] = $validated['other_deductions'] ?? 0;
        $validated['other_allowances'] = $validated['other_allowances'] ?? 0;
        $validated['salary_advance'] = $validated['salary_advance'] ?? 0;

        ManualPayrollEntry::create($validated);

        return back()->with('success', 'Manual payroll entry added successfully for ' . $validated['employee_name'] . '!');
    }

    /**
     * Update an existing manual payroll entry
     */
    public function updateManualEntry(Request $request, $id)
    {
        $entry = ManualPayrollEntry::findOrFail($id);

        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'join_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0|max:999999.99',
            'punctuality_bonus' => 'nullable|numeric|min:0|max:99999.99',
            'full_days' => 'nullable|integer|min:0|max:31',
            'half_days' => 'nullable|integer|min:0|max:31',
            'late_days' => 'nullable|integer|min:0|max:31',
            'is_qualified' => 'nullable|boolean',
            'dock_amount' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'salary_advance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $entry->update($validated);

        return back()->with('success', 'Manual payroll entry updated successfully for ' . $entry->employee_name . '!');
    }

    /**
     * Delete a manual payroll entry
     */
    public function destroyManualEntry($id)
    {
        $entry = ManualPayrollEntry::findOrFail($id);
        $name = $entry->employee_name;
        
        $entry->delete();

        return back()->with('success', 'Manual payroll entry deleted successfully for ' . $name . '!');
    }

    /**
     * Approve a salary component
     */
    public function approveComponent(Request $request, $componentId)
    {
        $component = SalaryComponent::findOrFail($componentId);

        $component->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_amount' => $component->net_amount,
        ]);

        return redirect()->back()
            ->with('success', ucfirst($component->component_type) . ' salary approved for ' . $component->user->name);
    }

    /**
     * Mark salary component as paid
     */
    public function markPaidComponent(Request $request, $componentId)
    {
        $component = SalaryComponent::findOrFail($componentId);

        // Only allow marking paid if approved
        if ($component->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Component must be approved before marking as paid');
        }

        $component->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', ucfirst($component->component_type) . ' salary marked as paid on ' . now()->format('d M Y'));
    }

    /**
     * Download payslip for salary component
     */
    public function downloadComponentPayslip($componentId)
    {
        $component = SalaryComponent::with('user')->findOrFail($componentId);
        
        $pdf = Pdf::loadView('admin.salary.component-payslip', [
            'component' => $component,
            'company' => Setting::get('company_name', 'Taurus CRM'),
        ]);

        $filename = "{$component->user->name}-" . strtoupper($component->component_type) . "-{$component->salary_month}-{$component->salary_year}.pdf";
        
        return $pdf->download($filename);
    }

    private function calculateUserSalary($userId, $month, $year)
    {
        $user = User::find($userId);

        // Check if user has basic salary configured
        if (!$user->basic_salary || $user->basic_salary <= 0) {
            throw new \Exception("User {$user->name} does not have a basic salary configured.");
        }

        // 1. Count sales and chargebacks (only if user is sales employee)
        $actualSales = 0;
        $chargebackCount = 0;
        $netApprovedSales = 0;
        $salesBonus = 0;
        $nextMonthTargetAdjustment = 0;
        
        if ($user->is_sales_employee) {
            // Count total sales
            $actualSales = Lead::where(function($query) use ($userId, $user) {
                $query->where('managed_by', $userId)
                      ->orWhere('closer_name', $user->name);
            })
            ->where('status', 'accepted')
            ->whereNotNull('sale_date')
            ->whereMonth('sale_date', $month)
            ->whereYear('sale_date', $year)
            ->count();
            
            // Count chargebacks
            $chargebackCount = Lead::where(function($query) use ($userId, $user) {
                $query->where('managed_by', $userId)
                      ->orWhere('closer_name', $user->name);
            })
            ->where('status', 'chargeback')
            ->whereMonth('chargeback_marked_date', $month)
            ->whereYear('chargeback_marked_date', $year)
            ->count();
            
            // Calculate net approved sales
            $netApprovedSales = $actualSales - $chargebackCount;
            $targetSales = $user->target_sales ?? 20;
            
            // Chargeback logic:
            // If net approved < target: no bonus, adjust next month target
            // If net approved >= target: calculate bonus on net approved - target
            if ($netApprovedSales < $targetSales) {
                // Below target: no bonus, carry forward deficit to next month
                $salesBonus = 0;
                $nextMonthTargetAdjustment = $targetSales - $netApprovedSales;
            } else {
                // At or above target: bonus only on net extras
                $extraSales = $netApprovedSales - $targetSales;
                $bonusPerSale = $user->bonus_per_extra_sale ?? 0;
                $salesBonus = $extraSales * $bonusPerSale;
                $nextMonthTargetAdjustment = 0;
            }
        }

        // 2. Calculate attendance-based adjustments
        $attendanceData = $this->calculateAttendanceAdjustments($userId, $month, $year, $user);

        // 3. Calculate manual dock records for the month
        $dockRecords = \App\Models\DockRecord::where('user_id', $userId)
            ->where('dock_month', $month)
            ->where('dock_year', $year)
            ->where('status', 'active')
            ->get();
            
        $dockDeductions = $dockRecords->sum('amount');
        $dockDetails = $dockRecords->pluck('reason')->join('; ');

        // 4. Calculate totals
        $totalBonus = $salesBonus + $attendanceData['punctuality_bonus'];
        $totalDeductions = abs($attendanceData['attendance_deduction']) + $dockDeductions;
        $grossSalary = $attendanceData['basic_salary_earned'] + $totalBonus;
        $netSalary = $grossSalary - $totalDeductions;

        // Create or update salary record
        $notesArray = [$attendanceData['notes']];
        if ($user->is_sales_employee) {
            $notesArray[] = "Sales: {$actualSales} total, {$chargebackCount} chargebacks, {$netApprovedSales} net approved";
            if ($nextMonthTargetAdjustment > 0) {
                $notesArray[] = "⚠️ Next month target: " . ($user->target_sales ?? 20) . " + {$nextMonthTargetAdjustment} = " . (($user->target_sales ?? 20) + $nextMonthTargetAdjustment);
            }
        }
        if ($dockDeductions > 0) {
            $dockCount = $dockRecords->count();
            $notesArray[] = "Dock Deductions: Rs" . number_format($dockDeductions, 2) . " ({$dockCount} record(s))";
        }
        
        $salaryRecord = SalaryRecord::updateOrCreate(
            [
                'user_id' => $userId,
                'salary_month' => $month,
                'salary_year' => $year,
            ],
            [
                'basic_salary' => $attendanceData['basic_salary_earned'],
                'target_sales' => $user->target_sales ?? 20,
                'actual_sales' => $actualSales, // Total sales (before chargebacks)
                'chargeback_count' => $chargebackCount,
                'net_approved_sales' => $netApprovedSales, // Net after chargebacks
                'next_month_target_adjustment' => $nextMonthTargetAdjustment,
                'extra_sales' => max(0, $netApprovedSales - ($user->target_sales ?? 20)),
                'bonus_per_extra_sale' => $user->bonus_per_extra_sale ?? 0,
                'total_bonus' => $totalBonus,

                // Attendance fields
                'working_days' => $attendanceData['working_days'],
                'present_days' => $attendanceData['present_days'],
                'leave_days' => $attendanceData['leave_days'],
                'late_days' => $attendanceData['late_days'],
                'attendance_bonus' => $attendanceData['punctuality_bonus'],
                'attendance_deduction' => -$attendanceData['attendance_deduction'],
                'daily_salary' => $attendanceData['daily_salary'],
                
                // Dock fields
                'dock_amount' => $dockDeductions,
                'dock_details' => $dockDetails,

                'gross_salary' => $grossSalary,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'status' => 'calculated',
                'calculated_at' => now(),
                'notes' => implode(' | ', array_filter($notesArray)),
            ]
        );

        return $salaryRecord;
    }

    /**
     * Calculate attendance adjustments (punctuality bonus and earned salary)
     * 
     * Attendance Rules:
     * - Working days calculated excluding Sat/Sun from payroll period (26th to 25th)
     * - Daily salary = basic salary / total working days
     * - Eligible days = full_days + half_days (both counted as 1.0 per day)
     * - For mid-period joiners, eligible days capped at working days from join date
     * - Punctuality bonus: No bonus if 1+ offs OR 2+ half days OR 4+ late arrivals
     */
    private function calculateAttendanceAdjustments($userId, $month, $year, $user)
    {
        // Get payroll period (26th of previous month to 25th of current month)
        $payrollPeriod = $this->getPayrollPeriod($month, $year);
        $totalWorkingDays = $this->countWorkingDays($payrollPeriod['start'], $payrollPeriod['end']);
        
        // Calculate per-day salary based on actual working days
        $dailySalary = $user->basic_salary / $totalWorkingDays;
        
        // Get eligible days from manual fields (full_days + half_days, both count as 1.0)
        $eligibleDays = ($user->full_days ?? 0) + ($user->half_days ?? 0);
        
        // Handle join date: cap eligible days or set to 0 if joined after period
        if ($user->joining_date) {
            $joiningDate = Carbon::parse($user->joining_date);
            
            // If joined after the payroll period ends, eligible days = 0
            if ($joiningDate->gt($payrollPeriod['end'])) {
                $eligibleDays = 0;
            }
            // If joined within the period, cap at working days from join date
            elseif ($joiningDate->between($payrollPeriod['start'], $payrollPeriod['end'])) {
                $maxAllowedDays = $this->countWorkingDays($joiningDate, $payrollPeriod['end']);
                $eligibleDays = min($eligibleDays, $maxAllowedDays);
            }
            // If joined before period start, use full eligible days
        }

        // Use manual fields for attendance tracking
        $fullDays = $user->full_days ?? 0;
        $halfDays = $user->half_days ?? 0;
        $lateDays = $user->late_days ?? 0;
        $leaveDays = 0; // Not directly tracked, but can be inferred if needed
        
        // Calculate basic salary earned based on eligible days worked
        $basicSalaryEarned = $eligibleDays * $dailySalary;
        
        // No attendance deductions - salary is based on days worked
        $attendanceDeduction = 0;
        $notes = [];
        $notes[] = "Working days in period: {$totalWorkingDays}";
        $notes[] = "Eligible days: {$eligibleDays} (Full: {$fullDays}, Half: {$halfDays})";
        $notes[] = "Daily salary: Rs" . number_format($dailySalary, 2) . " × {$eligibleDays} days = Rs" . number_format($basicSalaryEarned, 2);

        // Calculate punctuality bonus
        $punctualityBonus = 0;
        $userPunctualityBonus = $user->punctuality_bonus ?? 0;
        
        if ($userPunctualityBonus > 0) {
            // Check disqualification criteria
            $disqualified = false;
            $disqualifyReason = '';
            
            if ($leaveDays >= self::PUNCTUALITY_DISQUALIFY_OFFS) {
                $disqualified = true;
                $disqualifyReason = "{$leaveDays} off(s) (max 0)";
            } elseif ($halfDays >= self::PUNCTUALITY_DISQUALIFY_HALF_DAYS) {
                $disqualified = true;
                $disqualifyReason = "{$halfDays} half day(s) (max 1)";
            } elseif ($lateDays >= self::PUNCTUALITY_DISQUALIFY_LATE_DAYS) {
                $disqualified = true;
                $disqualifyReason = "{$lateDays} late arrival(s) (max 3)";
            }
            
            if (!$disqualified) {
                $punctualityBonus = $userPunctualityBonus;
                $notes[] = "Punctuality bonus earned: Rs" . number_format($punctualityBonus, 2);
            } else {
                $notes[] = "Punctuality bonus not earned: {$disqualifyReason}";
            }
        }

        // Summary notes
        if ($lateDays > 0) {
            $notes[] = "{$lateDays} late arrival(s) after 7:15 AM";
        }

        return [
            'present_days' => $eligibleDays,
            'leave_days' => $leaveDays,
            'half_days' => $halfDays,
            'late_days' => $lateDays,
            'daily_salary' => round($dailySalary, 2),
            'working_days' => $totalWorkingDays,
            'basic_salary_earned' => round($basicSalaryEarned, 2),
            'punctuality_bonus' => $punctualityBonus,
            'attendance_deduction' => $attendanceDeduction,
            'notes' => implode(' | ', $notes),
        ];
    }

    /**
     * Calculate working days in a month (excluding weekends)
     * Respects your weekend attendance settings
     */
    private function calculateWorkingDays($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $workingDays = 0;

        $allowWeekendAttendance = Setting::get('allow_weekend_attendance', false);

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if ($allowWeekendAttendance) {
                // If weekend attendance is allowed, count all days
                $workingDays++;
            } else {
                // Only count Monday to Friday (1-5), excluding Saturday (6) and Sunday (0)
                if ($currentDate->dayOfWeek >= 1 && $currentDate->dayOfWeek <= 5) {
                    $workingDays++;
                }
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    public function records(Request $request)
    {
        $query = SalaryRecord::with(['user', 'deductions']);

        // Apply filters
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        if ($request->filled('month')) {
            $query->where('salary_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('salary_year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Default ordering
        $query->orderBy('salary_year', 'desc')
            ->orderBy('salary_month', 'desc')
            ->orderBy('created_at', 'desc');

        $salaryRecords = $query->paginate(20)->withQueryString();

        return view('admin.salary.records', compact('salaryRecords'));
    }

    public function show(SalaryRecord $salaryRecord)
    {
        $salaryRecord->load(['user', 'deductions']);

        // Get sales details for this period
        $salesDetails = Lead::where('forwarded_by', $salaryRecord->user_id)
            ->whereNotNull('sale_at')
            ->whereMonth('sale_at', $salaryRecord->salary_month)
            ->whereYear('sale_at', $salaryRecord->salary_year)
            ->orderBy('sale_at', 'desc')
            ->get();

        // Get attendance details for this period using your existing model
        $attendanceDetails = Attendance::where('user_id', $salaryRecord->user_id)
            ->whereMonth('date', $salaryRecord->salary_month)
            ->whereYear('date', $salaryRecord->salary_year)
            ->orderBy('date', 'asc')
            ->get();

        // Generate working days calendar
        $workingDaysCalendar = $this->generateAttendanceCalendar(
            $salaryRecord->user_id,
            $salaryRecord->salary_month,
            $salaryRecord->salary_year
        );

        // Get attendance settings for context
        $attendanceSettings = [
            'office_start_time' => Setting::get('office_start_time', '09:00'),
            'late_threshold_minutes' => Setting::get('late_threshold_minutes', 15),
            'allow_weekend_attendance' => Setting::get('allow_weekend_attendance', false),
            'office_networks' => Setting::get('office_networks', []),
        ];

        return view('admin.salary.show', compact(
            'salaryRecord',
            'salesDetails',
            'attendanceDetails',
            'workingDaysCalendar',
            'attendanceSettings'
        ));
    }

    /**
     * Generate attendance calendar compatible with your existing system
     */
    private function generateAttendanceCalendar($userId, $month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendanceRecords = Attendance::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $allowWeekendAttendance = Setting::get('allow_weekend_attendance', false);
        $calendar = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $isWeekend = $currentDate->isWeekend();
            $isWorkingDay = $allowWeekendAttendance ? true : ! $isWeekend;

            $attendance = $attendanceRecords->get($dateString);

            if ($isWorkingDay) {
                $status = $attendance ? $attendance->status : 'missing';
                $workingHours = 0;

                if ($attendance && $attendance->login_time && $attendance->logout_time) {
                    $workingHours = Carbon::parse($attendance->logout_time)
                        ->diffInHours(Carbon::parse($attendance->login_time), true);
                }
            } else {
                $status = 'weekend';
                $workingHours = 0;
            }

            $calendar[] = [
                'date' => $currentDate->copy(),
                'day_name' => $currentDate->format('D'),
                'is_working_day' => $isWorkingDay,
                'is_weekend' => $isWeekend,
                'status' => $status,
                'login_time' => $attendance ? $attendance->login_time : null,
                'logout_time' => $attendance ? $attendance->logout_time : null,
                'working_hours' => round($workingHours, 1),
                'ip_address' => $attendance ? $attendance->ip_address : null,
                'formatted_login' => $attendance && $attendance->login_time ?
                    Carbon::parse($attendance->login_time)->format('H:i') : null,
                'formatted_logout' => $attendance && $attendance->logout_time ?
                    Carbon::parse($attendance->logout_time)->format('H:i') : null,
            ];

            $currentDate->addDay();
        }

        return $calendar;
    }

    public function addDeduction(Request $request, SalaryRecord $salaryRecord)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_percentage' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $salaryRecord) {
            // Create deduction
            $salaryRecord->deductions()->create([
                'type' => $request->type,
                'description' => $request->description,
                'amount' => $request->amount,
                'is_percentage' => $request->boolean('is_percentage'),
                'notes' => $request->notes,
            ]);

            // Recalculate total deductions and net salary
            $this->recalculateDeductions($salaryRecord);
        });

        return response()->json(['success' => true, 'message' => 'Deduction added successfully!']);
    }

    public function removeDeduction(SalaryDeduction $deduction)
    {
        $salaryRecord = $deduction->salaryRecord;

        DB::transaction(function () use ($deduction, $salaryRecord) {
            $deduction->delete();
            $this->recalculateDeductions($salaryRecord);
        });

        return response()->json(['success' => true, 'message' => 'Deduction removed successfully!']);
    }

    public function approve(SalaryRecord $salaryRecord)
    {
        if ($salaryRecord->status !== 'calculated') {
            return response()->json(['success' => false, 'message' => 'Only calculated salaries can be approved.']);
        }

        $salaryRecord->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Salary record approved successfully!']);
    }

    public function markPaid(SalaryRecord $salaryRecord)
    {
        if ($salaryRecord->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved salaries can be marked as paid.']);
        }

        $salaryRecord->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Salary record marked as paid successfully!']);
    }

    public function employees(Request $request)
    {
        // Get all users (show everyone for salary configuration)
        $query = User::with('roles')
            ->where('status', '!=', 'inactive')
            ->orderBy('name');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply salary range filter
        if ($request->filled('salary_range')) {
            $range = $request->salary_range;
            switch ($range) {
                case '0-25000':
                    $query->whereBetween('basic_salary', [0, 25000]);
                    break;
                case '25000-50000':
                    $query->whereBetween('basic_salary', [25000, 50000]);
                    break;
                case '50000-100000':
                    $query->whereBetween('basic_salary', [50000, 100000]);
                    break;
                case '100000+':
                    $query->where('basic_salary', '>', 100000);
                    break;
            }
        }

        // Apply target sales filter
        if ($request->filled('target_range')) {
            $range = $request->target_range;
            switch ($range) {
                case '0-10':
                    $query->whereBetween('target_sales', [0, 10]);
                    break;
                case '10-20':
                    $query->whereBetween('target_sales', [10, 20]);
                    break;
                case '20-50':
                    $query->whereBetween('target_sales', [20, 50]);
                    break;
                case '50+':
                    $query->where('target_sales', '>', 50);
                    break;
            }
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.salary.employees', compact('employees'));
    }

    public function updateEmployee(Request $request, User $user)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0|max:999999.99',
            'target_sales' => 'nullable|integer|min:0|max:1000',
            'bonus_per_extra_sale' => 'nullable|numeric|min:0|max:9999.99',
            'punctuality_bonus' => 'nullable|numeric|min:0|max:9999.99',
            'fine_per_absence' => 'nullable|numeric|min:0|max:9999.99',
            'fine_per_late' => 'nullable|numeric|min:0|max:9999.99',
            'salary_start_date' => 'nullable|date',
            'salary_end_date' => 'nullable|date|after_or_equal:salary_start_date',
            'payday_date' => 'nullable|integer|min:1|max:31',
            'is_sales_employee' => 'boolean',
        ]);

        $user->update([
            'basic_salary' => $request->basic_salary,
            'target_sales' => $request->target_sales ?? 20,
            'bonus_per_extra_sale' => $request->bonus_per_extra_sale ?? 0,
            'punctuality_bonus' => $request->punctuality_bonus ?? 0,
            'fine_per_absence' => $request->fine_per_absence ?? 0,
            'fine_per_late' => $request->fine_per_late ?? 0,
            'salary_start_date' => $request->salary_start_date,
            'salary_end_date' => $request->salary_end_date,
            'payday_date' => $request->payday_date ?? 5,
            'is_sales_employee' => $request->has('is_sales_employee') ? $request->boolean('is_sales_employee') : true,
        ]);

        return redirect()->back()->with('success', 'Employee salary settings updated successfully for '.$user->name);
    }

    public function downloadPayslip(SalaryRecord $salaryRecord)
    {
        $salaryRecord->load(['user', 'deductions']);

        // Get sales details for this period
        $salesDetails = Lead::where('forwarded_by', $salaryRecord->user_id)
            ->whereNotNull('sale_at')
            ->whereMonth('sale_at', $salaryRecord->salary_month)
            ->whereYear('sale_at', $salaryRecord->salary_year)
            ->orderBy('sale_at', 'desc')
            ->get();

        // Get attendance details for this period
        $attendanceDetails = Attendance::where('user_id', $salaryRecord->user_id)
            ->whereMonth('date', $salaryRecord->salary_month)
            ->whereYear('date', $salaryRecord->salary_year)
            ->orderBy('date', 'asc')
            ->get();

        // Get attendance settings for payslip context
        $attendanceSettings = [
            'office_start_time' => Setting::get('office_start_time', '09:00'),
            'late_threshold_minutes' => Setting::get('late_threshold_minutes', 15),
            'perfect_attendance_bonus' => self::PERFECT_ATTENDANCE_BONUS,
            'sandwich_rule_days' => self::SANDWICH_RULE_PENALTY_DAYS,
        ];

        // Generate PDF
        $pdf = PDF::loadView('admin.salary.payslip', compact(
            'salaryRecord',
            'salesDetails',
            'attendanceDetails',
            'attendanceSettings'
        ));

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Generate filename
        $filename = sprintf(
            'payslip_%s_%s_%s.pdf',
            str_replace(' ', '_', strtolower($salaryRecord->user->name)),
            strtolower($salaryRecord->month_name),
            $salaryRecord->salary_year
        );

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Get attendance summary for an employee (AJAX endpoint)
     */
    public function getAttendanceSummary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $userId = $request->user_id;
        $month = $request->month;
        $year = $request->year;

        $user = User::find($userId);
        $attendanceData = $this->calculateAttendanceAdjustments($userId, $month, $year, $user);

        return response()->json([
            'success' => true,
            'data' => $attendanceData,
            'settings' => [
                'late_threshold' => '07:15',
                'working_days' => self::WORKING_DAYS_PER_MONTH,
                'punctuality_rules' => [
                    'max_offs' => self::PUNCTUALITY_DISQUALIFY_OFFS - 1,
                    'max_half_days' => self::PUNCTUALITY_DISQUALIFY_HALF_DAYS - 1,
                    'max_late_arrivals' => self::PUNCTUALITY_DISQUALIFY_LATE_DAYS - 1,
                ],
            ],
        ]);
    }

    private function recalculateDeductions(SalaryRecord $salaryRecord)
    {
        $totalDeductions = $salaryRecord->deductions->sum(function ($deduction) use ($salaryRecord) {
            if ($deduction->is_percentage) {
                return ($salaryRecord->basic_salary * $deduction->amount) / 100;
            }

            return $deduction->amount;
        });

        $salaryRecord->update([
            'total_deductions' => $totalDeductions,
            'net_salary' => $salaryRecord->gross_salary - $totalDeductions,
        ]);
    }

    /**
     * Print payroll page - matches exactly what's shown on main payroll page
     */
    public function printPayroll(Request $request)
    {
        try {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);

            // Calculate payroll period (26th to 25th cycle) - SAME AS MAIN PAYROLL PAGE
            $payrollPeriod = $this->getPayrollPeriod($month, $year);
            $startDate = $payrollPeriod['start'];
            $endDate = $payrollPeriod['end'];
            
            // Format period for display
            $payrollPeriodFormatted = $this->formatPayrollPeriod($month, $year);

            // Get ACTIVE employees only - EXCLUDE Shawn
            $employees = User::with(['roles', 'userDetail'])
                ->where('status', 'Active')
                ->where('name', '!=', 'Shawn')
                ->orderBy('name')
                ->get();

            // Get total working days from settings
            $totalWorkingDays = PayrollSetting::getTotalWorkingDays($month, $year);

            // Load manual payroll entries for this period
            $manualEntries = ManualPayrollEntry::where('payroll_month', $month)
                ->where('payroll_year', $year)
                ->orderBy('employee_name')
                ->get();

            // Calculate payroll data - EXACT SAME LOGIC AS MAIN PAYROLL PAGE
            $payrollData = [];
            $totalBasicSalary = 0;
            $totalPunctuality = 0;
            $totalAllowances = 0;
            $totalTotal = 0;  // Sum of Total column (earned salary + punctuality)
            $totalDock = 0;
            $totalDeductions = 0;
            $totalNetSalary = 0;
            $totalAdvance = 0;
            $totalPayable = 0;
            $qualifiedForBonus = 0;

            foreach ($employees as $employee) {
                $basicSalary = $employee->basic_salary ?? 0;
                $joinDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date)->format('d M Y') : 'N/A';
                $joiningDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date) : null;
                
                // Calculate per-day wage
                $perDayWage = $basicSalary / max($totalWorkingDays, 1);
                
                // Get eligible days from user fields
                $fullDays = $employee->full_days ?? 0;
                $halfDays = $employee->half_days ?? 0;
                $lateDays = $employee->late_days ?? 0;
                $eligibleDays = $fullDays + $halfDays;
                
                // Handle join date
                if ($joiningDate) {
                    if ($joiningDate->gt($endDate)) {
                        $eligibleDays = 0;
                    } elseif ($joiningDate->between($startDate, $endDate)) {
                        $maxAllowedDays = 0;
                        $current = $joiningDate->copy();
                        while ($current->lte($endDate)) {
                            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                                $maxAllowedDays++;
                            }
                            $current->addDay();
                        }
                        $eligibleDays = min($eligibleDays, $maxAllowedDays);
                    }
                }
                
                // Calculate earned salary
                $earnedSalary = $eligibleDays * $perDayWage;
                
                // Punctuality qualification check
                $isQualified = true;
                if ($halfDays >= 2) {
                    $isQualified = false;
                } elseif ($lateDays >= 4) {
                    $isQualified = false;
                } else {
                    if ($halfDays == 1) {
                        $requiredFullDays = $totalWorkingDays - 1;
                        $isQualified = ($fullDays >= $requiredFullDays);
                    } elseif ($halfDays == 0) {
                        $requiredFullDays = $totalWorkingDays;
                        $isQualified = ($fullDays >= $requiredFullDays);
                    }
                }
                
                // Apply punctuality bonus
                $punctualityBonus = 0;
                if ($isQualified && $employee->punctuality_bonus && $employee->punctuality_bonus > 0) {
                    $punctualityBonus = $employee->punctuality_bonus;
                }
                
                // Override punctuality bonus
                if ($employee->override_punctuality_bonus && $employee->override_punctuality_bonus > 0) {
                    $punctualityBonus = $employee->override_punctuality_bonus;
                    $isQualified = true;
                }
                
                // Get allowances
                $otherAllowances = $employee->other_allowances ?? 0;
                
                // Calculate total (includes allowances)
                $total = $earnedSalary + $punctualityBonus + $otherAllowances;
                
                // Get dock amount for payroll period (26th to 25th)
                $dockAmount = \App\Models\DockRecord::where('user_id', $employee->id)
                    ->whereDate('dock_date', '>=', $startDate->format('Y-m-d'))
                    ->whereDate('dock_date', '<=', $endDate->format('Y-m-d'))
                    ->where('status', 'active')
                    ->sum('amount');
                
                // Deductions
                $taxDeduction = $employee->tax_deduction ?? 0;
                $otherDeductions = $employee->other_deductions ?? 0;
                $totalDeductionsForEmployee = $taxDeduction + $otherDeductions + $dockAmount;
                
                // Net salary
                $netSalary = $total - $totalDeductionsForEmployee;
                $advance = $employee->salary_advance ?? 0;
                $payable = $netSalary - $advance;
                
                $payrollData[] = [
                    'employee' => $employee,
                    'joinDate' => $joinDate,
                    'basicSalary' => $basicSalary,
                    'perDayWage' => $perDayWage,
                    'punctualityBonus' => $punctualityBonus,
                    'total' => $total,
                    'fullDays' => $fullDays,
                    'halfDays' => $halfDays,
                    'lateDays' => $lateDays,
                    'isQualified' => $isQualified,
                    'dockAmount' => $dockAmount,
                    'otherDeductions' => $taxDeduction + $otherDeductions,
                    'netSalary' => $netSalary,
                    'advance' => $advance,
                    'payable' => $payable,
                ];
                
                $totalBasicSalary += $basicSalary;
                $totalPunctuality += $punctualityBonus;
                $totalAllowances += $otherAllowances;
                $totalTotal += $total;
                if ($punctualityBonus > 0) {
                    $qualifiedForBonus++;
                }
                $totalDock += $dockAmount;
                $totalDeductions += $totalDeductionsForEmployee;
                $totalNetSalary += $netSalary;
                $totalAdvance += $advance;
                $totalPayable += $payable;
            }
            
            // Process manual payroll entries (same calculation logic)
            foreach ($manualEntries as $entry) {
                $basicSalary = $entry->basic_salary ?? 0;
                $joinDate = $entry->join_date ? \Carbon\Carbon::parse($entry->join_date)->format('d M Y') : 'N/A';
                $perDayWage = $basicSalary / max($totalWorkingDays, 1);
                $fullDays = $entry->full_days ?? 0;
                $halfDays = $entry->half_days ?? 0;
                $lateDays = $entry->late_days ?? 0;
                $eligibleDays = $fullDays + $halfDays;
                $earnedSalary = $eligibleDays * $perDayWage;
                
                $punctualityBonus = ($entry->is_qualified && $entry->punctuality_bonus) ? $entry->punctuality_bonus : 0;
                $otherAllowances = $entry->other_allowances ?? 0;
                $total = $earnedSalary + $punctualityBonus + $otherAllowances;
                $dockAmount = $entry->dock_amount ?? 0;
                $otherDeductions = $entry->other_deductions ?? 0;
                $netSalary = $total - $dockAmount - $otherDeductions;
                $advance = $entry->salary_advance ?? 0;
                $payable = $netSalary - $advance;
                
                $payrollData[] = [
                    'isManual' => true,
                    'employeeName' => $entry->employee_name,
                    'joinDate' => $joinDate,
                    'basicSalary' => $basicSalary,
                    'perDayWage' => $perDayWage,
                    'punctualityBonus' => $punctualityBonus,
                    'total' => $total,
                    'fullDays' => $fullDays,
                    'halfDays' => $halfDays,
                    'lateDays' => $lateDays,
                    'isQualified' => $entry->is_qualified,
                    'dockAmount' => $dockAmount,
                    'otherDeductions' => $otherDeductions,
                    'netSalary' => $netSalary,
                    'advance' => $advance,
                    'payable' => $payable,
                ];
                
                $totalBasicSalary += $basicSalary;
                $totalPunctuality += $punctualityBonus;
                $totalAllowances += $otherAllowances;
                $totalTotal += $total;
                if ($punctualityBonus > 0) {
                    $qualifiedForBonus++;
                }
                $totalDock += $dockAmount;
                $totalDeductions += ($dockAmount + $otherDeductions);
                $totalNetSalary += $netSalary;
                $totalAdvance += $advance;
                $totalPayable += $payable;
            }
            
            return view('admin.payroll.print', [
                'payrollData' => $payrollData,
                'month' => $month,
                'year' => $year,
                'payrollPeriod' => $payrollPeriodFormatted,
                'totalBasicSalary' => $totalBasicSalary,
                'totalPunctuality' => $totalPunctuality,
                'totalAllowances' => $totalAllowances,
                'totalTotal' => $totalTotal,
                'totalDock' => $totalDock,
                'totalDeductions' => $totalDeductions,
                'totalNetSalary' => $totalNetSalary,
                'totalAdvance' => $totalAdvance,
                'totalPayable' => $totalPayable,
                'qualifiedForBonus' => $qualifiedForBonus,
                'totalEmployees' => $employees->count() + $manualEntries->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Payroll print error: ' . $e->getMessage());
            return back()->with('error', 'Error generating payroll print: ' . $e->getMessage());
        }
    }
}
