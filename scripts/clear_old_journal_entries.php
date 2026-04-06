<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;

DB::statement('SET FOREIGN_KEY_CHECKS=0');

// 1. Load all lines before deleting to reverse balances
$lines = DB::table('ledger_journal_entry_lines')
    ->join('ledger_journal_entries', 'ledger_journal_entries.id', '=', 'ledger_journal_entry_lines.journal_entry_id')
    ->get(['ledger_journal_entry_lines.account_id', 'ledger_journal_entry_lines.debit', 'ledger_journal_entry_lines.credit']);

echo "Found " . count($lines) . " journal lines to remove.\n";

// 2. Reverse account balances
foreach ($lines as $line) {
    $acct = ChartOfAccount::find($line->account_id);
    if (!$acct) continue;
    $normalDebit = in_array($acct->account_type, ['Asset', 'Expense']);
    if ($normalDebit) {
        $acct->current_balance -= ($line->debit - $line->credit);
    } else {
        $acct->current_balance -= ($line->credit - $line->debit);
    }
    $acct->save();
    echo "  Reversed account {$acct->account_code} ({$acct->name}): new balance = {$acct->current_balance}\n";
}

// 3. Unlink any leads
$unlinked = DB::table('leads')->whereNotNull('ledger_journal_entry_id')->update(['ledger_journal_entry_id' => null]);
echo "Unlinked {$unlinked} lead(s) from journal entries.\n";

// 4. Delete all
DB::table('ledger_journal_entry_lines')->delete();
$deleted = DB::table('ledger_journal_entries')->delete();
echo "Deleted {$deleted} journal entries.\n";

// 5. Reset auto-increment
DB::statement('ALTER TABLE ledger_journal_entries AUTO_INCREMENT = 1');
DB::statement('ALTER TABLE ledger_journal_entry_lines AUTO_INCREMENT = 1');

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Done. Ledger is now clean.\n";
