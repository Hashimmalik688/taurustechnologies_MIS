<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Employee;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        // Only create Employee if not already exists for this email
        if (!Employee::where('email', $user->email)->exists()) {
            Employee::create([
                'name' => $user->name,
                'email' => $user->email,
                'contact_info' => '',
                'emergency_contact' => '',
                'cnic' => '',
                'position' => '',
                'area_of_residence' => '',
                'status' => $user->status ?? 'Active',
                'mis' => 'Yes',
                'passport_image' => null,
                'account_password' => null,
            ]);
        }
    }
}
