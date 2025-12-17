<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    private $attendanceService;

    // Roles that should track attendance
    private $trackableRoles = ['Employee', 'Paraguins Closer', 'Paraguins Validator', 'Verifier', 'Trainer', 'Ravens Closer'];

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get all users with trackable roles
     */
    private function getTrackableUsers()
    {
        return User::whereHas('roles', function($q) {
            $q->whereIn('name', $this->trackableRoles);
        });
    }

    /**
     * Manager Dashboard - Today's Overview with enhanced filters
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Enhanced filters
        $startDate = $request->get('start_date', $today->format('Y-m-d'));
        $endDate = $request->get('end_date', $today->format('Y-m-d'));
        $searchName = $request->get('search_name');
        $searchStatus = $request->get('status');

        // Get all employees for dropdown
        $allEmployees = $this->getTrackableUsers()->orderBy('name')->get();
        $totalEmployees = $allEmployees->count();

        // Build query with filters
        $query = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by employee name
        if ($searchName) {
            $query->whereHas('user', function($q) use ($searchName) {
                $q->where('name', 'LIKE', '%' . $searchName . '%');
            });
        }

        // Filter by status
        if ($searchStatus) {
            $query->where('status', $searchStatus);
        }

        $attendanceDetails = $query->orderBy('date', 'desc')
            ->orderBy('login_time')
            ->get();

        // Statistics for the selected date range
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->get();

        $presentCount = $todayAttendances->where('status', 'present')->count();
        $lateCount = $todayAttendances->where('status', 'late')->count();
        $absentCount = $totalEmployees - $todayAttendances->count();

        // Get absent employees for today
        $presentUserIds = $todayAttendances->pluck('user_id')->toArray();
        $absentEmployees = $this->getTrackableUsers()
            ->whereNotIn('id', $presentUserIds)
            ->get();

        // Recent attendance trends (last 7 days)
        $weeklyStats = $this->getWeeklyStats();

        return view('admin.attendance.index', compact(
            'totalEmployees',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendanceDetails',
            'absentEmployees',
            'allEmployees',
            'startDate',
            'endDate',
            'searchName',
            'searchStatus',
            'weeklyStats'
        ));
    }

    /**
     * Attendance History
     */
    public function history(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $userId = $request->get('user_id');
        $status = $request->get('status');

        $query = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('login_time', 'desc')
            ->paginate(50);

        // Get users for filter dropdown
        $users = $this->getTrackableUsers()
            ->orderBy('name')
            ->get();

        // Summary statistics for the filtered period
        $summaryStats = $this->getSummaryStats($startDate, $endDate, $userId);

        return view('admin.attendance.history', compact(
            'attendances',
            'users',
            'startDate',
            'endDate',
            'userId',
            'status',
            'summaryStats'
        ));
    }

    /**
     * Individual Employee Report
     */
    public function employeeReport($userId, Request $request)
    {
        $employee = User::findOrFail($userId);
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $monthStart = Carbon::parse($month.'-01');
        $monthEnd = $monthStart->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->orderBy('date')
            ->get();

        // Calculate monthly statistics
        $totalWorkingDays = $this->getWorkingDaysInMonth($monthStart, $monthEnd);
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $totalWorkingDays - $attendances->count();

        // Calculate average login time
        $avgLoginTime = $attendances->avg(function ($attendance) {
            return Carbon::parse($attendance->login_time)->hour * 60 +
                   Carbon::parse($attendance->login_time)->minute;
        });

        $avgLoginTime = $avgLoginTime ?
            sprintf('%02d:%02d', floor($avgLoginTime / 60), $avgLoginTime % 60) :
            'N/A';

        return view('attendance.employee-report', compact(
            'employee',
            'attendances',
            'month',
            'totalWorkingDays',
            'presentDays',
            'lateDays',
            'absentDays',
            'avgLoginTime'
        ));
    }

    /**
     * AJAX: Check-in (mark attendance)
     */
    public function checkIn(Request $request)
    {
        $force = $request->input('force_office', false) ? true : false;
        $result = $this->attendanceService->markAttendance(auth()->id(), $force);

        if (!isset($result['success'])) {
            $result['success'] = false;
        }

        return response()->json($result);
    }

    /**
     * AJAX: Check-out (mark logout)
     */
    public function checkOut(Request $request)
    {
        $result = $this->attendanceService->markLogout(auth()->id());

        return response()->json($result);
    }

    /**
     * Personal attendance dashboard with calendar view
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $currentMonth = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($currentMonth . '-01');
        
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Get all attendance records for this month
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            ->keyBy(function ($a) { return $a->date->format('Y-m-d'); });
        
        // Build calendar grid
        $calendar = [];
        $firstDayOfWeek = $startOfMonth->copy()->startOfWeek(); // Monday
        $lastDayOfWeek = $endOfMonth->copy()->endOfWeek(); // Sunday
        
        $cursor = $firstDayOfWeek->copy();
        $week = [];
        
        while ($cursor <= $lastDayOfWeek) {
            $dateKey = $cursor->format('Y-m-d');
            $isCurrentMonth = $cursor->month === $date->month;
            
            $week[] = [
                'date' => $cursor->copy(),
                'dateKey' => $dateKey,
                'isCurrentMonth' => $isCurrentMonth,
                'attendance' => $attendances->get($dateKey),
                'isToday' => $cursor->isToday(),
            ];
            
            if ($cursor->dayOfWeek === 0) { // Sunday
                $calendar[] = $week;
                $week = [];
            }
            
            $cursor->addDay();
        }
        
        if (count($week) > 0) {
            $calendar[] = $week;
        }
        
        // Calculate statistics for current month
        $monthAttendances = $attendances->values();
        $stats = [
            'total_days' => $monthAttendances->count(),
            'present' => $monthAttendances->where('status', 'present')->count(),
            'absent' => $monthAttendances->where('status', 'absent')->count(),
            'late' => $monthAttendances->filter(function($a) { return $a->isLate(); })->count(),
            'total_hours' => round($monthAttendances->sum('working_hours'), 1),
            'avg_hours' => $monthAttendances->count() > 0 ? round($monthAttendances->sum('working_hours') / $monthAttendances->count(), 1) : 0,
        ];
        
        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();
        
        return view('attendance.dashboard', compact('calendar', 'stats', 'currentMonth', 'todayAttendance'));
    }

    /**
     * Export Attendance Data
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $attendances = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // You can implement CSV/Excel export here
        // For now, returning JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $attendances,
                'message' => 'Data exported successfully',
            ]);
        }

        return redirect()->back()->with('info', 'Export functionality coming soon!');
    }

    /**
     * Manual Attendance Marking (for corrections)
     */
    public function markManual(Request $request)
    {
        $forceOffice = $request->get('force_office', false);

        if ($forceOffice) {
            // Manual override - mark attendance even if not in office network
            $result = $this->attendanceService->markAttendance(auth()->id(), true);
        } else {
            // Regular validation
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'login_time' => 'required',
                'logout_time' => 'nullable',
                'status' => 'required|in:present,late,absent',
            ]);

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'date' => $request->date,
                ],
                [
                    'login_time' => $request->date.' '.$request->login_time,
                    'logout_time' => $request->logout_time ?
                        $request->date.' '.$request->logout_time : null,
                    'status' => $request->status,
                    'ip_address' => 'Manual Entry by Admin',
                ]
            );

            $result = [
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance,
            ];
        }

        return response()->json($result);
    }

    /**
     * Helper Methods
     */
    private function getWeeklyStats()
    {
        $weekDates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $weekDates->push(Carbon::now()->subDays($i));
        }

        return $weekDates->map(function ($date) {
            $dayAttendances = Attendance::whereDate('date', $date)->get();
            $totalEmployees = $this->getTrackableUsers()->count();

            return [
                'date' => $date->format('M d'),
                'present' => $dayAttendances->where('status', 'present')->count(),
                'late' => $dayAttendances->where('status', 'late')->count(),
                'absent' => $totalEmployees - $dayAttendances->count(),
                'percentage' => $totalEmployees > 0 ?
                    round(($dayAttendances->count() / $totalEmployees) * 100, 1) : 0,
            ];
        });
    }

    private function getSummaryStats($startDate, $endDate, $userId = null)
    {
        $query = Attendance::whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();

        return [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'avg_login_time' => $this->calculateAverageLoginTime($attendances),
        ];
    }

    private function calculateAverageLoginTime($attendances)
    {
        if ($attendances->isEmpty()) {
            return 'N/A';
        }

        $totalMinutes = $attendances->sum(function ($attendance) {
            $time = Carbon::parse($attendance->login_time);

            return $time->hour * 60 + $time->minute;
        });

        $avgMinutes = $totalMinutes / $attendances->count();

        return sprintf('%02d:%02d', floor($avgMinutes / 60), $avgMinutes % 60);
    }

    private function getWorkingDaysInMonth($start, $end)
    {
        $workingDays = 0;
        $current = $start->copy();

        while ($current <= $end) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (! in_array($current->dayOfWeek, [0, 6])) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }
}
