<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add granular sub-modules for Peregrine and Ravens operations
     * so each page can be individually controlled via permissions.
     */
    public function up(): void
    {
        $newModules = [
            // Peregrine sub-modules
            [
                'name' => 'Peregrine Dashboard',
                'slug' => 'peregrine-dashboard',
                'description' => 'Peregrine team overview dashboard',
                'category' => 'Peregrine Operations',
                'sort_order' => 121,
                'is_active' => true,
            ],
            [
                'name' => 'Verifier Form',
                'slug' => 'peregrine-verifier',
                'description' => 'Peregrine verifier form and submissions',
                'category' => 'Peregrine Operations',
                'sort_order' => 122,
                'is_active' => true,
            ],
            [
                'name' => 'Peregrine Closers',
                'slug' => 'peregrine-closers',
                'description' => 'Peregrine closer operations and lead management',
                'category' => 'Peregrine Operations',
                'sort_order' => 123,
                'is_active' => true,
            ],
            [
                'name' => 'Validation Dashboard',
                'slug' => 'peregrine-validation',
                'description' => 'Peregrine lead validation and approval dashboard',
                'category' => 'Peregrine Operations',
                'sort_order' => 124,
                'is_active' => true,
            ],

            // Ravens sub-modules
            [
                'name' => 'Ravens Dashboard',
                'slug' => 'ravens-dashboard',
                'description' => 'Ravens team overview dashboard',
                'category' => 'Ravens Operations',
                'sort_order' => 131,
                'is_active' => true,
            ],
            [
                'name' => 'Ravens Calling',
                'slug' => 'ravens-calling',
                'description' => 'Ravens outbound calling interface',
                'category' => 'Ravens Operations',
                'sort_order' => 132,
                'is_active' => true,
            ],
            [
                'name' => 'Bad Leads',
                'slug' => 'ravens-bad-leads',
                'description' => 'Manage and review bad/rejected leads',
                'category' => 'Ravens Operations',
                'sort_order' => 133,
                'is_active' => true,
            ],
            [
                'name' => 'Followups & Bank Verification',
                'slug' => 'ravens-followups',
                'description' => 'Lead followups and bank verification tracking',
                'category' => 'Ravens Operations',
                'sort_order' => 134,
                'is_active' => true,
            ],
        ];

        foreach ($newModules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],
                array_merge($module, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Auto-assign 'full' permission for these new sub-modules 
        // to all roles that currently have permission on the parent modules
        $parentMappings = [
            'peregrine' => ['peregrine-dashboard', 'peregrine-verifier', 'peregrine-closers', 'peregrine-validation'],
            'ravens' => ['ravens-dashboard', 'ravens-calling', 'ravens-bad-leads', 'ravens-followups'],
        ];

        foreach ($parentMappings as $parentSlug => $childSlugs) {
            $parentModule = DB::table('modules')->where('slug', $parentSlug)->first();
            if (!$parentModule) continue;

            // Get all role permissions for the parent module
            $parentPermissions = DB::table('role_module_permissions')
                ->where('module_id', $parentModule->id)
                ->get();

            foreach ($childSlugs as $childSlug) {
                $childModule = DB::table('modules')->where('slug', $childSlug)->first();
                if (!$childModule) continue;

                foreach ($parentPermissions as $perm) {
                    DB::table('role_module_permissions')->updateOrInsert(
                        [
                            'role_id' => $perm->role_id,
                            'module_id' => $childModule->id,
                        ],
                        [
                            'permission_level' => $perm->permission_level,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugs = [
            'peregrine-dashboard', 'peregrine-verifier', 'peregrine-closers', 'peregrine-validation',
            'ravens-dashboard', 'ravens-calling', 'ravens-bad-leads', 'ravens-followups',
        ];

        $moduleIds = DB::table('modules')->whereIn('slug', $slugs)->pluck('id');
        
        DB::table('role_module_permissions')->whereIn('module_id', $moduleIds)->delete();
        DB::table('user_module_permissions')->whereIn('module_id', $moduleIds)->delete();
        DB::table('modules')->whereIn('slug', $slugs)->delete();
    }
};
