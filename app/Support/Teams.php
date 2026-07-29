<?php

namespace App\Support;

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

    /** All valid team identifiers */
    public const ALL = [
        self::PEREGRINE,
        self::RAVENS,
        self::CC_PARTNER,
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
            default          => $team,
        };
    }
}
