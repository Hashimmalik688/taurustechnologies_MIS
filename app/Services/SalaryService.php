<?php

namespace App\Services;

use App\Models\SalaryComponent;
use App\Models\Lead;
use App\Models\DockRecord;
use App\Models\User;
use Carbon\Carbon;

class SalaryService
{
    protected $attendanceService;

    const WORKING_DAYS_PER_MONTH = 22;
    const PUNCTUALITY_DISQUALIFY_OFFS = 1;
    const PUNCTUALITY_DISQUALIFY_HALF_DAYS = 3;
    const PUNCTUALITY_DISQUALIFY_LATE_DAYS = 4;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Create two salary components (basic and bonus) for an employee
     * Basic payment: payday_date (default 10th)
     * Bonus payment: bonus_payday_date (default 20th, only for sales employees)
     */
    public function createSalaryComponents($userId, $month, $year)
    {
        $user = User::find($userId);

        if (!$user || !$user->basic_salary || $user->basic_salary <= 0) {
            $userName = $user ? $user->name : 'Unknown';
            throw new \Exception("User {$userName} does not have a basic salary configured.");
        }

        $components = [];

        // 1. Create BASIC SALARY component (10th of month)
        $basicPaymentDate = $this->getPaymentDate($month, $year, $user->payday_date ?? 10);
        $basicComponent = $this->calculateBasicSalary($user, $month, $year, $basicPaymentDate);
        $components['basic'] = $basicComponent;

        // 2. Create BONUS SALARY component (20th of month, only for sales employees)
        if ($user->is_sales_employee) {
            $bonusPaymentDate = $this->getPaymentDate($month, $year, $user->bonus_payday_date ?? 20);
            $bonusComponent = $this->calculateBonusSalary($user, $month, $year, $bonusPaymentDate);
            $components['bonus'] = $bonusComponent;
        }

        return $components;
    }

    /**
     * Calculate BASIC SALARY component
     * Includes: basic_salary + attendance deductions/bonuses (excluding sales bonuses)
     * Deductions: attendance fines, dock records, manual deductions
     */
    private function calculateBasicSalary(User $user, $month, $year, $basicPaymentDate)
    {
        // Get attendance data
        $attendanceData = $this->getAttendanceData($user->id, $month, $year);
        
        // Get dock deductions
        $dockDeductions = DockRecord::where('user_id', $user->id)
            ->where('dock_month', $month)
            ->where('dock_year', $year)
            ->where('status', 'active')
            ->sum('amount') ?? 0;

        // Calculate total deductions
        $totalDeductions = abs($attendanceData['attendance_deduction']) + $dockDeductions;

        // Calculate amounts
        $calculatedAmount = $user->basic_salary + $attendanceData['attendance_bonus'];
        $netAmount = $calculatedAmount - $totalDeductions;

        // Create component
        $component = SalaryComponent::updateOrCreate(
            [
                'user_id' => $user->id,
                'salary_year' => $year,
                'salary_month' => $month,
                'component_type' => 'basic',
                'payment_date' => $basicPaymentDate,
            ],
            [
                'basic_salary' => $user->basic_salary,
                'calculated_amount' => $calculatedAmount,
                'deductions' => $totalDeductions,
                'net_amount' => $netAmount,
                
                // Attendance data
                'working_days' => self::WORKING_DAYS_PER_MONTH,
                'present_days' => $attendanceData['present_days'],
                'leave_days' => $attendanceData['leave_days'],
                'late_days' => $attendanceData['late_days'],
                'daily_salary' => $attendanceData['daily_salary'],
                'attendance_bonus' => $attendanceData['attendance_bonus'],
                'attendance_deduction' => $attendanceData['attendance_deduction'],
                
                // Deduction breakdown
                'dock_deductions' => $dockDeductions,
                
                'status' => 'calculated',
                'calculated_at' => now(),
                'notes' => $attendanceData['notes'],
            ]
        );

        return $component;
    }

    /**
     * Calculate BONUS SALARY component
     * Includes: sales bonus + punctuality bonus
     * Only created for is_sales_employee = true
     */
    private function calculateBonusSalary(User $user, $month, $year, $bonusPaymentDate)
    {
        // Get sales data
        $salesData = $this->getSalesData($user, $month, $year);
        
        // Get attendance data (for punctuality bonus)
        $attendanceData = $this->getAttendanceData($user->id, $month, $year);
        
        // Calculate total bonus
        $totalBonus = $salesData['sales_bonus'] + $attendanceData['attendance_bonus'];
        
        // Bonus component typically has no deductions (or minimal)
        // Decide: Should bonuses be deductible? For now, no deductions
        $netAmount = $totalBonus;

        // Create component
        $component = SalaryComponent::updateOrCreate(
            [
                'user_id' => $user->id,
                'salary_year' => $year,
                'salary_month' => $month,
                'component_type' => 'bonus',
                'payment_date' => $bonusPaymentDate,
            ],
            [
                'calculated_amount' => $totalBonus,
                'deductions' => 0,
                'net_amount' => $netAmount,
                
                // Sales data
                'target_sales' => $user->target_sales ?? 20,
                'actual_sales' => $salesData['actual_sales'],
                'chargeback_count' => $salesData['chargeback_count'],
                'net_approved_sales' => $salesData['net_approved_sales'],
                'extra_sales' => $salesData['extra_sales'],
                'bonus_per_extra_sale' => $user->bonus_per_extra_sale ?? 0,
                
                'status' => 'calculated',
                'calculated_at' => now(),
                'notes' => $salesData['notes'],
            ]
        );

        return $component;
    }

    /**
     * Get attendance data for salary calculation
     */
    private function getAttendanceData($userId, $month, $year)
    {
        $user = User::find($userId);
        $user->load('attendances');
        
        // Simulate attendance calculation (use existing AttendanceService logic)
        // For now, provide defaults
        $presentDays = 20;
        $leaveDays = 0;
        $halfDays = 0;
        $lateDays = 0;
        
        $dailySalary = ($user->basic_salary ?? 0) / self::WORKING_DAYS_PER_MONTH;
        
        // Calculate attendance deduction
        $attendanceDeduction = 0;
        if ($leaveDays > 0) {
            $attendanceDeduction += $leaveDays * $dailySalary;
        }
        if ($halfDays > 0) {
            $attendanceDeduction += ($halfDays * 0.5) * $dailySalary;
        }
        
        // Calculate fines
        $finePerAbsence = $user->fine_per_absence ?? 0;
        $finePerLate = $user->fine_per_late ?? 0;
        $attendanceDeduction += ($leaveDays * $finePerAbsence) + ($lateDays * $finePerLate);
        
        // Calculate punctuality bonus
        $punctualityBonus = 0;
        if ($this->qualifiesForPunctualityBonus($leaveDays, $halfDays, $lateDays)) {
            $punctualityBonus = $user->punctuality_bonus ?? 0;
        }
        
        $netAttendanceBonus = $punctualityBonus;

        return [
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
            'late_days' => $lateDays,
            'daily_salary' => $dailySalary,
            'attendance_bonus' => $netAttendanceBonus,
            'attendance_deduction' => -abs($attendanceDeduction),
            'notes' => "Attendance: {$presentDays} present, {$leaveDays} leaves, {$lateDays} lates",
        ];
    }

    /**
     * Get sales data for bonus calculation
     */
    private function getSalesData(User $user, $month, $year)
    {
        // Count total sales
        $actualSales = Lead::where(function($query) use ($user) {
                $query->where('managed_by', $user->id)
                      ->orWhere('closer_name', $user->name);
            })
            ->where('status', 'accepted')
            ->whereNotNull('sale_date')
            ->whereMonth('sale_date', $month)
            ->whereYear('sale_date', $year)
            ->count();
        
        // Count chargebacks
        $chargebackCount = Lead::where(function($query) use ($user) {
                $query->where('managed_by', $user->id)
                      ->orWhere('closer_name', $user->name);
            })
            ->where('status', 'chargeback')
            ->whereMonth('chargeback_marked_date', $month)
            ->whereYear('chargeback_marked_date', $year)
            ->count();
        
        $netApprovedSales = $actualSales - $chargebackCount;
        $targetSales = $user->target_sales ?? 20;
        
        // Calculate sales bonus
        $salesBonus = 0;
        $extraSales = 0;
        if ($netApprovedSales >= $targetSales) {
            $extraSales = $netApprovedSales - $targetSales;
            $bonusPerSale = $user->bonus_per_extra_sale ?? 0;
            $salesBonus = $extraSales * $bonusPerSale;
        }
        
        $notes = "Sales: {$actualSales} total, {$chargebackCount} chargebacks, {$netApprovedSales} net approved | Target: {$targetSales} | Bonus: Rs" . number_format($salesBonus, 2);

        return [
            'actual_sales' => $actualSales,
            'chargeback_count' => $chargebackCount,
            'net_approved_sales' => $netApprovedSales,
            'extra_sales' => max(0, $extraSales),
            'sales_bonus' => $salesBonus,
            'notes' => $notes,
        ];
    }

    /**
     * Check if employee qualifies for punctuality bonus
     */
    private function qualifiesForPunctualityBonus($leaveDays, $halfDays, $lateDays)
    {
        return $leaveDays < self::PUNCTUALITY_DISQUALIFY_OFFS
            && $halfDays < self::PUNCTUALITY_DISQUALIFY_HALF_DAYS
            && $lateDays < self::PUNCTUALITY_DISQUALIFY_LATE_DAYS;
    }

    /**
     * Get payment date based on day of month
     * Handles month-end edge cases
     */
    private function getPaymentDate($month, $year, $dayOfMonth)
    {
        try {
            return Carbon::create($year, $month, $dayOfMonth);
        } catch (\Exception $e) {
            // If day doesn't exist in month (e.g., Feb 30), use last day of month
            return Carbon::create($year, $month, 1)->endOfMonth();
        }
    }
}
