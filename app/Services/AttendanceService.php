<?php

// app/Services/AttendanceService.php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\PublicHoliday;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class AttendanceService
{
    public function isInOfficeNetwork($ipAddress = null)
    {
        // Check if attendance is enabled
        if (! Setting::get('attendance_enabled', true)) {
            return false;
        }

        $allowedNetworks = Setting::get('office_networks', []);

        // If it's an array string from database, convert it
        if (is_string($allowedNetworks)) {
            $allowedNetworks = array_filter(array_map('trim', explode(',', $allowedNetworks)));
        }

        // No networks configured → no IP restriction; allow from any device
        if (empty($allowedNetworks)) {
            return true;
        }

        $ip = $ipAddress ?: Request::ip();

        foreach ($allowedNetworks as $network) {
            $network = trim($network);
            if ($this->ipInRange($ip, $network)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange($ip, $range)
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }

    public function markAttendance($userId, $forceOffice = false)
    {
        $currentTime = Carbon::now();

        // Get office start time and late time from settings
        $officeStartTimeRaw = Setting::get('office_start_time', '09:00');
        $lateTimeRaw = Setting::get('late_time', '09:15');

        // Accept both '09:00' and '09:00 AM' formats
        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw);
        }

        try {
            $lateTime = Carbon::createFromFormat('H:i', $lateTimeRaw);
        } catch (\Exception $e) {
            $lateTime = Carbon::createFromFormat('h:i A', $lateTimeRaw);
        }

        // Get configurable attendance window settings
        $bufferHours = (int) Setting::get('attendance_buffer_hours', '1');
        $shiftDurationHours = (int) Setting::get('shift_duration_hours', '8');

        // Calculate allowed attendance window based on settings
        // windowStart allows early check-in up to bufferHours before start time
        $windowStart = $startTime->copy()->subHours($bufferHours);
        $windowEnd = $startTime->copy()->addHours($shiftDurationHours + $bufferHours);

        // Check if current time is within the attendance window
        $isWithinOfficeHours = $currentTime->between($windowStart, $windowEnd, true);

        if (!$isWithinOfficeHours && !$forceOffice) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between ' . $windowStart->format('g:i A') . ' and ' . $windowEnd->format('g:i A') . '. Office hours are ' . $startTime->format('g:i A') . ' to ' . $startTime->copy()->addHours($shiftDurationHours)->format('g:i A') . ' with ' . $bufferHours . '-hour buffer.',
            ];
        }

        $shiftDate = Carbon::today();

        // Check if it's a public holiday
        if (PublicHoliday::isHoliday($shiftDate)) {
            return [
                'success' => false,
                'message' => 'Today is a public holiday. Attendance marking is not required.',
            ];
        }

        // Check if it's weekend and weekend attendance is not allowed
        if (! Setting::get('allow_weekend_attendance', false)) {
            if ($shiftDate->isWeekend()) {
                return [
                    'success' => false,
                    'message' => 'Attendance marking is not allowed on weekends.',
                ];
            }
        }

        // Check office network unless forced, or unless the office-only restriction
        // is disabled. Set the `attendance_restrict_to_office` setting to true to
        // re-enable office-network-only attendance marking.
        if (Setting::get('attendance_restrict_to_office', false) && ! $forceOffice && ! $this->isInOfficeNetwork()) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked from office network.',
                'debug_ip' => Request::ip(),
                'allowed_networks' => Setting::get('office_networks'),
            ];
        }

        // Check if attendance already exists for today
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->first();

        if ($attendance) {
            return [
                'success' => false,
                'message' => 'Attendance already marked for today.',
                'attendance' => $attendance,
            ];
        }

        // Day shift: present if on time, late if past late threshold
        $status = $currentTime->lessThanOrEqualTo($lateTime) ? Statuses::ATTENDANCE_PRESENT : Statuses::ATTENDANCE_LATE;

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $shiftDate,
            'login_time' => $currentTime,
            'ip_address' => Request::ip(),
            'device_fingerprint' => Request::cookie(\App\Http\Middleware\RestrictToAllowedDevice::COOKIE),
            'device_name' => null,
            'status' => $status,
        ]);

        return [
            'success' => true,
            'message' => 'Attendance marked successfully.',
            'attendance' => $attendance,
            'status' => $status,
        ];
    }

    public function markLogout($userId)
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', Carbon::today())
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->first();

        if ($attendance && ! $attendance->logout_time) {
            $attendance->update([
                'logout_time' => Carbon::now(),
            ]);

            return [
                'success' => true,
                'message' => 'Logout time recorded.',
                'attendance' => $attendance,
            ];
        }

        return [
            'success' => false,
            'message' => 'No active attendance found to check out.',
        ];
    }

    // New method to check and mark attendance on dashboard visits
    public function checkAndMarkDailyAttendance($userId)
    {
        $currentTime = Carbon::now();

        // Get office start time from settings
        $officeStartTimeRaw = Setting::get('office_start_time', '09:00');
        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw);
        }

        // Calculate allowed attendance window with 1-hour buffer
        $bufferHours = (int) Setting::get('attendance_buffer_hours', '1');
        $shiftDurationHours = (int) Setting::get('shift_duration_hours', '8');
        $windowStart = $startTime->copy()->subHours($bufferHours);
        $windowEnd = $startTime->copy()->addHours($shiftDurationHours + $bufferHours);

        // Check if within allowed office hours
        $isWithinOfficeHours = $currentTime->between($windowStart, $windowEnd, true);

        if (!$isWithinOfficeHours) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between ' . $windowStart->format('g:i A') . ' and ' . $windowEnd->format('g:i A') . '.',
            ];
        }

        $shiftDate = Carbon::today();

        // Check if attendance already exists for today
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->first();

        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Attendance already marked for today.',
                'attendance' => $existingAttendance,
            ];
        }

        // Auto-mark unless office-only restriction is enabled and the user is off-network.
        if (! Setting::get('attendance_restrict_to_office', false) || $this->isInOfficeNetwork()) {
            return $this->markAttendance($userId);
        }

        return [
            'success' => false,
            'message' => 'Not in office network, attendance not marked.',
            'should_show_manual' => true,
        ];
    }
    
    /**
     * Auto-checkout employees who haven't checked out by end of day.
     * Should be run via scheduled command.
     */
    public function autoCheckoutOverdueAttendances()
    {
        $currentTime = Carbon::now();

        // Only run shortly after the end of the shift (e.g., around 5:30 PM PT)
        if ($currentTime->hour < 17 || $currentTime->hour > 18) {
            return [
                'success' => false,
                'message' => 'Auto-checkout only runs between 5:00 PM and 6:59 PM PT.',
            ];
        }

        $shiftDate = Carbon::today();

        // Find all attendance records from today without logout
        $overdueAttendances = Attendance::where('date', $shiftDate)
            ->whereNull('logout_time')
            ->get();

        $checkedOutCount = 0;

        foreach ($overdueAttendances as $attendance) {
            // Set logout time to 5:30 PM PT
            $checkoutTime = Carbon::today()->setTime(17, 30, 0);

            $attendance->update([
                'logout_time' => $checkoutTime,
                'auto_checkout' => true,
            ]);
            
            $checkedOutCount++;
        }
        
        return [
            'success' => true,
            'message' => "Auto-checkout completed for {$checkedOutCount} employee(s).",
            'checked_out_count' => $checkedOutCount,
        ];
    }
}
