<?php

use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Models\UserModulePermission;
use Illuminate\Database\Migrations\Migration;

/**
 * Remove dead/unnecessary modules from the permission system:
 * 
 * 1. "holidays" — Old Holiday module that is NOT used anywhere in the
 *    sidebar or HR hub. The app only uses "public-holidays" (PublicHolidayController).
 *    The holidays routes/controller still exist but are unreachable from the UI.
 * 
 * 2. "notifications" — The notification system is infrastructure (topbar bell icon)
 *    that all authenticated users access. It's not a manageable module — there's
 *    nothing to gate behind permissions.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slugsToRemove = ['holidays', 'notifications'];

        $moduleIds = Module::whereIn('slug', $slugsToRemove)->pluck('id');

        // Remove any permissions referencing these modules
        RoleModulePermission::whereIn('module_id', $moduleIds)->delete();
        UserModulePermission::whereIn('module_id', $moduleIds)->delete();

        // Deactivate and soft-delete the modules
        Module::whereIn('slug', $slugsToRemove)->delete();
    }

    public function down(): void
    {
        // Re-create the modules if rolling back
        Module::updateOrCreate(
            ['slug' => 'holidays'],
            [
                'name'        => 'Holidays',
                'slug'        => 'holidays',
                'description' => 'Manage company holidays and office closures',
                'category'    => 'HR Operations',
                'sort_order'  => 170,
                'is_active'   => true,
            ]
        );

        Module::updateOrCreate(
            ['slug' => 'notifications'],
            [
                'name'        => 'Notifications',
                'slug'        => 'notifications',
                'description' => 'System notifications and alerts management',
                'category'    => 'Communication',
                'sort_order'  => 310,
                'is_active'   => true,
            ]
        );
    }
};
