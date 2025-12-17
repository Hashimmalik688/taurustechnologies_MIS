<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalaryTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // / Get employees
        $employees = User::role('Employee')->take(3)->get();

        if ($employees->count() < 3) {
            $this->command->info('Not enough employees to create test data. Please create some users with Employee role first.');

            return;
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        foreach ($employees as $index => $employee) {
            // Create different attendance scenarios
            $this->createAttendanceData($employee, $currentMonth, $currentYear, $index);

            // Create sales data
            $this->createSalesData($employee, $currentMonth, $currentYear, $index);
        }
    }

    private function createAttendanceData($employee, $month, $year, $scenario)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $currentDate = $startDate->copy();
        $leaveCount = 0;

        while ($currentDate <= $endDate && $currentDate <= Carbon::now()) {
            // Only create for working days (Mon-Fri)
            if ($currentDate->dayOfWeek >= 1 && $currentDate->dayOfWeek <= 5) {
                $status = 'present';
                $loginTime = $currentDate->copy()->setTime(8, 45 + rand(0, 30), 0); // Random login between 8:45-9:15
                $logoutTime = $currentDate->copy()->setTime(17, 30 + rand(0, 30), 0);

                // Create different scenarios
                switch ($scenario) {
                    case 0: // Perfect employee
                        // Always on time, no leaves
                        break;

                    case 1: // Employee with 1 leave
                        if ($currentDate->day == 15 && $leaveCount == 0) {
                            $status = 'leave';
                            $loginTime = null;
                            $logoutTime = null;
                            $leaveCount++;
                        }
                        break;

                    case 2: // Employee with late arrivals
                        if (rand(1, 5) == 1) { // 20% chance of being late
                            $status = 'late';
                            $loginTime = $currentDate->copy()->setTime(9, 20 + rand(0, 30), 0); // Late arrival
                        }
                        break;
                }

                Attendance::create([
                    'user_id' => $employee->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'login_time' => $loginTime,
                    'logout_time' => $logoutTime,
                    'ip_address' => '192.168.1.'.rand(10, 100),
                    'status' => $status,
                ]);
            }

            $currentDate->addDay();
        }
    }

    private function createSalesData($employee, $month, $year, $scenario)
    {
        $salesCount = [22, 19, 16][$scenario]; // Different sales performance

        for ($i = 0; $i < $salesCount; $i++) {
            Lead::create([
                'cn_name' => 'Test Client '.($i + 1),
                'phone_number' => '555-'.rand(1000, 9999),
                'forwarded_by' => $employee->id,
                'sale_at' => Carbon::create($year, $month, rand(1, 28)),
                'status' => 'forwarded',
            ]);
        }
    }
}
