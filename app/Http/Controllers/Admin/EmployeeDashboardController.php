<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\CallLog;
use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Attendance summary using configured attendance period (default start day 25)
        $startDay = Setting::get('attendance_period_start_day', 25);
        $today = now();

        if ($today->day >= $startDay) {
            $periodStart = \Carbon\Carbon::create($today->year, $today->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            $periodStart = \Carbon\Carbon::create($today->copy()->subMonth()->year, $today->copy()->subMonth()->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->get()
            ->keyBy(function($a) { return $a->date->format('Y-m-d'); });

        // Calculate actual workdays (excluding weekends and holidays)
        $workdays = 0;
        $present = 0;
        $late = 0;
        $absent = 0;
        $totalHours = 0;
        
        $cursor = $periodStart->copy();
        $now = \Carbon\Carbon::now('Asia/Karachi');
        
        // For night shift: if before 5am, we're still in previous day's shift
        $effectiveToday = $now->copy();
        if ($now->hour < 5) {
            $effectiveToday->subDay();
        }
        
        while ($cursor->lte($periodEnd)) {
            // Skip if future date (only check if viewing current period)
            // If cursor is beyond today, skip it (don't count future days)
            if ($cursor->gt($now)) {
                $cursor->addDay();
                continue;
            }
            
            // Skip weekends
            if (in_array($cursor->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                $cursor->addDay();
                continue;
            }
            
            // Skip public holidays
            if (PublicHoliday::isHoliday($cursor)) {
                $cursor->addDay();
                continue;
            }
            
            $workdays++;
            $att = $attendanceRecords->get($cursor->format('Y-m-d'));
            
            if ($att) {
                if ($att->status === 'present') $present++;
                if ($att->status === 'late') $late++;
                // Ensure we get the working hours value correctly
                $workingHours = $att->working_hours;
                if ($workingHours === null || $workingHours === 0) {
                    // Fallback: calculate working hours if not stored
                    $workingHours = $att->getWorkingHoursAttribute();
                }
                $totalHours += $workingHours;
            } else {
                $absent++;
            }
            
            $cursor->addDay();
        }

        $attendanceSummary = [
            'total_days' => $workdays,
            'present_days' => $present,
            'late_days' => $late,
            'absent_days' => $absent,
            'total_working_hours' => round($totalHours, 1),
            'average_working_hours' => $workdays > 0 ? round($totalHours / $workdays, 1) : 0,
        ];

        // Get stats for the employee
        $stats = [
            'dialed_today' => $this->getDialedTodayCount($user->id),
            'calls_connected' => $this->getCallsConnectedCount($user->id),
            'sales_today' => $this->getSalesTodayCount($user->id),
            'mtd_sales' => $this->getMTDSalesCount($user->id),
            'attendance_summary' => $attendanceSummary,
            // Today's attendance status if any
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        return view('employee.dashboard', compact('stats'));
    }

    public function leads()
    {
        // Get leads for employees to call - paginated for performance
        $leads = Lead::orderBy('created_at', 'desc')->paginate(100);

        return view('employee.leads', compact('leads'));
    }

    /**
     * Get count of unique leads dialed today by this employee
     */
    private function getDialedTodayCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->distinct('lead_id')
            ->count('lead_id');
    }

    /**
     * Get count of sales made today by this employee
     */
    private function getSalesTodayCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereDate('sale_at', today())
            ->whereIn('status', ['accepted', 'underwritten'])
            ->count();
    }

    /**
     * Get count of calls connected today
     */
    private function getCallsConnectedCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->where('call_status', 'connected')
            ->count();
    }

    /**
     * Get MTD (Month-To-Date) sales count for this employee
     */
    private function getMTDSalesCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->whereIn('status', ['accepted', 'underwritten'])
            ->count();
    }
}
