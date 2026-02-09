<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Lead;
use App\Models\SalaryDeduction;
use App\Models\SalaryRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryCalculationService
{
    /**
     * Calculate salary for a specific user and month
     */
    public function calculateSalary(User $user, int $year, int $month): SalaryRecord
    {
        // Check if user has salary settings
        if (!$user->basic_salary || $user->basic_salary <= 0) {
            throw new \Exception("User {$user->name} does not have a basic salary configured.");
        }

        // Start transaction
        return DB::transaction(function () use ($user, $year, $month) {
            // Delete existing draft record if exists
            SalaryRecord::where('user_id', $user->id)
                ->where('salary_year', $year)
                ->where('salary_month', $month)
                ->where('status', 'draft')
                ->delete();

            // Get attendance data
            $attendanceData = $this->getAttendanceData($user, $year, $month);
            
            // Get sales data
            $salesData = $this->getSalesData($user, $year, $month);
            
            // Calculate working days (default 22)
            $workingDays = 22;
            $dailySalary = round($user->basic_salary / $workingDays, 2);
            
            // Calculate salary components
            $basicSalary = $user->basic_salary;
            $targetSales = $user->target_sales ?? 20;
            $bonusPerSale = $user->bonus_per_extra_sale ?? 0;
            
            // Calculate sales bonus
            $actualSales = $salesData['total_sales'];
            $extraSales = max(0, $actualSales - $targetSales);
            $totalBonus = $extraSales * $bonusPerSale;
            
            // Calculate punctuality bonus
            $punctualityBonus = $this->calculatePunctualityBonus($user, $attendanceData);
            $totalBonus += $punctualityBonus;
            
            // Calculate attendance deductions
            $attendanceDeduction = $this->calculateAttendanceDeductions($attendanceData, $dailySalary);
            
            // Calculate gross and net salary
            $grossSalary = $basicSalary + $totalBonus;
            $totalDeductions = abs($attendanceDeduction); // Make positive for display
            $netSalary = $grossSalary - $totalDeductions;
            
            // Create salary record
            $salaryRecord = SalaryRecord::create([
                'user_id' => $user->id,
                'salary_year' => $year,
                'salary_month' => $month,
                'basic_salary' => $basicSalary,
                'target_sales' => $targetSales,
                'actual_sales' => $actualSales,
                'extra_sales' => $extraSales,
                'bonus_per_extra_sale' => $bonusPerSale,
                'total_bonus' => $totalBonus,
                'total_deductions' => $totalDeductions,
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'working_days' => $workingDays,
                'present_days' => $attendanceData['present_days'],
                'leave_days' => $attendanceData['leave_days'],
                'late_days' => $attendanceData['late_days'],
                'half_days' => $attendanceData['half_days'],
                'daily_salary' => $dailySalary,
                'attendance_bonus' => $punctualityBonus,
                'attendance_deduction' => -$attendanceDeduction, // Store as negative
                'status' => 'calculated',
                'calculated_at' => now(),
            ]);
            
            // Create detailed deduction records
            $this->createDeductionRecords($salaryRecord, $attendanceData, $dailySalary);
            
            return $salaryRecord;
        });
    }

    /**
     * Calculate salary for all active employees for a specific month
     */
    public function calculateSalaryForAll(int $year, int $month): array
    {
        $users = User::where('employment_status', 'active')
            ->whereNotNull('basic_salary')
            ->where('basic_salary', '>', 0)
            ->get();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($users as $user) {
            try {
                $salaryRecord = $this->calculateSalary($user, $year, $month);
                $results['success'][] = [
                    'user' => $user->name,
                    'net_salary' => $salaryRecord->net_salary,
                ];
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'user' => $user->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get attendance data for the month
     */
    protected function getAttendanceData(User $user, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Get all attendances for the month
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        $presentDays = 0;
        $leaveDays = 0;
        $halfDays = 0;
        $lateDays = 0;
        $lateThreshold = Carbon::parse('07:15:00');
        
        foreach ($attendances as $attendance) {
            if ($attendance->status === 'present') {
                $presentDays++;
            } elseif ($attendance->status === 'late') {
                $lateDays++;
                $presentDays++; // Late still counts as present
            } elseif ($attendance->status === 'half_day') {
                $halfDays++;
                $presentDays += 0.5;
            } elseif (in_array($attendance->status, ['leave', 'absent'])) {
                $leaveDays++;
            }
        }
        
        return [
            'total_attendances' => $attendances->count(),
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
            'half_days' => $halfDays,
            'late_days' => $lateDays,
            'attendances' => $attendances,
        ];
    }

    /**
     * Get sales data for the month
     */
    protected function getSalesData(User $user, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Count leads where this user is the closer (made the sale)
        // Status should be 'accepted' or similar to indicate a successful sale
        $totalSales = Lead::where('closer_name', $user->name)
            ->where('status', 'accepted')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();
        
        // Alternative: if closer_name might be stored differently
        // You can also check by managed_by or another field
        if ($totalSales === 0) {
            // Fallback to managed_by if closer_name doesn't match
            $totalSales = Lead::where('managed_by', $user->id)
                ->where('status', 'accepted')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->count();
        }
        
        return [
            'total_sales' => $totalSales,
        ];
    }

    /**
     * Calculate punctuality bonus
     * Rules: No bonus if 1 off, 2 half days, or 4+ late arrivals
     */
    protected function calculatePunctualityBonus(User $user, array $attendanceData): float
    {
        $punctualityBonus = $user->punctuality_bonus ?? 0;
        
        if ($punctualityBonus <= 0) {
            return 0;
        }
        
        // Check disqualification criteria
        $leaveDays = $attendanceData['leave_days'];
        $halfDays = $attendanceData['half_days'];
        $lateDays = $attendanceData['late_days'];
        
        // Disqualify if: 1 or more offs, OR 2 or more half days, OR 4 or more late arrivals
        if ($leaveDays >= 1 || $halfDays >= 2 || $lateDays >= 4) {
            return 0;
        }
        
        return $punctualityBonus;
    }

    /**
     * Calculate attendance deductions
     * Formula: salary / 22 working days = per day salary
     * - Full leave = full day deduction
     * - Half day = half day deduction
     */
    protected function calculateAttendanceDeductions(array $attendanceData, float $dailySalary): float
    {
        $totalDeduction = 0;
        
        // Full leave days deduction
        $totalDeduction += $attendanceData['leave_days'] * $dailySalary;
        
        // Half days deduction (half of daily salary)
        $totalDeduction += $attendanceData['half_days'] * ($dailySalary / 2);
        
        return $totalDeduction;
    }

    /**
     * Create detailed deduction records
     */
    protected function createDeductionRecords(SalaryRecord $salaryRecord, array $attendanceData, float $dailySalary): void
    {
        // Leave deductions
        if ($attendanceData['leave_days'] > 0) {
            SalaryDeduction::create([
                'salary_record_id' => $salaryRecord->id,
                'type' => 'leave',
                'description' => "{$attendanceData['leave_days']} leave day(s)",
                'amount' => $attendanceData['leave_days'] * $dailySalary,
                'is_percentage' => false,
            ]);
        }
        
        // Half day deductions
        if ($attendanceData['half_days'] > 0) {
            SalaryDeduction::create([
                'salary_record_id' => $salaryRecord->id,
                'type' => 'half_day',
                'description' => "{$attendanceData['half_days']} half day(s)",
                'amount' => $attendanceData['half_days'] * ($dailySalary / 2),
                'is_percentage' => false,
            ]);
        }
    }

    /**
     * Approve a salary record
     */
    public function approveSalary(SalaryRecord $salaryRecord): SalaryRecord
    {
        if ($salaryRecord->status === 'paid') {
            throw new \Exception('Cannot approve a salary that has already been paid.');
        }
        
        $salaryRecord->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        return $salaryRecord;
    }

    /**
     * Mark salary as paid
     */
    public function markAsPaid(SalaryRecord $salaryRecord): SalaryRecord
    {
        if ($salaryRecord->status !== 'approved') {
            throw new \Exception('Salary must be approved before marking as paid.');
        }
        
        $salaryRecord->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        
        return $salaryRecord;
    }

    /**
     * Recalculate an existing salary record
     */
    public function recalculateSalary(SalaryRecord $salaryRecord): SalaryRecord
    {
        if ($salaryRecord->status === 'paid') {
            throw new \Exception('Cannot recalculate a salary that has already been paid.');
        }
        
        return $this->calculateSalary(
            $salaryRecord->user,
            $salaryRecord->salary_year,
            $salaryRecord->salary_month
        );
    }

    /**
     * Get salary summary for dashboard
     */
    public function getSalarySummary(int $year, int $month): array
    {
        $salaries = SalaryRecord::with('user')
            ->where('salary_year', $year)
            ->where('salary_month', $month)
            ->get();
        
        return [
            'total_employees' => $salaries->count(),
            'total_gross_salary' => $salaries->sum('gross_salary'),
            'total_deductions' => $salaries->sum('total_deductions'),
            'total_net_salary' => $salaries->sum('net_salary'),
            'total_bonus' => $salaries->sum('total_bonus'),
            'approved_count' => $salaries->where('status', 'approved')->count(),
            'paid_count' => $salaries->where('status', 'paid')->count(),
            'pending_count' => $salaries->whereIn('status', ['draft', 'calculated'])->count(),
        ];
    }
}
