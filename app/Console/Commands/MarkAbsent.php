<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;

class MarkAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent {--roles=* : Roles to include (comma separated or multiple options) } {--cutoff=19:30 : Cutoff time (H:i) to consider absence}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark absent users who did not record attendance by cutoff time';

    /** @var AttendanceService */
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();

        $this->attendanceService = $attendanceService;
    }

    public function handle()
    {
        $this->info('Starting absentee check...');

        $roles = $this->option('roles');
        if (empty($roles)) {
            // default list of worker roles - adjust as needed
            $roles = ['Employee', 'Live Closer', 'Verification Officer', 'Verifier', 'Trainer', 'Ravens Closer'];
        }

        $cutoff = $this->option('cutoff') ?: '19:30';

        $today = Carbon::today();

        // Skip marking absent if today is a public holiday
        if (\App\Models\Holiday::isHoliday($today)) {
            $this->info('Today is a public holiday. Skipping absence marking.');
            return 0;
        }

        // Get users with the provided roles
        $usersQuery = User::query()->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });

        $users = $usersQuery->get();

        $countMarked = 0;

        foreach ($users as $user) {
            // Skip if attendance already exists
            if (Attendance::hasRecordForDate($user->id, $today->toDateString())) {
                continue;
            }

            // Create absent record
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'status' => 'absent',
                'ip_address' => 'Auto-marked absent',
            ]);

            $countMarked++;
        }

        $this->info("Marked {$countMarked} users as absent for {$today->toDateString()}.");

        return 0;
    }
}
