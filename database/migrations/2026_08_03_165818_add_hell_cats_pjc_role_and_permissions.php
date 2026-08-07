<?php

use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * "Hell Cats Junior Closer" — the Hell Cats equivalent of "Peregrines
 * Junior Closer" (Roles::VERIFIER). Shares the same PJC form
 * (VerifierController + peregrine-verifier module); only the team tag
 * differs, resolved via Teams::fromUser().
 *
 * Creates the role only — no permissions are auto-granted. Access must be
 * assigned explicitly via Settings → Permission Manager.
 */
return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(['name' => Roles::HELL_CATS_PJC, 'guard_name' => 'web']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::where('name', Roles::HELL_CATS_PJC)->first();

        if ($role) {
            \App\Models\RoleModulePermission::where('role_id', $role->id)->delete();
            $role->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
