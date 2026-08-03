<?php

use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * "Hell Cats Junior Closer" — the Hell Cats equivalent of "Peregrines
 * Junior Closer" (Roles::VERIFIER). Shares the same PJC form
 * (VerifierController + peregrine-verifier module); only the team tag
 * differs, resolved via Teams::fromUser().
 */
return new class extends Migration
{
    public function up(): void
    {
        $role = Role::firstOrCreate(['name' => Roles::HELL_CATS_PJC, 'guard_name' => 'web']);

        $modules = Module::all()->keyBy('slug');

        $permissions = [
            'dashboard' => 'none',
            'leads-peregrine' => 'none',
            'hr' => 'none',
            'finance' => 'none',
            'ems' => 'none',
            'attendance' => 'view',
            'dock' => 'view',
            'holidays' => 'view',
            'leads' => 'none',
            'sales' => 'view',
            'issuance' => 'view',
            'qa-review' => 'none',
            'bank-verification' => 'none',
            'peregrine' => 'full',
            'peregrine-dashboard' => 'full',
            'peregrine-verifier' => 'full',
            'peregrine-closers' => 'none',
            'peregrine-validation' => 'none',
            'ravens' => 'none',
            'ravens-dashboard' => 'none',
            'ravens-calling' => 'none',
            'ravens-bad-leads' => 'none',
            'ravens-followups' => 'none',
            'ravens-validation' => 'none',
            'retention' => 'none',
            'chargebacks' => 'none',
            'partners' => 'none',
            'carriers' => 'none',
            'payroll' => 'none',
            'chart-of-accounts' => 'none',
            'general-ledger' => 'none',
            'petty-cash' => 'none',
            'pabs-tickets' => 'none',
            'revenue-analytics' => 'none',
            'live-analytics' => 'none',
            'users' => 'none',
            'settings' => 'none',
            'duplicate-checker' => 'none',
            'account-switch-log' => 'none',
            'epms' => 'none',
        ];

        foreach ($permissions as $slug => $level) {
            if (!isset($modules[$slug])) {
                continue;
            }

            RoleModulePermission::firstOrCreate(
                ['role_id' => $role->id, 'module_id' => $modules[$slug]->id],
                ['permission_level' => $level]
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::where('name', Roles::HELL_CATS_PJC)->first();

        if ($role) {
            RoleModulePermission::where('role_id', $role->id)->delete();
            $role->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
