<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Carrier;
use App\Models\Lead;
use App\Models\SalaryDeduction;
use App\Models\SalaryRecord;
use App\Models\Setting;
use App\Models\User;
use App\Services\AttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    protected $attendanceService;

    // Salary calculation constants
    const WORKING_DAYS_PER_MONTH = 22;

    // Punctuality Rules:
    // - 1 off OR 2 half days OR 4+ late arrivals = NO punctuality bonus
    // - Late threshold: 7:15 AM (configured in settings)
    const PUNCTUALITY_DISQUALIFY_OFFS = 1;
    const PUNCTUALITY_DISQUALIFY_HALF_DAYS = 2;
    const PUNCTUALITY_DISQUALIFY_LATE_DAYS = 4;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
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

        return view('admin.salary.index', compact('employees', 'existingRecords', 'currentMonth', 'currentYear'));
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
                $this->calculateUserSalary($userId, $month, $year);
            }
        });

        return redirect()->route('salary.index')
            ->with('success', 'Salaries calculated successfully for selected employees!');
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
        $dockDeductions = \App\Models\DockRecord::where('user_id', $userId)
            ->where('dock_month', $month)
            ->where('dock_year', $year)
            ->where('status', 'active')
            ->sum('amount');

        // 4. Calculate totals
        $totalBonus = $salesBonus + $attendanceData['punctuality_bonus'];
        $totalDeductions = abs($attendanceData['attendance_deduction']) + $dockDeductions;
        $grossSalary = $user->basic_salary + $totalBonus;
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
            $dockCount = \App\Models\DockRecord::where('user_id', $userId)
                ->where('dock_month', $month)
                ->where('dock_year', $year)
                ->where('status', 'active')
                ->count();
            $notesArray[] = "Dock Deductions: Rs" . number_format($dockDeductions, 2) . " ({$dockCount} record(s))";
        }
        
        $salaryRecord = SalaryRecord::updateOrCreate(
            [
                'user_id' => $userId,
                'salary_month' => $month,
                'salary_year' => $year,
            ],
            [
                'basic_salary' => $user->basic_salary,
                'target_sales' => $user->target_sales ?? 20,
                'actual_sales' => $actualSales, // Total sales (before chargebacks)
                'chargeback_count' => $chargebackCount,
                'net_approved_sales' => $netApprovedSales, // Net after chargebacks
                'next_month_target_adjustment' => $nextMonthTargetAdjustment,
                'extra_sales' => max(0, $netApprovedSales - ($user->target_sales ?? 20)),
                'bonus_per_extra_sale' => $user->bonus_per_extra_sale ?? 0,
                'total_bonus' => $totalBonus,

                // Attendance fields
                'working_days' => self::WORKING_DAYS_PER_MONTH,
                'present_days' => $attendanceData['present_days'],
                'leave_days' => $attendanceData['leave_days'],
                'late_days' => $attendanceData['late_days'],
                'attendance_bonus' => $attendanceData['punctuality_bonus'],
                'attendance_deduction' => -$attendanceData['attendance_deduction'],
                'daily_salary' => $attendanceData['daily_salary'],

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
     * Calculate attendance-based salary adjustments
     * NEW RULES:
     * - 22 working days formula: daily salary = basic salary / 22
     * - Deductions: full leave = 1 day salary, half day = 0.5 day salary
     * - Punctuality bonus: No bonus if 1+ offs OR 2+ half days OR 4+ late arrivals (after 7:15)
     */
    private function calculateAttendanceAdjustments($userId, $month, $year, $user)
    {
        // Calculate daily salary based on 22 working days
        $dailySalary = $user->basic_salary / self::WORKING_DAYS_PER_MONTH;

        // Get attendance records for the month
        $attendanceRecords = Attendance::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Count different attendance types
        $presentDays = 0;
        $lateDays = 0;
        $leaveDays = 0;
        $halfDays = 0;
        
        // Late threshold: 7:15 AM
        $lateThresholdTime = Carbon::parse('07:15:00');

        foreach ($attendanceRecords as $record) {
            if ($record->status === 'present') {
                $presentDays++;
                
                // Check if late (after 7:15)
                if ($record->login_time) {
                    $loginTime = Carbon::parse($record->login_time);
                    $checkTime = Carbon::parse($record->date->format('Y-m-d') . ' 07:15:00');
                    if ($loginTime->gt($checkTime)) {
                        $lateDays++;
                    }
                }
            } elseif ($record->status === 'late') {
                $presentDays++;
                $lateDays++;
            } elseif ($record->status === 'half_day') {
                $halfDays++;
                $presentDays += 0.5;
            } elseif (in_array($record->status, ['absent', 'leave', 'off'])) {
                $leaveDays++;
            }
        }

        // Calculate deductions
        $attendanceDeduction = 0;
        $notes = [];

        // Deduction for full leave days (daily salary deduction)
        if ($leaveDays > 0) {
            $leaveDeduction = $leaveDays * $dailySalary;
            $attendanceDeduction += $leaveDeduction;
            $notes[] = "{$leaveDays} leave day(s): Rs" . number_format($leaveDeduction, 2);
        }

        // Deduction for half days (half of daily salary)
        if ($halfDays > 0) {
            $halfDayDeduction = $halfDays * ($dailySalary / 2);
            $attendanceDeduction += $halfDayDeduction;
            $notes[] = "{$halfDays} half day(s): Rs" . number_format($halfDayDeduction, 2);
        }

        // Apply automatic fines (in addition to salary deductions)
        // Fine for absence (if configured)
        if ($leaveDays > 0 && $user->fine_per_absence && $user->fine_per_absence > 0) {
            $absenceFine = $leaveDays * $user->fine_per_absence;
            $attendanceDeduction += $absenceFine;
            $notes[] = "Fine for {$leaveDays} absence(s): Rs" . number_format($absenceFine, 2);
        }

        // Fine for late arrivals (if configured)
        if ($lateDays > 0 && $user->fine_per_late && $user->fine_per_late > 0) {
            $lateFine = $lateDays * $user->fine_per_late;
            $attendanceDeduction += $lateFine;
            $notes[] = "Fine for {$lateDays} late arrival(s): Rs" . number_format($lateFine, 2);
        }

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
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
            'half_days' => $halfDays,
            'late_days' => $lateDays,
            'daily_salary' => round($dailySalary, 2),
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
}
