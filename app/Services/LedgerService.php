<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\LedgerJournalEntry;
use App\Models\LedgerJournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LedgerService
{
    // ── Account code constants ─────────────────────────────────────────────

    const ACCOUNT_BANK         = '1100';
    const ACCOUNT_AR            = '1200';
    const ACCOUNT_AP_CARRIERS   = '2100';
    const ACCOUNT_OBE           = '3900';
    const ACCOUNT_SALES         = '4100';
    const ACCOUNT_SALES_RETURNS = '4200';

    // ── Public entry creators ──────────────────────────────────────────────

    /**
     * Record a Policy Sale.
     *
     * Dr  1200 Accounts Receivable (partner)
     * Cr  4100 Sales / Commission Income
     */
    public function createSaleEntry(
        int    $partnerId,
        float  $amount,
        string $date,
        string $description,
        ?string $reference       = null,
        ?int   $carrierId        = null,
        ?float $grossAmount      = null,
        ?float $sharePercentage  = null,
        ?string $insuredName     = null,
        ?int   $leadId           = null
    ): LedgerJournalEntry {
        $ar    = $this->account(self::ACCOUNT_AR);
        $sales = $this->account(self::ACCOUNT_SALES);

        $lines = [
            $this->line($ar->id,    $partnerId, $amount, 0,      $description, 1, $carrierId),
            $this->line($sales->id, null,       0,       $amount, $description, 2),
        ];

        return $this->persist('sale', $date, $description, $reference, $lines, [
            'gross_amount'         => $grossAmount,
            'our_share_percentage' => $sharePercentage,
            'insured_name'         => $insuredName,
            'lead_id'              => $leadId,
        ]);
    }

    /**
     * Record a ChargeBack / Sales Return.
     *
     * Dr  4200 Sales Returns / Chargebacks   (contra-revenue; our income is clawed back)
     * Cr  2100 Accounts Payable — Carriers   (we now owe the carrier)
     */
    public function createChargebackEntry(
        int    $partnerId,
        float  $amount,
        string $date,
        string $description,
        ?string $reference    = null,
        ?int   $carrierId     = null,
        ?string $insuredName  = null,
        ?float $grossAmount   = null,
        ?float $sharePercentage = null
    ): LedgerJournalEntry {
        $salesReturns = $this->account(self::ACCOUNT_SALES_RETURNS);
        $apCarriers   = $this->account(self::ACCOUNT_AP_CARRIERS);

        $lines = [
            $this->line($salesReturns->id, $partnerId, $amount, 0,      $description, 1, $carrierId),
            $this->line($apCarriers->id,   $partnerId, 0,       $amount, $description, 2, $carrierId),
        ];

        return $this->persist('chargeback', $date, $description, $reference, $lines, [
            'insured_name'         => $insuredName,
            'gross_amount'         => $grossAmount,
            'our_share_percentage' => $sharePercentage,
        ]);
    }

    /**
     * Record a Sales Return (pipeline chargeback reversal).
     *
     * Dr  4200 Sales Returns / Chargebacks   (contra-revenue)
     * Cr  1200 Accounts Receivable           (partner's AR is reduced)
     *
     * This is the correct double-entry reversal of a sale entry:
     *   Original sale:   Dr 1200 AR / Cr 4100 Sales
     *   Return entry:    Dr 4200 Returns / Cr 1200 AR
     */
    public function createSalesReturnEntry(
        int    $partnerId,
        float  $amount,
        string $date,
        string $description,
        ?string $reference      = null,
        ?int   $carrierId       = null,
        ?string $insuredName    = null,
        ?float $grossAmount     = null,
        ?float $sharePercentage = null,
        ?int   $leadId          = null
    ): LedgerJournalEntry {
        $salesReturns = $this->account(self::ACCOUNT_SALES_RETURNS);
        $ar           = $this->account(self::ACCOUNT_AR);

        $lines = [
            $this->line($salesReturns->id, $partnerId, $amount, 0,      $description, 1, $carrierId),
            $this->line($ar->id,           $partnerId, 0,       $amount, $description, 2, $carrierId),
        ];

        return $this->persist('sales_return', $date, $description, $reference, $lines, [
            'insured_name'         => $insuredName,
            'gross_amount'         => $grossAmount,
            'our_share_percentage' => $sharePercentage,
            'lead_id'              => $leadId,
        ]);
    }

    /**
     * Record a Payment Received from a partner.
     *
     * Dr  1100 Cash / Bank
     * Cr  1200 Accounts Receivable (partner)
     */
    public function createPaymentEntry(
        int    $partnerId,
        float  $amount,
        string $date,
        string $description,
        ?string $reference = null,
        ?int   $carrierId = null
    ): LedgerJournalEntry {
        $bank = $this->account(self::ACCOUNT_BANK);
        $ar   = $this->account(self::ACCOUNT_AR);

        $lines = [
            $this->line($bank->id, null,       $amount, 0,      $description, 1),
            $this->line($ar->id,   $partnerId, 0,       $amount, $description, 2, $carrierId),
        ];

        return $this->persist('payment_received', $date, $description, $reference, $lines);
    }

    /**
     * Record an opening balance for a partner.
     *
     * If the partner owes US (debit balance = we expect to receive):
     *   Dr 1200 Accounts Receivable  /  Cr 3900 Opening Balance Equity
     *
     * If WE owe the partner (credit balance = liability):
     *   Dr 3900 Opening Balance Equity  /  Cr 1200 Accounts Receivable
     *
     * @param string $normalBalance  'debit'|'credit'
     */
    public function createOpeningBalanceEntry(
        int    $partnerId,
        float  $amount,
        string $normalBalance,
        string $date,
        string $description = 'Opening Balance',
        ?int   $carrierId   = null
    ): LedgerJournalEntry {
        $ar  = $this->account(self::ACCOUNT_AR);
        $obe = $this->account(self::ACCOUNT_OBE);

        if ($normalBalance === 'debit') {
            // Partner owes us
            $lines = [
                $this->line($ar->id,  $partnerId, $amount, 0,      $description, 1, $carrierId),
                $this->line($obe->id, null,       0,       $amount, $description, 2),
            ];
        } else {
            // We owe partner
            $lines = [
                $this->line($obe->id, null,       $amount, 0,      $description, 1),
                $this->line($ar->id,  $partnerId, 0,       $amount, $description, 2, $carrierId),
            ];
        }

        return $this->persist('opening_balance', $date, $description, null, $lines);
    }

    /**
     * Record a free-form general journal entry.
     * Lines must be balanced: sum(debit) == sum(credit).
     *
     * @param  array<array{account_id:int, partner_id:int|null, debit:float, credit:float, description:string|null}> $lines
     */
    public function createGeneralEntry(
        array  $lines,
        string $date,
        string $description,
        ?string $reference = null
    ): LedgerJournalEntry {
        $totalDebit  = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) > 0.005) {
            throw new InvalidArgumentException(
                "Journal entry is unbalanced: debits ({$totalDebit}) ≠ credits ({$totalCredit})."
            );
        }

        $preparedLines = [];
        foreach ($lines as $i => $l) {
            $preparedLines[] = $this->line(
                $l['account_id'],
                $l['partner_id'] ?? null,
                (float) ($l['debit'] ?? 0),
                (float) ($l['credit'] ?? 0),
                $l['description'] ?? null,
                $i + 1,
                isset($l['insurance_carrier_id']) ? (int) $l['insurance_carrier_id'] : null
            );
        }

        return $this->persist('general', $date, $description, $reference, $preparedLines);
    }

    // ── Partner Ledger ─────────────────────────────────────────────────────

    /**
     * Return all ledger lines for a given partner against account 1200 (AR),
     * with a running balance appended to each row.
     *
     * Convention: debit increases the partner's balance (they owe us more),
     *             credit decreases it (they paid or we owed them).
     */
    public function getPartnerLedger(int $partnerId): Collection
    {
        $arAccount = $this->account(self::ACCOUNT_AR);

        $lines = LedgerJournalEntryLine::with(['journalEntry', 'account', 'carrier'])
            ->where('partner_id', $partnerId)
            ->where('account_id', $arAccount->id)
            ->join('ledger_journal_entries', 'ledger_journal_entries.id', '=', 'ledger_journal_entry_lines.journal_entry_id')
            ->orderBy('ledger_journal_entries.entry_date')
            ->orderBy('ledger_journal_entry_lines.id')
            ->select('ledger_journal_entry_lines.*')
            ->get();

        // Attach running balance
        $running = 0;
        return $lines->map(function ($line) use (&$running) {
            $running += ($line->debit - $line->credit);
            $line->running_balance = $running;
            return $line;
        });
    }

    /**
     * Return ledger lines for a given partner+carrier combination.
     * Pass $carrierId = null to retrieve lines with no carrier assigned.
     * Running balance is appended to each row.
     */
    public function getPartnerCarrierLedger(int $partnerId, ?int $carrierId): Collection
    {
        $arAccount = $this->account(self::ACCOUNT_AR);

        $query = LedgerJournalEntryLine::with(['journalEntry', 'account', 'carrier'])
            ->where('partner_id', $partnerId)
            ->where('account_id', $arAccount->id)
            ->join('ledger_journal_entries', 'ledger_journal_entries.id', '=', 'ledger_journal_entry_lines.journal_entry_id')
            ->orderBy('ledger_journal_entries.entry_date')
            ->orderBy('ledger_journal_entry_lines.id')
            ->select('ledger_journal_entry_lines.*');

        if ($carrierId === null) {
            $query->whereNull('ledger_journal_entry_lines.insurance_carrier_id');
        } else {
            $query->where('ledger_journal_entry_lines.insurance_carrier_id', $carrierId);
        }

        $running = 0;
        return $query->get()->map(function ($line) use (&$running) {
            $running += ($line->debit - $line->credit);
            $line->running_balance = $running;
            return $line;
        });
    }

    // ── Private helpers ────────────────────────────────────────────────────

    /**
     * Wrap entry header + lines creation in a DB transaction,
     * then update chart_of_accounts current balances.
     */
    private function persist(
        string  $type,
        string  $date,
        string  $description,
        ?string $reference,
        array   $lines,
        array   $extra = []
    ): LedgerJournalEntry {
        return DB::transaction(function () use ($type, $date, $description, $reference, $lines, $extra) {
            $totalDebit = array_sum(array_column($lines, 'debit'));

            $entry = LedgerJournalEntry::create(array_merge([
                'entry_number' => LedgerJournalEntry::generateEntryNumber(),
                'entry_date'   => $date,
                'type'         => $type,
                'reference'    => $reference,
                'description'  => $description,
                'is_posted'    => true,
                'total_debit'  => $totalDebit,
                'created_by'   => Auth::id(),
            ], $extra));

            foreach ($lines as $lineData) {
                $entry->lines()->create($lineData);
            }

            $this->updateAccountBalances($entry->fresh('lines'));

            return $entry;
        });
    }

    /**
     * Build a line array (not yet saved).
     */
    private function line(
        int    $accountId,
        ?int   $partnerId,
        float  $debit,
        float  $credit,
        ?string $description,
        int    $sortOrder,
        ?int   $carrierId = null
    ): array {
        return [
            'account_id'           => $accountId,
            'partner_id'           => $partnerId,
            'insurance_carrier_id' => $carrierId,
            'debit'                => $debit,
            'credit'               => $credit,
            'description'          => $description,
            'sort_order'           => $sortOrder,
        ];
    }

    /**
     * After posting an entry, update the current_balance on each affected
     * chart_of_accounts row.
     *
     * Normal balance rules:
     *   Asset / Expense      → debit increases, credit decreases
     *   Liability / Equity / Revenue → credit increases, debit decreases
     */
    private function updateAccountBalances(LedgerJournalEntry $entry): void
    {
        foreach ($entry->lines as $line) {
            /** @var ChartOfAccount $account */
            $account = ChartOfAccount::find($line->account_id);
            if (!$account) {
                continue;
            }

            $normalDebit = in_array($account->account_type, ['Asset', 'Expense']);

            if ($normalDebit) {
                $account->current_balance += ($line->debit - $line->credit);
            } else {
                $account->current_balance += ($line->credit - $line->debit);
            }

            $account->save();
        }
    }

    /**
     * Fetch a ChartOfAccount by code or throw if missing.
     */
    public function account(string $code): ChartOfAccount
    {
        $account = ChartOfAccount::where('account_code', $code)->first();

        if (!$account) {
            throw new \RuntimeException(
                "Required system account '{$code}' not found in chart of accounts. " .
                "Please run: php artisan db:seed --class=SystemAccountsSeeder"
            );
        }

        return $account;
    }
}
