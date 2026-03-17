<?php

use App\Models\Module;
use App\Models\RoleModulePermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Seed the new Sales Pipeline modules and set their permissions.
 *
 * Changes:
 *  - Rename 'Policy Submission' module → 'Pending Contract'
 *  - Add 'pendings-approved'  module (Pendings Approved page)
 *  - Add 'pending-draft'      module (Pending Draft page)
 *  - Add 'paid-sales'         module (Paid Sales page)
 *
 * Permissions mirror the issuance module, with Retention Officer
 * getting edit access to pendings-approved and pending-draft.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Rename the existing issuance module to "Pending Contract"
        Module::where('slug', 'issuance')
            ->update([
                'name'        => 'Pending Contract',
                'description' => 'Manage policy issuance and pending contract submissions',
            ]);

        // Base permission map (mirrors issuance module)
        $basePermissions = [
            'Super Admin'        => 'full',
            'CEO'                => 'full',
            'Manager'            => 'full',
            'Co-ordinator'       => 'full',
            'HR'                 => 'view',
            'QA'                 => 'view',
            'Employee'           => 'view',
            'Verifier'           => 'view',
            'Peregrine Closer'   => 'view',
            'Peregrine Validator'=> 'view',
            'Ravens Closer'      => 'view',
            'Retention Officer'  => 'none',
        ];

        $newModules = [
            [
                'name'        => 'Pendings Approved',
                'slug'        => 'pendings-approved',
                'description' => 'Review manager-approved leads before sending to Pending Contract; mark Not Issued',
                'category'    => 'Sales Operations',
                'sort_order'  => 38,
                'is_active'   => true,
                // Override: Retention Officer can view + resolve Not Issued blocks
                'overrides'   => ['Retention Officer' => 'edit'],
            ],
            [
                'name'        => 'Pending Draft',
                'slug'        => 'pending-draft',
                'description' => 'Track leads awaiting first premium draft; mark Not Paid (FDFP) or Paid',
                'category'    => 'Sales Operations',
                'sort_order'  => 55,
                'is_active'   => true,
                // Override: Retention Officer can mark Not Paid (FDFP)
                'overrides'   => ['Retention Officer' => 'edit'],
            ],
            [
                'name'        => 'Paid Sales',
                'slug'        => 'paid-sales',
                'description' => 'View successfully paid sales (first premium draft cleared)',
                'category'    => 'Sales Operations',
                'sort_order'  => 57,
                'is_active'   => true,
                // Override: Retention Officer can view paid sales
                'overrides'   => ['Retention Officer' => 'view'],
            ],
        ];

        $roles = Role::all()->keyBy('name');

        foreach ($newModules as $moduleData) {
            $overrides = $moduleData['overrides'] ?? [];
            unset($moduleData['overrides']);

            // Upsert module
            $module = Module::firstOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );

            // Assign permissions
            $permissions = array_merge($basePermissions, $overrides);

            foreach ($permissions as $roleName => $level) {
                $role = $roles->get($roleName);
                if (!$role) {
                    continue;
                }

                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    ['permission_level' => $level]
                );
            }
        }
    }

    public function down(): void
    {
        // Revert issuance module name
        Module::where('slug', 'issuance')
            ->update([
                'name'        => 'Policy Submission',
                'description' => 'Manage policy issuance, submissions, and status updates',
            ]);

        // Remove new modules (cascades delete permissions via FK if set,
        // otherwise delete manually first)
        $slugs = ['pendings-approved', 'pending-draft', 'paid-sales'];

        foreach ($slugs as $slug) {
            $module = Module::where('slug', $slug)->first();
            if ($module) {
                RoleModulePermission::where('module_id', $module->id)->delete();
                $module->delete();
            }
        }
    }
};
