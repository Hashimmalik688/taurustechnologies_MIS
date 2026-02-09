<?php

namespace App\Listeners;

use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MarkEmployeeAsTerminated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Find employee by email and mark as terminated
        if (isset($event->user) && $event->user->email) {
            $employee = Employee::where('email', $event->user->email)->first();
            
            if ($employee) {
                $employee->update([
                    'status' => 'Terminated',
                    'mis' => 'No', // Remove MIS access
                ]);
                
                \Log::info("Employee marked as Terminated", [
                    'employee_id' => $employee->id,
                    'email' => $employee->email,
                    'user_id' => $event->user->id ?? null
                ]);
            }
        }
    }
}
