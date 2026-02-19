<?php

namespace App\Support;

use App\Models\Module;
use Illuminate\Contracts\Auth\Authenticatable;

class SidebarHelper
{
    /**
     * Get visible modules for the authenticated user based on their permissions
     *
     * @param  Authenticatable|null  $user
     * @param  array  $moduleNames  Array of module slugs to filter
     * @return array
     */
    public static function getVisibleModules(?Authenticatable $user, array $moduleNames = []): array
    {
        if (!$user) {
            return [];
        }

        // If specific modules are requested, filter to those
        $query = Module::where('is_active', true);
        
        if (!empty($moduleNames)) {
            $query->whereIn('slug', $moduleNames);
        }

        $modules = $query->get();
        $visibleModules = [];

        foreach ($modules as $module) {
            // Only include if user can view this module
            if ($user->canViewModule($module->slug)) {
                $visibleModules[$module->slug] = $module;
            }
        }

        return $visibleModules;
    }

    /**
     * Check if a user can view a specific module
     *
     * @param  Authenticatable|null  $user
     * @param  string  $moduleSlug
     * @return bool
     */
    public static function canView(?Authenticatable $user, string $moduleSlug): bool
    {
        if (!$user) {
            return false;
        }

        return $user->canViewModule($moduleSlug);
    }

    /**
     * Check if a user can edit a specific module
     *
     * @param  Authenticatable|null  $user
     * @param  string  $moduleSlug
     * @return bool
     */
    public static function canEdit(?Authenticatable $user, string $moduleSlug): bool
    {
        if (!$user) {
            return false;
        }

        return $user->canEditModule($moduleSlug);
    }

    /**
     * Check if a user can delete in a specific module
     *
     * @param  Authenticatable|null  $user
     * @param  string  $moduleSlug
     * @return bool
     */
    public static function canDelete(?Authenticatable $user, string $moduleSlug): bool
    {
        if (!$user) {
            return false;
        }

        return $user->canDeleteInModule($moduleSlug);
    }
}
