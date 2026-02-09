<?php

namespace App\Http\Middleware;

use App\Services\AttendanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckDailyAttendance
{
    private $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            // Only check for employees (not admins)
            if ($this->shouldCheckAttendance($user)) {
                // Check if we've already tried today (to avoid multiple attempts)
                $sessionKey = 'attendance_checked_'.date('Y-m-d').'_'.$user->id;

                if (! Session::has($sessionKey)) {
                    $result = $this->attendanceService->checkAndMarkDailyAttendance($user->id);

                    // Store the result in session
                    if ($result['success']) {
                        Session::flash('attendance_success', $result['message']);
                    } elseif (isset($result['should_show_manual']) && $result['should_show_manual']) {
                        Session::flash('attendance_manual_needed', 'You are not in office network. Please mark attendance manually if you are working from office.');
                    }

                    // Mark that we've checked today
                    Session::put($sessionKey, true);
                }
            }
        }

        return $next($request);
    }

    private function shouldCheckAttendance($user)
    {
        // Only run auto-check for worker roles. Adjust the roles list as needed.
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['Employee', 'Peregrine Closer', 'Peregrine Validator', 'Verifier', 'Trainer', 'Ravens Closer'])
            : false;
    }
}
