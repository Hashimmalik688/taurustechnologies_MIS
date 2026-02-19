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
    public const PEREGRINE = 'peregrine';
    public const RAVENS    = 'ravens';

    /** All valid team identifiers */
    public const ALL = [
        self::PEREGRINE,
        self::RAVENS,
    ];
}
