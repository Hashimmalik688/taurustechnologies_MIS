<?php

namespace App\Services;

use App\Models\CarrierSheetEntry;
use App\Models\CarrierSheetRate;
use App\Models\Lead;
use App\Models\Partner;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CarrierSheetLeadSyncService
{
    public function __construct(
        private readonly CarrierSheetService $carrierSheetService
    ) {
    }

    /**
     * Ensure a lead marked as chargeback is reflected in Carrier Sheet.
     */
    public function syncChargebackForLead(Lead $lead): ?CarrierSheetEntry
    {
        if (strtolower((string) $lead->status) !== Statuses::LEAD_CHARGEBACK) {
            return null;
        }

        $rate = $this->resolveCarrierRate($lead);
        if (!$rate) {
            Log::info('Carrier sheet sync skipped: no matching sheet found for chargeback lead', [
                'lead_id' => $lead->id,
                'carrier_name' => $lead->carrier_name,
                'partner_id' => $lead->partner_id,
                'assigned_partner' => $lead->assigned_partner,
            ]);
            return null;
        }

        $chargebackDate = $lead->chargeback_marked_date
            ? Carbon::parse($lead->chargeback_marked_date)
            : now();
        $periodMonth = $chargebackDate->copy()->startOfMonth()->toDateString();

        $entry = $this->findExistingEntry($rate->id, $lead);

        if (!$entry) {
            $entry = new CarrierSheetEntry([
                'carrier_sheet_rate_id' => $rate->id,
                'sr_number' => (int) (CarrierSheetEntry::where('carrier_sheet_rate_id', $rate->id)->max('sr_number') ?? 0) + 1,
                'entry_date' => $chargebackDate->toDateString(),
                'policy_number' => $lead->policy_number,
                'name' => $lead->cn_name,
                'face_value' => $this->toFaceValue($lead->coverage_amount),
                'premium' => (float) ($lead->monthly_premium ?? 0),
                'policy_type' => $lead->policy_type,
                'status' => CarrierSheetEntry::STATUS_CHARGEBACK,
                'draft_date' => $lead->initial_draft_date,
                'payment_date' => $lead->future_draft_date,
                'paid_amount' => 0,
                'chargeback_amount' => $this->estimateChargebackAmount($rate, $lead),
                'period_month' => $periodMonth,
            ]);
        } else {
            $entry->status = CarrierSheetEntry::STATUS_CHARGEBACK;
            $entry->entry_date = $chargebackDate->toDateString();
            $entry->period_month = $periodMonth;

            if ((float) $entry->chargeback_amount <= 0) {
                $entry->chargeback_amount = round(max((float) $entry->paid_amount, (float) ($entry->commission ?? 0), 0), 2);
            }
        }

        $this->carrierSheetService->recalculateEntry($entry);
        $entry->save();
        $this->carrierSheetService->clearCache($rate->id, $periodMonth);

        return $entry;
    }

    private function resolveCarrierRate(Lead $lead): ?CarrierSheetRate
    {
        $partnerCode = $this->resolvePartnerCode($lead);
        $carrierSlug = $this->resolveCarrierSlug((string) $lead->carrier_name);

        if (!$partnerCode || !$carrierSlug) {
            return null;
        }

        $fullSlug = $carrierSlug . '-' . strtolower($partnerCode);

        return CarrierSheetRate::where('carrier_slug', $fullSlug)->first()
            ?? CarrierSheetRate::where('partner_code', $partnerCode)
                ->where('carrier_slug', 'like', $carrierSlug . '-%')
                ->first();
    }

    private function resolvePartnerCode(Lead $lead): ?string
    {
        $code = null;

        if ($lead->relationLoaded('partner')) {
            $code = $lead->partner?->code;
        } elseif ($lead->partner_id) {
            $code = Partner::where('id', $lead->partner_id)->value('code');
        }

        if (!$code && !empty($lead->assigned_partner)) {
            $code = (string) $lead->assigned_partner;
        }

        if (!$code) {
            return null;
        }

        return strtoupper(trim($code));
    }

    private function resolveCarrierSlug(string $carrierName): ?string
    {
        $name = strtolower(trim($carrierName));
        if ($name === '') {
            return null;
        }

        $map = [
            'transamerica' => 'ta',
            't.a' => 'ta',
            'ta' => 'ta',
            'aig' => 'aig',
            'american general' => 'aig',
            'amam' => 'amam',
            'american amicable' => 'amam',
            'securian' => 'sec',
            'sec' => 'sec',
            'royal arcanum' => 'ra',
            'r.a' => 'ra',
            'aetna' => 'aetna',
            'mutual of omaha' => 'moo',
            'moo' => 'moo',
        ];

        foreach ($map as $needle => $slug) {
            if (str_contains($name, $needle)) {
                return $slug;
            }
        }

        return null;
    }

    private function findExistingEntry(int $carrierRateId, Lead $lead): ?CarrierSheetEntry
    {
        $policyNumber = trim((string) $lead->policy_number);

        if ($policyNumber !== '' && !$this->isPlaceholderPolicyNumber($policyNumber)) {
            $byPolicy = CarrierSheetEntry::where('carrier_sheet_rate_id', $carrierRateId)
                ->where('policy_number', $policyNumber)
                ->orderByDesc('id')
                ->first();

            if ($byPolicy) {
                return $byPolicy;
            }
        }

        return CarrierSheetEntry::where('carrier_sheet_rate_id', $carrierRateId)
            ->where('name', (string) $lead->cn_name)
            ->when($lead->monthly_premium, function ($query) use ($lead) {
                $query->where('premium', (float) $lead->monthly_premium);
            })
            ->orderByDesc('id')
            ->first();
    }

    private function estimateChargebackAmount(CarrierSheetRate $rate, Lead $lead): float
    {
        $premium = (float) ($lead->monthly_premium ?? 0);
        if ($premium <= 0) {
            return 0.0;
        }

        $estimatedCommission = $this->carrierSheetService->calculateCommission(
            $rate,
            $lead->policy_type,
            $premium,
            CarrierSheetEntry::STATUS_APPROVED,
            0
        );

        return round(max((float) ($estimatedCommission ?? 0), 0), 2);
    }

    private function toFaceValue($coverageAmount): ?string
    {
        if (!is_numeric($coverageAmount)) {
            return null;
        }

        $amount = (float) $coverageAmount;
        if ($amount <= 0) {
            return null;
        }

        return $amount >= 1000
            ? ((string) round($amount / 1000)) . 'K'
            : (string) $amount;
    }

    private function isPlaceholderPolicyNumber(string $policyNumber): bool
    {
        $normalized = strtolower(trim($policyNumber));
        return in_array($normalized, ['na', 'n/a', 'n.a', 'none', 'tbd', '-'], true);
    }
}
