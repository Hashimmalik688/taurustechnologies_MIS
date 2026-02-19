<?php

namespace App\Support;

/**
 * Central definition of module permission levels.
 *
 * Used by User, RoleModulePermission, and UserModulePermission to avoid
 * duplicating the permission hierarchy map in multiple files.
 */
class PermissionLevel
{
    public const NONE = 'none';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const FULL = 'full';

    /**
     * Permission level hierarchy — higher numeric value = more access.
     */
    public const LEVELS = [
        self::NONE => 0,
        self::VIEW => 1,
        self::EDIT => 2,
        self::FULL => 3,
    ];

    /**
     * Get the numeric value for a permission level string.
     */
    public static function numeric(string $level): int
    {
        return self::LEVELS[$level] ?? 0;
    }
}
