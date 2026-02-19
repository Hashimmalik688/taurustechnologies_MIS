<?php

namespace App\Support;

/**
 * Central registry of all role name strings used throughout the application.
 *
 * Always reference these constants instead of hardcoding role name strings.
 * Roles are managed by spatie/laravel-permission and stored in the database.
 * This class provides compile-time safety and a single source of truth.
 *
 * @see database/seeders/RoleSeeder.php  — where roles are created
 * @see config/permission.php            — spatie config
 */
class Roles
{
    // ── Core Administrative Roles ──────────────────────────────────────

    public const SUPER_ADMIN = 'Super Admin';
    public const CEO = 'CEO';
    public const MANAGER = 'Manager';
    public const COORDINATOR = 'Co-ordinator';

    // ── Operational Roles ──────────────────────────────────────────────

    public const EMPLOYEE = 'Employee';
    public const HR = 'HR';
    public const QA = 'QA';
    public const VERIFIER = 'Verifier';

    // ── Sales / Pipeline Roles ─────────────────────────────────────────

    public const PEREGRINE_CLOSER = 'Peregrine Closer';
    public const PEREGRINE_VALIDATOR = 'Peregrine Validator';
    public const RAVENS_CLOSER = 'Ravens Closer';
    public const RETENTION_OFFICER = 'Retention Officer';

    // ── All active user roles (for reference / iteration) ──────────────

    public const ALL = [
        self::SUPER_ADMIN,
        self::CEO,
        self::MANAGER,
        self::COORDINATOR,
        self::EMPLOYEE,
        self::HR,
        self::QA,
        self::VERIFIER,
        self::PEREGRINE_CLOSER,
        self::PEREGRINE_VALIDATOR,
        self::RAVENS_CLOSER,
        self::RETENTION_OFFICER,
    ];

    // ── Helpers ────────────────────────────────────────────────────────

    /**
     * Build a Spatie role middleware string from one or more role constants.
     *
     * Usage:  ->middleware(Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN))
     * Result: 'role:CEO|Super Admin'
     */
    public static function middleware(string ...$roles): string
    {
        return 'role:' . implode('|', $roles);
    }
}
