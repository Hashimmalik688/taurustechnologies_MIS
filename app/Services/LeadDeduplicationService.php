<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadDeduplicationService
{
    /**
     * Scan all leads for duplicates by phone number and merge them
     * Keeps the most complete record and removes duplicates
     * 
     * CRITICAL: Peregrine team leads are ALWAYS excluded from deduplication
     * to protect closer-submitted leads from being automatically merged.
     */
    public function deduplicateByPhone()
    {
        $duplicates = [];
        $merged = 0;
        $deleted = 0;

        Log::info('Starting lead deduplication by phone number...');

        // Find all duplicate phone numbers (ALWAYS exclude Peregrine leads)
        $dupePhones = Lead::select('phone_number', DB::raw('COUNT(*) as count'))
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->where(function($query) {
                $query->where('team', '!=', 'peregrine')
                      ->orWhereNull('team');
            })
            ->groupBy('phone_number')
            ->having('count', '>', 1)
            ->get();

        foreach ($dupePhones as $dupePhone) {
            $phoneNumber = $dupePhone->phone_number;
            
            // Get all leads with this phone number (ALWAYS exclude Peregrine leads)
            $leads = Lead::where('phone_number', $phoneNumber)
                ->where(function($query) {
                    $query->where('team', '!=', 'peregrine')
                          ->orWhereNull('team');
                })
                ->orderBy('id', 'asc')
                ->get();

            if ($leads->count() < 2) {
                continue;
            }

            Log::info("Found {$leads->count()} duplicate leads for phone: {$phoneNumber}");

            // Keep the first lead and merge others into it
            $primaryLead = $leads->first();
            $duplicateLeads = $leads->slice(1);

            foreach ($duplicateLeads as $dupeLead) {
                $this->mergeLeadData($primaryLead, $dupeLead);
                
                // Delete the duplicate lead
                $dupeLead->delete();
                $deleted++;
            }

            $merged++;
            $duplicates[] = [
                'phone' => $phoneNumber,
                'primary_lead_id' => $primaryLead->id,
                'merged_count' => $duplicateLeads->count(),
            ];
        }

        Log::info('Lead deduplication completed', [
            'total_duplicates_found' => $dupePhones->count(),
            'leads_merged' => $merged,
            'leads_deleted' => $deleted,
        ]);

        return [
            'success' => true,
            'duplicates_found' => $dupePhones->count(),
            'leads_merged' => $merged,
            'leads_deleted' => $deleted,
            'details' => $duplicates,
        ];
    }

    /**
     * Merge data from duplicate lead into primary lead
     * Only updates fields if they are empty in primary lead
     */
    private function mergeLeadData($primaryLead, $duplicateLead)
    {
        $updateData = [];

        // List of fields to merge
        $fieldsToMerge = [
            'ssn',
            'acc_number',
            'cn_name',
            'secondary_phone_number',
            'date_of_birth',
            'gender',
            'address',
            'bank_name',
            'account_type',
            'routing_number',
            'account_verified_by',
            'bank_balance',
            'beneficiary',
            'beneficiary_dob',
            'beneficiaries',
            'carrier_name',
            'coverage_amount',
            'monthly_premium',
            'policy_type',
            'initial_draft_date',
            'source',
            'closer_name',
            'smoker',
            'height_weight',
            'birth_place',
            'medical_issue',
            'medications',
            'doctor_name',
            'emergency_contact',
            'policy_number',
            'card_number',
            'cvv',
            'expiry_date',
        ];

        foreach ($fieldsToMerge as $field) {
            // If primary lead's field is empty and duplicate has a value, merge it
            if (empty($primaryLead->$field) && !empty($duplicateLead->$field)) {
                $updateData[$field] = $duplicateLead->$field;
            }
        }

        // Update primary lead if there's data to merge
        if (!empty($updateData)) {
            $primaryLead->update($updateData);
            Log::info("Merged data into lead {$primaryLead->id}", [
                'merged_fields' => array_keys($updateData),
                'from_lead' => $duplicateLead->id,
            ]);
        }

        // Merge carriers from duplicate to primary
        $duplicateCarriers = $duplicateLead->carriers;
        foreach ($duplicateCarriers as $carrier) {
            // Clone carrier to primary lead
            $primaryLead->carriers()->create([
                'name' => $carrier->name,
                'coverage_amount' => $carrier->coverage_amount,
                'premium_amount' => $carrier->premium_amount,
                'policy_number' => $carrier->policy_number,
                'phone' => $carrier->phone,
                'email' => $carrier->email,
                'website' => $carrier->website,
                'status' => $carrier->status,
                'notes' => $carrier->notes,
            ]);
        }
    }
}
