<?php

namespace App\Support;

use App\Models\User;

/**
 * Central registry of team/department identifiers used in lead routing,
 * analytics, and dashboard queries.
 *
 * These values are stored in the `leads.team` and `users.department` columns.
 */
class Teams
{
    public const PEREGRINE  = 'peregrine';
    public const RAVENS     = 'ravens';
    public const CC_PARTNER = 'cc_partner';
    public const HELL_CATS  = 'hell_cats';

    /** All valid team identifiers */
    public const ALL = [
        self::PEREGRINE,
        self::RAVENS,
        self::CC_PARTNER,
        self::HELL_CATS,
    ];

    /**
     * Human-readable label for a team value. For CC_PARTNER, prefers the
     * specific submitting company's name (e.g. "Falcon") over the generic
     * bucket label, so CC Partner sales are identifiable everywhere a team
     * is shown, not just lumped under one generic tag.
     */
    public static function label(string $team, ?string $assignedPartner = null): string
    {
        return match ($team) {
            self::PEREGRINE  => 'Peregrine',
            self::RAVENS     => 'Ravens',
            self::CC_PARTNER => $assignedPartner ?: 'CC Partner',
            self::HELL_CATS  => 'Hell Cats',
            default          => $team,
        };
    }

    /**
     * Closer role for a team that shares the Peregrine-style PJC/closer/
     * validator pipeline (VerifierController, PeregrineController,
     * ValidatorController).
     */
    public static function closerRole(string $team): ?string
    {
        return match ($team) {
            self::PEREGRINE => Roles::PEREGRINE_CLOSER,
            self::HELL_CATS => Roles::HELL_CATS_CLOSER,
            default         => null,
        };
    }

    /**
     * Validator role for a team that shares the Peregrine-style pipeline.
     */
    public static function validatorRole(string $team): ?string
    {
        return match ($team) {
            self::PEREGRINE => Roles::PEREGRINE_VALIDATOR,
            self::HELL_CATS => Roles::HELL_CATS_VALIDATOR,
            default         => null,
        };
    }

    /**
     * Resolve which Peregrine-pipeline team a closer/validator/manager
     * belongs to, based on their role. Defaults to PEREGRINE so existing
     * Peregrine/Manager/Coordinator/Admin behavior is unchanged — only
     * users holding a Hell Cats role are routed to Hell Cats data.
     */
    public static function fromUser(?User $user): string
    {
        if (!$user) {
            return self::PEREGRINE;
        }

        $hellCatsRoles = [
            Roles::HELL_CATS_CLOSER,
            Roles::HELL_CATS_VALIDATOR,
            Roles::HELL_CATS_MANAGER,
            Roles::HELL_CATS_PJC,
        ];

        return $user->hasAnyRole($hellCatsRoles) ? self::HELL_CATS : self::PEREGRINE;
    }
}
