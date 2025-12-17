<?php

namespace Database\Seeders;

use App\Models\SalaryRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummySalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy seeder disabled per cleanup request.
        $this->command->info('DummySalarySeeder disabled.');
    }

    private function createSalaryRecord($employee, $month, $year, $status, $scenario)
    {
        // Different performance scenarios
        $scenarios = [
            // Scenario 0: High performer
            [
                'actual_sales' => 28,
                'working_days' => 22,
                'present_days' => 22,
                'leave_days' => 0,
                'late_days' => 0,
            ],
            // Scenario 1: Average performer
            [
                'actual_sales' => 22,
                'working_days' => 22,
                'present_days' => 21,
                'leave_days' => 1,
                'late_days' => 2,
            ],
            // Scenario 2: Below target
            [
                'actual_sales' => 18,
                'working_days' => 22,
                'present_days' => 20,
                'leave_days' => 2,
                'late_days' => 3,
            ],
            // Scenario 3: Good performer
            [
                'actual_sales' => 26,
                'working_days' => 22,
                'present_days' => 21,
                'leave_days' => 1,
                'late_days' => 1,
            ],
            // Scenario 4: Average performer
            [
                'actual_sales' => 20,
                'working_days' => 22,
                'present_days' => 22,
                'leave_days' => 0,
                'late_days' => 4,
            ],
            // Scenario 5: High performer
            [
                'actual_sales' => 30,
                'working_days' => 22,
                'present_days' => 22,
                'leave_days' => 0,
                'late_days' => 0,
            ],
        ];

        $scenarioData = $scenarios[$scenario % count($scenarios)];

        $basicSalary = $employee->basic_salary ?? 3500;
        $targetSales = $employee->target_sales ?? 25;
        $bonusPerExtraSale = $employee->bonus_per_extra_sale ?? 150;

        $actualSales = $scenarioData['actual_sales'];
        $extraSales = max(0, $actualSales - $targetSales);
        $totalBonus = $extraSales * $bonusPerExtraSale;

        // Calculate attendance metrics
        $workingDays = $scenarioData['working_days'];
        $presentDays = $scenarioData['present_days'];
        $leaveDays = $scenarioData['leave_days'];
        $lateDays = $scenarioData['late_days'];

        $dailySalary = $basicSalary / $workingDays;

        // Attendance bonus/deduction
        $attendanceBonus = 0;
        $attendanceDeduction = 0;

        // Perfect attendance bonus
        if ($presentDays == $workingDays && $lateDays == 0) {
            $attendanceBonus = 500; // Perfect attendance bonus
        }

        // Deduction for leaves
        if ($leaveDays > 0) {
            $attendanceDeduction = -($leaveDays * $dailySalary);
        }

        // Deduction for late arrivals
        if ($lateDays > 0) {
            $attendanceDeduction -= ($lateDays * 50); // $50 per late day
        }

        $totalDeductions = abs($attendanceDeduction);
        $grossSalary = $basicSalary + $totalBonus + $attendanceBonus;
        $netSalary = $grossSalary - $totalDeductions;

        $calculatedAt = Carbon::create($year, $month, 1)->endOfMonth();
        $approvedAt = $status === 'paid' ? $calculatedAt->copy()->addDays(2) : null;
        $paidAt = $status === 'paid' ? $calculatedAt->copy()->addDays(5) : null;

        SalaryRecord::create([
            'user_id' => $employee->id,
            'salary_year' => $year,
            'salary_month' => $month,
            'basic_salary' => $basicSalary,
            'target_sales' => $targetSales,
            'actual_sales' => $actualSales,
            'extra_sales' => $extraSales,
            'bonus_per_extra_sale' => $bonusPerExtraSale,
            'total_bonus' => $totalBonus,
            'total_deductions' => $totalDeductions,
            'gross_salary' => $grossSalary,
            'net_salary' => $netSalary,
            'status' => $status,
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
            'late_days' => $lateDays,
            'daily_salary' => $dailySalary,
            'attendance_bonus' => $attendanceBonus,
            'attendance_deduction' => $attendanceDeduction,
            'calculated_at' => $calculatedAt,
            'approved_at' => $approvedAt,
            'paid_at' => $paidAt,
            'notes' => $status === 'paid' ? 'Salary paid successfully' : 'Pending calculation',
        ]);
    }
}