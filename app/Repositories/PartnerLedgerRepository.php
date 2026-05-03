<?php

namespace App\Repositories;

use App\Models\Partner;
use App\Repositories\Contracts\PartnerLedgerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Partner Ledger Repository
 * 
 * Handles all partner-specific ledger queries and balance calculations
 * Using the chart_of_accounts and ledger_journal_entry_lines system
 */
class PartnerLedgerRepository implements PartnerLedgerRepositoryInterface
{
    protected const ACCOUNT_AR = '1200'; // Accounts Receivable – Partners

    /**
     * Get partner's current balance from AR account
     * Positive = partner owes us, Negative = we owe them
     * EXCLUDES chargeback/sales_return entries — shared industry losses, not partner debt
     *
     * @param Partner $partner
     * @return float
     */
    public function getBalance(Partner $partner): float
    {
        $arAccount = $this->getARAccount();

        if (!$arAccount) {
            return 0;
        }

        $balance = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->whereNotIn('je.type', ['sales_return', 'chargeback'])
            ->selectRaw('SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')
            ->first();

        return ((float) ($balance->total_debit ?? 0)) - ((float) ($balance->total_credit ?? 0));
    }

    /**
     * Get full ledger for a partner with running balances
     * 
     * @param Partner $partner
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return Collection
     */
    public function getLedger(Partner $partner, \DateTime $from = null, \DateTime $to = null): Collection
    {
        $arAccount = $this->getARAccount();

        if (!$arAccount) {
            return collect();
        }

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->leftJoin('insurance_carriers as ic', 'l.insurance_carrier_id', '=', 'ic.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->orderBy('je.entry_date', 'asc')
            ->orderBy('l.id', 'asc');

        if ($from) {
            $query->where('je.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('je.entry_date', '<=', $to);
        }

        $lines = $query->select([
            'l.id',
            'je.id as entry_id',
            'je.entry_date',
            'je.type',
            'je.reference',
            'ic.name as carrier_name',
            'l.debit',
            'l.credit',
            'je.description',
        ])->get();

        // Calculate running balance
        $runningBalance = 0;
        return $lines->map(function ($line) use (&$runningBalance) {
            $runningBalance += ($line->debit - $line->credit);

            return [
                'id' => $line->id,
                'entry_id' => $line->entry_id,
                'date' => \Carbon\Carbon::parse($line->entry_date),
                'type' => $line->type,
                'reference' => $line->reference,
                'carrier' => $line->carrier_name ?? 'General',
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
                'running_balance' => (float) $runningBalance,
                'description' => $line->description,
            ];
        })->values();
    }

    /**
     * Get ledger filtered by carrier
     * 
     * @param Partner $partner
     * @param int $carrierId
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return Collection
     */
    public function getLedgerByCarrier(Partner $partner, int $carrierId, \DateTime $from = null, \DateTime $to = null): Collection
    {
        return $this->getLedger($partner, $from, $to)
            ->filter(fn($line) => $this->isCarrierMatch($line, $carrierId))
            ->values();
    }

    /**
     * Get summary of payments received from partner
     * (Credit entries = partner paid us)
     * 
     * @param Partner $partner
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return array
     */
    public function getPaymentsSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array
    {
        $arAccount = $this->getARAccount();

        if (!$arAccount) {
            return [
                'total_payments' => 0,
                'payment_count' => 0,
                'last_payment_date' => null,
            ];
        }

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->where('je.type', 'payment_received');

        if ($from) {
            $query->where('je.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('je.entry_date', '<=', $to);
        }

        $total = $query->sum('l.credit') ?? 0;
        $count = $query->count();
        $lastPayment = Db::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->where('je.type', 'payment_received')
            ->orderByDesc('je.entry_date')
            ->first();

        return [
            'total_payments' => (float) $total,
            'payment_count' => (int) $count,
            'last_payment_date' => $lastPayment ? \Carbon\Carbon::parse($lastPayment->entry_date) : null,
            'average_payment' => $count > 0 ? round($total / $count, 2) : 0,
        ];
    }

    /**
     * Get summary of sales posted (Debit entries = sales to partner)
     * 
     * @param Partner $partner
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return array
     */
    public function getSalesSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array
    {
        $arAccount = $this->getARAccount();

        if (!$arAccount) {
            return [
                'total_sales' => 0,
                'sales_count' => 0,
                'average_sale' => 0,
            ];
        }

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->where('je.type', 'sale');

        if ($from) {
            $query->where('je.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('je.entry_date', '<=', $to);
        }

        $total = $query->sum('l.debit') ?? 0;
        $count = $query->count();

        return [
            'total_sales' => (float) $total,
            'sales_count' => (int) $count,
            'average_sale' => $count > 0 ? round($total / $count, 2) : 0,
        ];
    }

    /**
     * Get summary of chargebacks (sales returns)
     * 
     * @param Partner $partner
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return array
     */
    public function getChargebacksSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array
    {
        $arAccount = $this->getARAccount();

        if (!$arAccount) {
            return [
                'total_chargebacks' => 0,
                'chargeback_count' => 0,
                'average_chargeback' => 0,
            ];
        }

        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $partner->id)
            ->where('l.account_id', $arAccount->id)
            ->whereIn('je.type', ['sales_return', 'chargeback']);

        if ($from) {
            $query->where('je.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('je.entry_date', '<=', $to);
        }

        $total = $query->sum('l.credit') ?? 0;
        $count = $query->count();

        return [
            'total_chargebacks' => (float) $total,
            'chargeback_count' => (int) $count,
            'average_chargeback' => $count > 0 ? round($total / $count, 2) : 0,
        ];
    }

    /**
     * Get outstanding balance aging (how long partner has owed balance)
     * 
     * @param Partner $partner
     * @return array [due_today, due_30_days, due_60_days, due_90_plus]
     */
    public function getBalanceAging(Partner $partner): array
    {
        $ledger = $this->getLedger($partner);
        $lastEntry = $ledger->last();
        $currentBalance = $lastEntry ? $lastEntry['running_balance'] : 0;

        if ($currentBalance <= 0) {
            // Partner doesn't owe us
            return [
                'current' => 0,
                'due_30' => 0,
                'due_60' => 0,
                'due_90_plus' => 0,
                'total_due' => 0,
            ];
        }

        // For now, assume all balance is current (can be enhanced with aging logic)
        // In real business, you'd apply aging based on invoice dates
        return [
            'current' => round($currentBalance, 2),
            'due_30' => 0,
            'due_60' => 0,
            'due_90_plus' => 0,
            'total_due' => round($currentBalance, 2),
        ];
    }

    /**
     * Get ledger statistics for dashboard KPIs
     * 
     * @param Partner $partner
     * @return array
     */
    public function getDashboardStats(Partner $partner): array
    {
        $from = now()->startOfYear();
        $to = now()->endOfYear();

        $balance = $this->getBalance($partner);
        $sales = $this->getSalesSummary($partner, $from, $to);
        $payments = $this->getPaymentsSummary($partner, $from, $to);
        $chargebacks = $this->getChargebacksSummary($partner, $from, $to);

        return [
            'current_balance' => round($balance, 2),
            'ytd_sales_total' => $sales['total_sales'],
            'ytd_sales_count' => $sales['sales_count'],
            'ytd_payments_total' => $payments['total_payments'],
            'ytd_payments_count' => $payments['payment_count'],
            'ytd_chargebacks_total' => $chargebacks['total_chargebacks'],
            'ytd_chargebacks_count' => $chargebacks['chargeback_count'],
            'net_ytd_revenue' => $sales['total_sales'] - $chargebacks['total_chargebacks'] - $payments['total_payments'],
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────

    /**
     * Get AR account record
     */
    protected function getARAccount()
    {
        return DB::table('chart_of_accounts')
            ->where('account_code', self::ACCOUNT_AR)
            ->first();
    }

    /**
     * Helper to match carrier in ledger entry
     */
    protected function isCarrierMatch(array $line, int $carrierId): bool
    {
        // In a real scenario, you'd have carrier_id stored in the ledger line
        // For now, this is a placeholder
        return true;
    }
}
