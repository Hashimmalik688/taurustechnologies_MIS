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

        // Get retention stats — single query for chargeback/retention counts
        $retAgg = Lead::selectRaw("
            SUM(CASE WHEN status = 'chargeback' THEN 1 ELSE 0 END) as total_chargebacks,
            SUM(CASE WHEN status = 'chargeback' AND (retention_status IS NULL OR retention_status = 'pending') THEN 1 ELSE 0 END) as yet_to_retain,
            SUM(CASE WHEN retention_status = 'retained' AND DATE(retained_at) = ? THEN 1 ELSE 0 END) as retained_today,
            SUM(CASE WHEN retention_status = 'retained' AND MONTH(retained_at) = ? AND YEAR(retained_at) = ? THEN 1 ELSE 0 END) as retained_mtd,
            SUM(CASE WHEN status = 'chargeback' AND is_rewrite = 1 THEN 1 ELSE 0 END) as rewrite_count
        ", [today()->toDateString(), now()->month, now()->year])->first();

        $stats = [
            'total_chargebacks' => (int) ($retAgg->total_chargebacks ?? 0),
            'yet_to_retain' => (int) ($retAgg->yet_to_retain ?? 0),
            'retained_today' => (int) ($retAgg->retained_today ?? 0),
            'retained_mtd' => (int) ($retAgg->retained_mtd ?? 0),
            'rewrite_count' => (int) ($retAgg->rewrite_count ?? 0),
            'attendance_summary' => $attendanceSummary,
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        return view('retention.dashboard', compact('stats'));
    }
}
