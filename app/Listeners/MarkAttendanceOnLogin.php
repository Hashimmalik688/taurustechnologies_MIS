<?php

namespace App\Listeners;

use App\Services\AttendanceService;
use Illuminate\Support\Facades\Log;

class MarkAttendanceOnLogin
{
    private $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function handle(object $event): void
    {
        $user = $event->user;

        // Only mark attendance for User model (not Partners)
        if (!$user instanceof \App\Models\User) {
            return;
        }

        // Only mark attendance for employees (you might have role-based logic here)
        if ($this->shouldMarkAttendance($user)) {
            $result = $this->attendanceService->markAttendance($user->id);

            if ($result['success']) {
                session()->flash('attendance_success', $result['message']);
            } else {
                session()->flash('attendance_info', $result['message']);
            }

            Log::info('Attendance marking attempt', [
                'user_id' => $user->id,
                'success' => $result['success'],
                'message' => $result['message'],
            ]);
        }
    }

    private function shouldMarkAttendance($user)
    {
        // Don't mark attendance for CEO role (owner/executive level)
        if ($user->hasRole('CEO')) {
            return false;
        }

        // Auto-mark attendance for all other authenticated users on login
        return true;
    }
}
