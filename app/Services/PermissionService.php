<?php

namespace App\Services;

use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Models\UserModulePermission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    /**
     * Check if user can view a module
     * 
     * @param User $user
     * @param string $moduleSlug
     * @return bool
     */
    public function canView(User $user, string $moduleSlug): bool
    {
        return $this->hasPermission($user, $moduleSlug, 'view');
    }

    /**
     * Check if user can edit in a module
     * 
     * @param User $user
     * @param string $moduleSlug
     * @return bool
     */
    public function canEdit(User $user, string $moduleSlug): bool
    {
        return $this->hasPermission($user, $moduleSlug, 'edit');
    }

    /**
     * Check if user can delete in a module (requires full access)
     * 
     * @param User $user
     * @param string $moduleSlug
     * @return bool
     */
    public function canDelete(User $user, string $moduleSlug): bool
    {
        return $this->hasPermission($user, $moduleSlug, 'full');
    }

    /**
     * Check if user has specific permission level for a module
     * 
     * @param User $user
     * @param string $moduleSlug
     * @param string $requiredLevel (view|edit|full)
     * @return bool
     */
    public function hasPermission(User $user, string $moduleSlug, string $requiredLevel): bool
    {
        $userLevel = $this->getUserPermissionForModule($user, $moduleSlug);

        $levels = ['none' => 0, 'view' => 1, 'edit' => 2, 'full' => 3];
        $userNumeric = $levels[$userLevel] ?? 0;
        $requiredNumeric = $levels[$requiredLevel] ?? 0;

        return $userNumeric >= $requiredNumeric;
    }

    /**
     * Get user's permission level for a specific module
     * Returns highest permission from user overrides or role permissions
     * 
     * @param User $user
     * @param string $moduleSlug
     * @return string (none|view|edit|full)
     */
    public function getUserPermissionForModule(User $user, string $moduleSlug): string
    {
        // Cache key unique to user and module
        $cacheKey = "user_permission_{$user->id}_{$moduleSlug}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user, $moduleSlug) {
            return $this->calculatePermission($user, $moduleSlug);
        });
    }

    /**
     * Calculate permission without cache
     * 
     * @param User $user
     * @param string $moduleSlug
     * @return string
     */
    protected function calculatePermission(User $user, string $moduleSlug): string
    {
        // Super Admin always has full access to everything
        if ($user->hasRole('Super Admin')) {
            return 'full';
        }

        // Get module
        $module = Module::where('slug', $moduleSlug)->where('is_active', true)->first();
        if (!$module) {
            Log::warning("Permission check for non-existent module: {$moduleSlug}");
            return 'none';
        }

        // Check for user-specific override (takes precedence)
        $userOverride = UserModulePermission::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->first();

        if ($userOverride) {
            return $userOverride->permission_level;
        }

        // Get highest permission from all user's roles
        $roleIds = $user->roles->pluck('id')->toArray();
        if (empty($roleIds)) {
            return 'none';
        }

        $rolePermission = RoleModulePermission::where('module_id', $module->id)
            ->whereIn('role_id', $roleIds)
            ->orderByRaw("FIELD(permission_level, 'full', 'edit', 'view', 'none')")
            ->first();

        return $rolePermission ? $rolePermission->permission_level : 'none';
    }

    /**
     * Set permission for a role on a module
     * 
     * @param int $roleId
     * @param string $moduleSlug
     * @param string $level (none|view|edit|full)
     * @return bool
     */
    public function setRolePermission(int $roleId, string $moduleSlug, string $level): bool
    {
        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) {
            return false;
        }

        RoleModulePermission::updateOrCreate(
            [
                'role_id' => $roleId,
                'module_id' => $module->id,
            ],
            [
                'permission_level' => $level,
            ]
        );

        // Clear cache for all users with this role
        $this->clearRolePermissionCache($roleId);

        return true;
    }

    /**
     * Set permission for a specific user on a module (override)
     * 
     * @param int $userId
     * @param string $moduleSlug
     * @param string $level (none|view|edit|full)
     * @return bool
     */
    public function setUserPermission(int $userId, string $moduleSlug, string $level): bool
    {
        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) {
            return false;
        }

        UserModulePermission::updateOrCreate(
            [
                'user_id' => $userId,
                'module_id' => $module->id,
            ],
            [
                'permission_level' => $level,
            ]
        );

        // Clear cache for this specific user
        $this->clearUserPermissionCache($userId);

        return true;
    }

    /**
     * Remove user permission override (revert to role permissions)
     * 
     * @param int $userId
     * @param string $moduleSlug
     * @return bool
     */
    public function removeUserPermission(int $userId, string $moduleSlug): bool
    {
        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) {
            return false;
        }

        UserModulePermission::where('user_id', $userId)
            ->where('module_id', $module->id)
            ->delete();

        // Clear cache for this user
        $this->clearUserPermissionCache($userId);

        return true;
    }

    /**
     * Get all permissions for a role
     * 
     * @param int $roleId
     * @return \Illuminate\Support\Collection
     */
    public function getRolePermissions(int $roleId)
    {
        return RoleModulePermission::where('role_id', $roleId)
            ->with('module')
            ->get();
    }

    /**
     * Get all permission overrides for a user
     * 
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getUserPermissions(int $userId)
    {
        return UserModulePermission::where('user_id', $userId)
            ->with('module')
            ->get();
    }

    /**
     * Bulk set permissions for a role
     * 
     * @param int $roleId
     * @param array $permissions ['module_slug' => 'level', ...]
     * @return bool
     */
    public function bulkSetRolePermissions(int $roleId, array $permissions): bool
    {
        foreach ($permissions as $moduleSlug => $level) {
            $this->setRolePermission($roleId, $moduleSlug, $level);
        }

        return true;
    }

    /**
     * Clear permission cache for a specific user
     * 
     * @param int $userId
     * @return void
     */
    public function clearUserPermissionCache(int $userId): void
    {
        // Get all module slugs
        $modules = Module::pluck('slug');

        foreach ($modules as $moduleSlug) {
            Cache::forget("user_permission_{$userId}_{$moduleSlug}");
        }
    }

    /**
     * Clear permission cache for all users with a specific role
     * 
     * @param int $roleId
     * @return void
     */
    public function clearRolePermissionCache(int $roleId): void
    {
        // Get all users with this role
        $users = User::role(\Spatie\Permission\Models\Role::find($roleId)->name)->get();

        foreach ($users as $user) {
            $this->clearUserPermissionCache($user->id);
        }
    }

    /**
     * Clear all permission caches
     * 
     * @return void
     */
    public function clearAllPermissionCaches(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->clearUserPermissionCache($user->id);
        }
    }

    /**
     * Get permission matrix for a role (all modules with their permission levels)
     * 
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionMatrix(int $roleId): array
    {
        $modules = Module::active()->ordered()->get();
        $permissions = RoleModulePermission::where('role_id', $roleId)
            ->pluck('permission_level', 'module_id');

        $matrix = [];
        foreach ($modules as $module) {
            $matrix[$module->slug] = [
                'module' => $module,
                'permission_level' => $permissions[$module->id] ?? 'none',
            ];
        }

        return $matrix;
    }

    /**
     * Get permission matrix for a user (showing inherited and overridden permissions)
     * 
     * @param int $userId
     * @return array
     */
    public function getUserPermissionMatrix(int $userId): array
    {
        $user = User::findOrFail($userId);
        $modules = Module::active()->ordered()->get();

        $userOverrides = UserModulePermission::where('user_id', $userId)
            ->pluck('permission_level', 'module_id');

        $roleIds = $user->roles->pluck('id')->toArray();

        $matrix = [];
        foreach ($modules as $module) {
            // Check for user override first
            if (isset($userOverrides[$module->id])) {
                $permissionLevel = $userOverrides[$module->id];
                $source = 'override';
            } else {
                // Get from roles
                $rolePermission = RoleModulePermission::where('module_id', $module->id)
                    ->whereIn('role_id', $roleIds)
                    ->orderByRaw("FIELD(permission_level, 'full', 'edit', 'view', 'none')")
                    ->first();

                $permissionLevel = $rolePermission ? $rolePermission->permission_level : 'none';
                $source = 'role';
            }

            $matrix[$module->slug] = [
                'module' => $module,
                'permission_level' => $permissionLevel,
                'source' => $source, // 'role' or 'override'
            ];
        }

        return $matrix;
    }
}
