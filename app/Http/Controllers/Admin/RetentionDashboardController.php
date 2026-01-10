<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetentionDashboardController extends Controller
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
                $totalHours += $att->working_hours ?? 0;
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

        // Get retention stats
        $stats = [
            'total_chargebacks' => Lead::where('status', 'chargeback')->count(),
            'yet_to_retain' => Lead::where('status', 'chargeback')
                ->where(function($q) {
                    $q->whereNull('retention_status')
                      ->orWhere('retention_status', 'pending');
                })->count(),
            'retained_today' => Lead::where('retention_status', 'retained')
                ->whereDate('retained_at', today())->count(),
            'retained_mtd' => Lead::where('retention_status', 'retained')
                ->whereMonth('retained_at', now()->month)
                ->whereYear('retained_at', now()->year)->count(),
            'rewrite_count' => Lead::where('status', 'chargeback')
                ->where('is_rewrite', true)->count(),
            'attendance_summary' => $attendanceSummary,
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        return view('retention.dashboard', compact('stats'));
    }
}
