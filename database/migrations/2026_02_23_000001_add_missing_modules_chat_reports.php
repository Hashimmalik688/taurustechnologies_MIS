<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

/**
 * Add missing modules that were not in the original seeder:
 * - chat-shadow (Chat & Note Shadowing)
 * - reports (Reports & Analytics exports)
 * - chat (Team Chat & Messaging)
 * - team-dashboards (Team Performance Dashboards)
 * - notifications (Notification Management)
 *
 * These modules are already used in the application but were
 * missing from the module permission system, so they didn't
 * appear in the Permission Manager.
 */
return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            [
                'name'        => 'Chat Shadowing',
                'slug'        => 'chat-shadow',
                'description' => 'Monitor & review user conversations and notes in read-only mode',
                'category'    => 'Settings',
                'sort_order'  => 225,
                'is_active'   => true,
            ],
            [
                'name'        => 'Reports',
                'slug'        => 'reports',
                'description' => 'Generate sales, partner, agent & manager reports with CSV export',
                'category'    => 'Settings',
                'sort_order'  => 226,
                'is_active'   => true,
            ],
            [
                'name'        => 'Team Chat',
                'slug'        => 'chat',
                'description' => 'Real-time team messaging, group chats & community announcements',
                'category'    => 'Communication',
                'sort_order'  => 300,
                'is_active'   => true,
            ],
            [
                'name'        => 'Team Dashboards',
                'slug'        => 'team-dashboards',
                'description' => 'Peregrine & Ravens team performance dashboards',
                'category'    => 'Company Overview',
                'sort_order'  => 15,
                'is_active'   => true,
            ],
            [
                'name'        => 'Notifications',
                'slug'        => 'notifications',
                'description' => 'System notifications and alerts management',
                'category'    => 'Communication',
                'sort_order'  => 310,
                'is_active'   => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }

        // Auto-grant full access to Super Admin for all new modules
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $newModules = Module::whereIn('slug', ['chat-shadow', 'reports', 'chat', 'team-dashboards', 'notifications'])->get();
            foreach ($newModules as $module) {
                \App\Models\RoleModulePermission::updateOrCreate(
                    ['role_id' => $superAdminRole->id, 'module_id' => $module->id],
                    ['permission_level' => 'full']
                );
            }
        }

        // Auto-grant chat view access to all roles (everyone can chat)
        $chatModule = Module::where('slug', 'chat')->first();
        if ($chatModule) {
            $allRoles = \Spatie\Permission\Models\Role::all();
            foreach ($allRoles as $role) {
                \App\Models\RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $chatModule->id],
                    ['permission_level' => 'full']
                );
            }
        }

        // Grant CEO full access to new modules
        $ceoRole = \Spatie\Permission\Models\Role::where('name', 'CEO')->first();
        if ($ceoRole) {
            $newModules = Module::whereIn('slug', ['chat-shadow', 'reports', 'team-dashboards', 'notifications'])->get();
            foreach ($newModules as $module) {
                \App\Models\RoleModulePermission::updateOrCreate(
                    ['role_id' => $ceoRole->id, 'module_id' => $module->id],
                    ['permission_level' => 'full']
                );
            }
        }

        // Grant Manager & Coordinator view access to reports and team-dashboards
        $managerRoles = \Spatie\Permission\Models\Role::whereIn('name', ['Manager', 'Co-ordinator'])->get();
        foreach ($managerRoles as $role) {
            foreach (['reports', 'team-dashboards', 'notifications'] as $slug) {
                $mod = Module::where('slug', $slug)->first();
                if ($mod) {
                    \App\Models\RoleModulePermission::updateOrCreate(
                        ['role_id' => $role->id, 'module_id' => $mod->id],
                        ['permission_level' => 'view']
                    );
                }
            }
        }
    }

    public function down(): void
    {
        $slugs = ['chat-shadow', 'reports', 'chat', 'team-dashboards', 'notifications'];

        // Remove permissions first
        $moduleIds = Module::whereIn('slug', $slugs)->pluck('id');
        \App\Models\RoleModulePermission::whereIn('module_id', $moduleIds)->delete();
        \App\Models\UserModulePermission::whereIn('module_id', $moduleIds)->delete();

        // Remove modules
        Module::whereIn('slug', $slugs)->delete();
    }
};
