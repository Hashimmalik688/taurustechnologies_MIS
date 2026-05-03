<?php

namespace App\Repositories\Contracts;

use App\Models\Partner;
use Illuminate\Support\Collection;

interface PartnerLedgerRepositoryInterface
{
    public function getBalance(Partner $partner): float;

    public function getLedger(Partner $partner, \DateTime $from = null, \DateTime $to = null): Collection;

    public function getLedgerByCarrier(Partner $partner, int $carrierId, \DateTime $from = null, \DateTime $to = null): Collection;

    public function getPaymentsSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array;

    public function getSalesSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array;

    public function getChargebacksSummary(Partner $partner, \DateTime $from = null, \DateTime $to = null): array;

    public function getBalanceAging(Partner $partner): array;

    public function getDashboardStats(Partner $partner): array;
}
