<?php

use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * Hell Cats is a second team on the Peregrine PJC/closer/validator pipeline
 * (VerifierController, PeregrineController, ValidatorController) — same
 * forms, same routes, same "peregrine-*" permission modules. Only the
 * `leads.team` value differs, resolved per-user by Teams::fromUser().
 */
return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            Roles::HELL_CATS_MANAGER   => $this->managerPermissions(),
            Roles::HELL_CATS_CLOSER    => $this->closerPermissions(),
            Roles::HELL_CATS_VALIDATOR => $this->validatorPermissions(),
        ];

        $modules = Module::all()->keyBy('slug');

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            foreach ($permissions as $slug => $level) {
                if (!isset($modules[$slug])) {
                    continue;
                }

                RoleModulePermission::firstOrCreate(
                    ['role_id' => $role->id, 'module_id' => $modules[$slug]->id],
                    ['permission_level' => $level]
                );
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $roleNames = [Roles::HELL_CATS_MANAGER, Roles::HELL_CATS_CLOSER, Roles::HELL_CATS_VALIDATOR];

        foreach (Role::whereIn('name', $roleNames)->get() as $role) {
            RoleModulePermission::where('role_id', $role->id)->delete();
            $role->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function baseline(): array
    {
        return [
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
            'peregrine' => 'none',
            'peregrine-dashboard' => 'none',
            'peregrine-verifier' => 'none',
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
    }

    private function closerPermissions(): array
    {
        return array_merge($this->baseline(), [
            'bank-verification' => 'view',
            'peregrine' => 'full',
            'peregrine-closers' => 'full',
        ]);
    }

    private function validatorPermissions(): array
    {
        return array_merge($this->baseline(), [
            'peregrine' => 'full',
            'peregrine-validation' => 'full',
        ]);
    }

    private function managerPermissions(): array
    {
        return array_merge($this->baseline(), [
            'bank-verification' => 'view',
            'peregrine' => 'full',
            'peregrine-dashboard' => 'full',
            'peregrine-verifier' => 'full',
            'peregrine-closers' => 'full',
            'peregrine-validation' => 'full',
        ]);
    }
};
