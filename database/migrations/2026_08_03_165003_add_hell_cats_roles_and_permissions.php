<?php

use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * Hell Cats is a second team on the Peregrine PJC/closer/validator pipeline
 * (VerifierController, PeregrineController, ValidatorController) — same
 * forms, same routes, same "peregrine-*" permission modules. Only the
 * `leads.team` value differs, resolved per-user by Teams::fromUser().
 *
 * No separate Hell Cats Validator role — validation for both teams is
 * handled by the one shared Peregrine Validator pool.
 *
 * Creates the roles only — no permissions are auto-granted. Access must be
 * assigned explicitly per role via Settings → Permission Manager.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([Roles::HELL_CATS_MANAGER, Roles::HELL_CATS_CLOSER] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $roleNames = [Roles::HELL_CATS_MANAGER, Roles::HELL_CATS_CLOSER];

        foreach (Role::whereIn('name', $roleNames)->get() as $role) {
            \App\Models\RoleModulePermission::where('role_id', $role->id)->delete();
            $role->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
