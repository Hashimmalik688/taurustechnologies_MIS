<?php

use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $role = Role::firstOrCreate(['name' => Roles::IT_MANAGER, 'guard_name' => 'web']);

        $permissions = [
            'dashboard'            => 'view',
            'leads-peregrine'      => 'none',
            'hr'                   => 'none',
            'finance'              => 'none',
            'ems'                  => 'view',
            'attendance'           => 'view',
            'dock'                 => 'none',
            'holidays'             => 'view',
            'leads'                => 'none',
            'sales'                => 'none',
            'issuance'             => 'none',
            'qa-review'            => 'none',
            'bank-verification'    => 'none',
            'peregrine'            => 'none',
            'peregrine-dashboard'  => 'none',
            'peregrine-verifier'   => 'none',
            'peregrine-closers'    => 'none',
            'peregrine-validation' => 'none',
            'ravens'               => 'none',
            'ravens-dashboard'     => 'none',
            'ravens-calling'       => 'none',
            'ravens-bad-leads'     => 'none',
            'ravens-followups'     => 'none',
            'ravens-validation'    => 'none',
            'retention'            => 'none',
            'chargebacks'          => 'none',
            'partners'             => 'view',
            'carriers'             => 'view',
            'payroll'              => 'none',
            'chart-of-accounts'    => 'none',
            'general-ledger'       => 'none',
            'petty-cash'           => 'none',
            'pabs-tickets'         => 'none',
            'revenue-analytics'    => 'none',
            'live-analytics'       => 'none',
            'users'                => 'full',
            'settings'             => 'full',
            'duplicate-checker'    => 'full',
            'account-switch-log'   => 'full',
            'epms'                 => 'none',
        ];

        $modules = Module::all()->keyBy('slug');

        foreach ($permissions as $slug => $level) {
            if (!isset($modules[$slug])) {
                continue;
            }

            RoleModulePermission::firstOrCreate(
                [
                    'role_id'   => $role->id,
                    'module_id' => $modules[$slug]->id,
                ],
                ['permission_level' => $level]
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::where('name', Roles::IT_MANAGER)->first();

        if ($role) {
            RoleModulePermission::where('role_id', $role->id)->delete();
            $role->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
