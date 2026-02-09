<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Payroll Month Calculation Trait
 * 
 * Handles the company's payroll cycle:
 * - Payroll month runs from 26th of previous month to 25th of current month
 * - Example: Jan 26 to Feb 25 = February payroll
 * - February payroll is paid on March 10
 */
trait PayrollMonthCalculation
{
    /**
     * Get the payroll month and year for a given date
     * 
     * Rules:
     * - All dates (1st-31st) belong to the CURRENT month's payroll
     * 
     * Examples:
     * - Jan 1 → January payroll
     * - Jan 26 → January payroll
     * - Jan 31 → January payroll
     * - Feb 1 → February payroll
     * 
     * @param Carbon $date The date to calculate payroll month for
     * @return array ['month' => int, 'year' => int]
     */
    public function getPayrollMonthYear(Carbon $date): array
    {
        // All dates belong to the current month
        return [
            'month' => $date->month,
            'year' => $date->year,
        ];
    }

    /**
     * Get the start and end dates for a payroll period
     * 
     * @param int $month Payroll month (1-12)
     * @param int $year Payroll year
     * @return array ['start' => Carbon, 'end' => Carbon]
     */
    public function getPayrollPeriod(int $month, int $year): array
    {
        // Start: 26th of previous month
        $start = Carbon::create($year, $month, 1)->subMonth()->day(26)->startOfDay();
        
        // End: 25th of current month
        $end = Carbon::create($year, $month, 25)->endOfDay();
        
        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get the payment date for a payroll month
     * Default is 10th of the following month
     * 
     * @param int $month Payroll month
     * @param int $year Payroll year
     * @param int $dayOfMonth Day of month to pay (default 10)
     * @return Carbon
     */
    public function getPayrollPaymentDate(int $month, int $year, int $dayOfMonth = 10): Carbon
    {
        // Payment is made in the NEXT month after payroll month
        return Carbon::create($year, $month, 1)
            ->addMonth()
            ->day($dayOfMonth)
            ->startOfDay();
    }

    /**
     * Get current payroll month based on today's date
     * 
     * @return array ['month' => int, 'year' => int]
     */
    public function getCurrentPayrollMonthYear(): array
    {
        return $this->getPayrollMonthYear(Carbon::now());
    }

    /**
     * Format payroll period as string
     * 
     * @param int $month
     * @param int $year
     * @return string Example: "Jan 26, 2024 - Feb 25, 2024"
     */
    public function formatPayrollPeriod(int $month, int $year): string
    {
        $period = $this->getPayrollPeriod($month, $year);
        return $period['start']->format('M d, Y') . ' - ' . $period['end']->format('M d, Y');
    }

    /**
     * Count working days (excluding Sundays) in a date range
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int Number of working days
     */
    public function countWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            // Skip Saturdays (6) and Sundays (0)
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }
}
