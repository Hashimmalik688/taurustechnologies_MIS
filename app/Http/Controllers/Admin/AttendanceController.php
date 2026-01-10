<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\Setting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    private $attendanceService;

    // Roles that should track attendance (all non-admin roles)
    private $trackableRoles = ['Employee', 'Paraguins Closer', 'Paraguins Validator', 'Verifier', 'Trainer', 'Ravens Closer', 'Manager', 'HR', 'QA', 'Retention Officer'];

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Return attendance record as JSON for AJAX edit modal
     */
    public function json($id)
    {
        $attendance = Attendance::with('user')->find($id);
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'attendance' => [
                'id' => $attendance->id,
                'user_name' => $attendance->user->name,
                'date' => $attendance->date ? $attendance->date->format('Y-m-d') : '',
                'login_time' => $attendance->login_time ? \Carbon\Carbon::parse($attendance->login_time)->format('H:i') : '',
                'logout_time' => $attendance->logout_time ? \Carbon\Carbon::parse($attendance->logout_time)->format('H:i') : '',
                'status' => $attendance->status,
            ]
        ]);
    }

    /**
     * Update attendance record via AJAX
     */
    public function updateAjax(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        try {
            // Validate required fields
            $request->validate([
                'date' => 'required|date',
                'login_time' => 'required',
                'status' => 'required|in:present,late,absent,half_day,paid_leave'
            ]);

            $date = $request->input('date');
            $loginTime = $request->input('login_time');
            $logoutTime = $request->input('logout_time');
            
            // Update attendance date
            $attendance->date = $date;
            
            // Combine date with time for login_time datetime field
            if ($loginTime) {
                $attendance->login_time = \Carbon\Carbon::parse("$date $loginTime");
            }
            
            // Combine date with time for logout_time datetime field
            // Handle night shift: if logout time is earlier than login time, add 1 day
            if ($logoutTime) {
                $logoutDateTime = \Carbon\Carbon::parse("$date $logoutTime");
                $loginDateTime = \Carbon\Carbon::parse("$date $loginTime");
                
                // If logout is before login (e.g., login 22:00, logout 05:00), it's next day
                if ($logoutDateTime->lt($loginDateTime)) {
                    $logoutDateTime->addDay();
                }
                
                $attendance->logout_time = $logoutDateTime;
            } else {
                $attendance->logout_time = null;
            }
            
            // Update status
            $attendance->status = $request->input('status', 'present');
            
            $attendance->save();
            
            return response()->json(['success' => true, 'message' => 'Updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
        try {
            // Enhanced filters
            $today = Carbon::today();
            $startDate = $request->get('start_date', $today->format('Y-m-d'));
            $endDate = $request->get('end_date', $today->format('Y-m-d'));
            $searchName = $request->get('search_name');
            $searchStatus = $request->get('status');

            // Validate dates
            if ($startDate && !strtotime($startDate)) {
                $startDate = $today->format('Y-m-d');
            }
            if ($endDate && !strtotime($endDate)) {
                $endDate = $today->format('Y-m-d');
            }

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

            // Use the selected date (startDate) for stats and absentees
            $selectedDate = Carbon::parse($startDate);
            $selectedAttendances = Attendance::with('user')
                ->whereDate('date', $selectedDate)
                ->get();

            $presentCount = $selectedAttendances->where('status', 'present')->count();
            $lateCount = $selectedAttendances->where('status', 'late')->count();
            
            // Only count absent if selected date is a workday (not weekend/holiday)
            $isWorkday = !in_array($selectedDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]) 
                         && !PublicHoliday::isHoliday($selectedDate);
            $absentCount = $isWorkday ? ($totalEmployees - $selectedAttendances->count()) : 0;

            // Get absent employees for the selected date
            $presentUserIds = $selectedAttendances->pluck('user_id')->toArray();
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
        } catch (\Exception $e) {
            \Log::error('Attendance Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading attendance data. Please try again.');
        }
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
        
        // Get all employees for the filter dropdown
        $employees = User::whereHas('roles', function ($query) {
            $query->whereIn('name', $this->trackableRoles);
        })->orderBy('name')->get();
        
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
            if ($attendance->login_time) {
                return Carbon::parse($attendance->login_time)->hour * 60 +
                       Carbon::parse($attendance->login_time)->minute;
            }
            return 0;
        });

        $avgLoginTime = $avgLoginTime ?
            sprintf('%02d:%02d', floor($avgLoginTime / 60), $avgLoginTime % 60) :
            'N/A';

        // Calculate total working hours
        $totalWorkingHours = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->login_time && $attendance->logout_time) {
                // Use the actual attendance date for proper night shift calculation
                $attendanceDate = $attendance->date ?? Carbon::today();
                
                // Parse login and logout times with the actual attendance date
                $loginTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->login_time->format('H:i:s'));
                $logoutTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->logout_time->format('H:i:s'));
                
                // Handle night shift - if logout is before login, add a day
                if ($logoutTime->lt($loginTime)) {
                    $logoutTime->addDay();
                }
                
                $totalWorkingHours += $loginTime->diffInHours($logoutTime, true);
            }
        }

        // Build statistics array
        $statistics = [
            'total_present' => $presentDays,
            'total_absent' => $absentDays,
            'total_late' => $lateDays,
            'total_working_days' => $totalWorkingDays,
            'avg_login_time' => $avgLoginTime,
            'total_working_hours' => round($totalWorkingHours, 2),
        ];

        return view('admin.attendance.employee-report', compact(
            'employee',
            'employees',
            'attendances',
            'month',
            'totalWorkingDays',
            'presentDays',
            'lateDays',
            'absentDays',
            'avgLoginTime',
            'statistics'
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
        
        // Get public holidays for this month (new system)
        $newHolidays = PublicHoliday::getMonthHolidays($date->year, $date->month);
        
        // Get legacy holidays for this month
        $legacyHolidays = \App\Models\Holiday::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->orderBy('date')
            ->get();
        
        // Merge both holiday sources
        $holidays = $newHolidays->merge($legacyHolidays)
            ->unique(function($h) { return $h->date->format('Y-m-d'); })
            ->keyBy(function ($h) { return $h->date->format('Y-m-d'); });
        
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
                'holiday' => $holidays->get($dateKey),
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
        // Calculate total days in the current month (excluding weekends)
        $crmStartDate = Carbon::create(2020, 1, 1); // Set to very old date to include all data
        $startOfMonth = Carbon::parse($currentMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($currentMonth)->endOfMonth();
        $totalDays = 0;
        $absent = 0;
        $present = 0;
        $late = 0;
        $half_day = 0;
        $paid_leave = 0;
        $totalHours = 0;
        $attendanceByDate = $monthAttendances->keyBy(function($a) { return Carbon::parse($a->date)->toDateString(); });
        $now = Carbon::now('Asia/Karachi');
        // Get office start time from settings (default 19:00)
        $officeStartTimeRaw = \App\Models\Setting::get('office_start_time', '19:00');
        try {
            $shiftStart = Carbon::createFromFormat('H:i', $officeStartTimeRaw, 'Asia/Karachi');
        } catch (\Exception $e) {
            $shiftStart = Carbon::createFromFormat('h:i A', $officeStartTimeRaw, 'Asia/Karachi');
        }
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            if ($date->lt($crmStartDate)) continue;
            if (in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) continue;
            
            // Skip public holidays - don't count as workdays
            if (PublicHoliday::isHoliday($date)) continue;
            
            // Only count as absent if day is in the past, or today and current time is after shift start
            $isPast = $date->lt($now->copy()->startOfDay());
            $isToday = $date->isSameDay($now);
            $canBeAbsent = $isPast || ($isToday && $now->gte($date->copy()->setTimeFrom($shiftStart)));
            if (!$canBeAbsent) continue;
            $totalDays++;
            $att = $attendanceByDate->get($date->toDateString());
            if ($att) {
                if ($att->status === 'present') $present++;
                if ($att->status === 'late') $late++;
                if ($att->status === 'half_day') $half_day++;
                if ($att->status === 'paid_leave') $paid_leave++;
                $totalHours += $att->working_hours ?? 0;
            } else {
                $absent++;
            }
        }
        $stats = [
            'total_days' => $totalDays,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'half_day' => $half_day,
            'paid_leave' => $paid_leave,
            'total_hours' => round($totalHours, 1),
            'avg_hours' => $totalDays > 0 ? round($totalHours / $totalDays, 1) : 0,
        ];
        
        // Today's attendance - for night shift, if before 5am, show yesterday's attendance
        $now = Carbon::now('Asia/Karachi');
        $attendanceDate = $now->copy();
        if ($now->hour < 5) {
            // We're still in previous day's shift
            $attendanceDate->subDay();
        }
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $attendanceDate)
            ->first();
        
        // Also check for any unchecked-out attendance from recent days (for checkout button)
        $pendingCheckout = null;
        if (!$todayAttendance || $todayAttendance->logout_time) {
            $pendingCheckout = Attendance::where('user_id', $user->id)
                ->whereNotNull('login_time')
                ->whereNull('logout_time')
                ->where('date', '>=', Carbon::now()->subDays(3)->format('Y-m-d'))
                ->orderBy('date', 'desc')
                ->first();
        }
        
        return view('attendance.dashboard', compact('calendar', 'stats', 'currentMonth', 'todayAttendance', 'pendingCheckout'));
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
     * Manual Attendance Entry - Handles Night Shifts Correctly
     * Automatically discovers and uses existing table schema
     */
    public function markManual(Request $request)
    {
        // Validate inputs
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'login_time' => 'required',
            'logout_time' => 'nullable',
            'status' => 'required|in:present,late,absent,half_day,paid_leave',
        ]);

        try {
            // Parse the shift date (date when shift STARTED)
            $shiftDate = Carbon::parse($request->date);
            $shiftDateString = $shiftDate->format('Y-m-d');
            
            // Parse login and logout times
            $loginTime = $request->login_time;
            $logoutTime = $request->logout_time;
            
            // Create full datetime objects for calculation
            $loginDateTime = Carbon::parse($shiftDateString . ' ' . $loginTime);
            
            // Handle night shift: if logout time is provided
            $logoutDateTime = null;
            if ($logoutTime) {
                // Parse logout as same day initially
                $logoutDateTime = Carbon::parse($shiftDateString . ' ' . $logoutTime);
                
                // Night shift logic: If logout is BEFORE login time, it's next day
                // Example: 22:00 start, 06:00 end means 06:00 is next morning
                if ($logoutDateTime->lt($loginDateTime)) {
                    $logoutDateTime->addDay();
                }
            }
            
            // Automatically discover existing field names from Attendance model
            $attendanceFields = (new Attendance())->getFillable();
            
            // Build data array using discovered field names
            $attendanceData = [];
            
            // Map user_id (handles both 'user_id' and 'employee_id' if it exists)
            if (in_array('user_id', $attendanceFields)) {
                $attendanceData['user_id'] = $request->user_id;
            } elseif (in_array('employee_id', $attendanceFields)) {
                $attendanceData['employee_id'] = $request->user_id;
            }
            
            // Add timestamps using discovered field names
            if (in_array('login_time', $attendanceFields)) {
                $attendanceData['login_time'] = $loginDateTime;
            }
            if (in_array('logout_time', $attendanceFields) && $logoutDateTime) {
                $attendanceData['logout_time'] = $logoutDateTime;
            }
            
            // Add status
            if (in_array('status', $attendanceFields)) {
                $attendanceData['status'] = $request->status;
            }
            
            // Add IP address marker
            if (in_array('ip_address', $attendanceFields)) {
                $adminName = auth()->check() ? auth()->user()->name : 'Admin';
                $attendanceData['ip_address'] = 'Manual Entry by ' . $adminName;
            }
            
            // Determine the unique key field name
            $userIdField = in_array('user_id', $attendanceFields) ? 'user_id' : 'employee_id';
            
            // Use updateOrCreate to overwrite existing records (e.g., replace 'Absent' with 'Present')
            // This ensures dashboard shows updated status immediately
            $attendance = Attendance::updateOrCreate(
                [
                    $userIdField => $request->user_id,
                    'date' => $shiftDateString, // Always use the shift START date
                ],
                $attendanceData
            );

            // Force model refresh to trigger boot() calculations (working_hours, etc.)
            $attendance->refresh();
            
            // Calculate overnight duration for logging
            $durationHours = 0;
            if ($logoutDateTime) {
                $durationMinutes = $loginDateTime->diffInMinutes($logoutDateTime);
                $durationHours = round($durationMinutes / 60, 2);
            }

            \Log::info('Manual Attendance Entry Created/Updated', [
                'admin_user' => auth()->check() ? auth()->user()->name : 'System',
                'employee_id' => $request->user_id,
                'shift_date' => $shiftDateString,
                'login' => $loginDateTime->format('Y-m-d H:i:s'),
                'logout' => $logoutDateTime ? $logoutDateTime->format('Y-m-d H:i:s') : 'Still working',
                'duration_hours' => $durationHours,
                'is_overnight_shift' => $logoutDateTime && $logoutDateTime->day !== $loginDateTime->day,
                'attendance_id' => $attendance->id,
                'action' => $attendance->wasRecentlyCreated ? 'created' : 'updated',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance ' . ($attendance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully',
                'attendance' => [
                    'id' => $attendance->id,
                    'date' => $shiftDateString,
                    'login_time' => $loginDateTime->format('H:i'),
                    'logout_time' => $logoutDateTime ? $logoutDateTime->format('H:i') : null,
                    'duration_hours' => $durationHours,
                    'is_overnight' => $logoutDateTime && $logoutDateTime->day !== $loginDateTime->day,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Manual Attendance Entry Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete attendance record
     */
    public function delete($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $userId = $attendance->user_id;
            $date = $attendance->date;
            
            $attendance->delete();
            
            \Log::info('Manual Attendance Entry Deleted', [
                'admin_user' => auth()->check() ? auth()->user()->name : 'System',
                'attendance_id' => $id,
                'employee_id' => $userId,
                'date' => $date,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete Attendance Error', [
                'error' => $e->getMessage(),
                'attendance_id' => $id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attendance: ' . $e->getMessage(),
            ], 500);
        }
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
            
            // Only count absent if it's a workday
            $isWorkday = !in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]) 
                         && !PublicHoliday::isHoliday($date);
            $absentCount = $isWorkday ? ($totalEmployees - $dayAttendances->count()) : 0;

            return [
                'date' => $date->format('M d'),
                'present' => $dayAttendances->where('status', 'present')->count(),
                'late' => $dayAttendances->where('status', 'late')->count(),
                'absent' => $absentCount,
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
            if (in_array($current->dayOfWeek, [0, 6])) {
                $current->addDay();
                continue;
            }
            
            // Skip holidays
            if (PublicHoliday::isHoliday($current)) {
                $current->addDay();
                continue;
            }
            
            $workingDays++;
            $current->addDay();
        }

        return $workingDays;
    }
}
