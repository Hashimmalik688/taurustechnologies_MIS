<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Serialises generation of human-readable sequential numbers that are derived
 * from MAX(column) + 1 (journal-entry numbers, petty-cash serials,
 * carrier-sheet SR numbers, ...).
 *
 * Without serialisation, two concurrent requests both read the same MAX and
 * generate the same "next" number, producing duplicate / colliding identifiers.
 *
 * Implemented with a MySQL named advisory lock (GET_LOCK / RELEASE_LOCK) so the
 * read-max-then-insert critical section runs for one connection at a time.
 * Unlike `lockForUpdate` on the current max row, an advisory lock is also
 * correct when the table (or the filtered subset) is empty — e.g. the first
 * entry of a new year — because there is no row to lock in that case.
 *
 * IMPORTANT: when the insert happens inside a DB transaction, the lock must
 * wrap the WHOLE transaction (call SequenceLock::run OUTSIDE DB::transaction),
 * not just the read+insert. Under MySQL's default REPEATABLE READ isolation a
 * concurrent reader would otherwise still see the pre-insert MAX until the
 * first transaction commits. For a single auto-committed insert (no explicit
 * transaction) wrapping the read+insert is sufficient.
 *
 * NOTE: GET_LOCK/RELEASE_LOCK and the work inside the callback must run on the
 * same database connection. Laravel reuses a single PDO per connection for the
 * lifetime of a request, so this holds as long as the callback uses the default
 * connection (which every current caller does). This helper is MySQL-specific.
 */
class SequenceLock
{
    /**
     * Execute $callback while holding a named advisory lock.
     *
     * @template TReturn
     *
     * @param  string  $name  Logical sequence name, e.g. "ledger_journal_entry".
     * @param  callable():TReturn  $callback  The read-max + insert critical section.
     * @param  int  $timeout  Seconds to wait for the lock before failing.
     * @return TReturn
     */
    public static function run(string $name, callable $callback, int $timeout = 10)
    {
        $lockName = 'seq:' . $name;

        $result = DB::selectOne('SELECT GET_LOCK(?, ?) AS acquired', [$lockName, $timeout]);

        if (! $result || (int) $result->acquired !== 1) {
            throw new RuntimeException(
                "Could not acquire sequence lock [{$name}] within {$timeout}s."
            );
        }

        try {
            return $callback();
        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }
}
