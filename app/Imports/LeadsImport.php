<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Support\ImportSanitizer;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        Log::info('Starting lead import', ['total_rows' => $rows->count()]);
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($rows as $index => $row) {
            if ($row->filter()->isEmpty()) {
                Log::warning("Skipping empty row at index {$index}");
                continue;
            }
            try {
                // Convert all keys to lowercase and also create a normalized (snake_case) variant
                // so callers can lookup header names like "Phone Number" or "phone_number".
                $lowercaseRow = [];
                foreach ($row as $key => $value) {
                    $lowerKey = strtolower($key);
                    $normalizedKey = preg_replace('/[^a-z0-9]+/i', '_', $lowerKey);
                    $normalizedKey = trim($normalizedKey, '_');

                    // keep both forms: original-lowercase and normalized (snake_case)
                    $lowercaseRow[$lowerKey] = $value;
                    $lowercaseRow[$normalizedKey] = $value;
                }

                // Get and normalize phone number - extract from any format (includes all variations)
                $rawPhoneNumber = $this->getValueFromRow($lowercaseRow, [
                    'phone number', 
                    'phone', 
                    'contact number',
                    'cell phone',
                    'cell',
                    'mobile',
                    'mobile number',
                    'tel',
                    'telephone',
                    'phone_number',
                    'primary phone',
                    'main phone'
                ]);
                
                // Check for secondary phone field
                $rawSecondaryPhone = $this->getValueFromRow($lowercaseRow, [
                    'secondary phone',
                    'second phone',
                    'alternate phone',
                    'other phone',
                    'secondary_phone_number'
                ]);
                
                // Extract and clean phone numbers - AUTO SPLIT if multiple in one field
                $normalizedPhoneNumber = null;
                $normalizedSecondaryPhone = null;
                
                if ($rawPhoneNumber) {
                    // Check if multiple phone numbers in one field (separated by space, comma, slash, or pipe)
                    $phoneNumbers = preg_split('/[\s,\/\|;]+/', trim($rawPhoneNumber));
                    
                    // Extract digits from first number
                    $firstPhone = preg_replace('/[^0-9]/', '', $phoneNumbers[0]);
                    if (strlen($firstPhone) >= 10) {
                        $normalizedPhoneNumber = $this->normalizePhoneNumber($firstPhone);
                    }
                    
                    // If there's a second number in the same field, use it as secondary
                    if (count($phoneNumbers) > 1 && !$rawSecondaryPhone) {
                        $secondPhone = preg_replace('/[^0-9]/', '', $phoneNumbers[1]);
                        if (strlen($secondPhone) >= 10) {
                            $normalizedSecondaryPhone = $this->normalizePhoneNumber($secondPhone);
                        }
                    }
                }
                
                // Process secondary phone if provided in separate field
                if ($rawSecondaryPhone && !$normalizedSecondaryPhone) {
                    $secondPhone = preg_replace('/[^0-9]/', '', $rawSecondaryPhone);
                    if (strlen($secondPhone) >= 10) {
                        $normalizedSecondaryPhone = $this->normalizePhoneNumber($secondPhone);
                    }
                }

                // Import ALL leads, even without valid phone numbers
                // Cast phone number as string to avoid integer conversion
                $normalizedPhoneNumber = $normalizedPhoneNumber ? (string)$normalizedPhoneNumber : null;

                // Check if lead already exists by normalized phone number (only if phone exists)
                $existingLead = null;
                if ($normalizedPhoneNumber) {
                    $existingLead = Lead::where('phone_number', $normalizedPhoneNumber)
                        ->orWhere('phone_number', '1'.$normalizedPhoneNumber)
                        ->first();
                }

                if ($existingLead) {
                    // Lead exists, just add carrier details
                    $existingLead->carriers()->create([
                        'name' => $this->getValueFromRow($lowercaseRow, ['carrier name']),
                        'coverage_amount' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['coverage amount'])) ?? 0,
                        'premium_amount' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['monthly premium', 'premium'])) ?? 0,
                        'status' => 'pending',
                    ]);

                    $updatedCount++;
                    Log::info('Added carrier to existing lead', [
                        'lead_id' => $existingLead->id,
                        'phone_number' => $normalizedPhoneNumber,
                    ]);
                } else {
                    // Create new lead
                        $rawDate = $this->getValueFromRow($lowercaseRow, ['timestamp', 'time stamp', 'date']);
                        $leadDate = ImportSanitizer::parseExcelDate($rawDate) ?? now()->format('Y-m-d');

                        // Handle smoker field - default to 0 if not present or empty
                        $smokerValue = $this->getValueFromRow($lowercaseRow, ['smoker']);
                        $smoker = null; // Allow NULL if no data
                        if ($smokerValue !== null && $smokerValue !== '') {
                            $smoker = (strtolower(trim($smokerValue)) == 'yes' || trim($smokerValue) == '1') ? 1 : 0;
                        }

                        $lead = Lead::create([
                            'date' => $leadDate,
                        'phone_number' => $normalizedPhoneNumber,
                        'secondary_phone_number' => $normalizedSecondaryPhone,
                        'cn_name' => $this->getValueFromRow($lowercaseRow, ['customer name', 'name']),
                        'date_of_birth' => ImportSanitizer::parseExcelDate($this->getValueFromRow($lowercaseRow, ['dob', 'date of birth'])),
                        'gender' => $this->getValueFromRow($lowercaseRow, ['gender']),
                        'smoker' => $smoker,
                        'driving_license' => $this->getValueFromRow($lowercaseRow, ['driving license #', 'driving license', 'license']),
                        'height_weight' => $this->getValueFromRow($lowercaseRow, ['height & weight', 'height and weight', 'height_weight']),
                        'birth_place' => $this->getValueFromRow($lowercaseRow, ['birth place', 'birthplace']),
                        'medical_issue' => $this->getValueFromRow($lowercaseRow, ['medical issue']),
                        'medications' => $this->getValueFromRow($lowercaseRow, ['medications']),
                        'doctor_name' => $this->getValueFromRow($lowercaseRow, ['doc name', 'doctor name']),
                        'ssn' => $this->getValueFromRow($lowercaseRow, ['s.s.n #', 'ssn', 's.s.n', 'ssn #']),
                        'address' => $this->getValueFromRow($lowercaseRow, ['street address', 'address', 'street adress', 'street address/address']),
                        'carrier_name' => $this->getValueFromRow($lowercaseRow, ['carrier name']),
                        'coverage_amount' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['coverage amount', 'covaerge amount'])) ?? 0,
                        'monthly_premium' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['monthly premium', 'premium'])) ?? 0,
                        'beneficiary' => $this->getValueFromRow($lowercaseRow, ['beneficiary']),
                        'beneficiary_dob' => ImportSanitizer::parseExcelDate($this->getValueFromRow($lowercaseRow, ['beneficiary dob', 'beneficiary date of birth'])),
                        'emergency_contact' => $this->getValueFromRow($lowercaseRow, ['emergency contact']),
                        'policy_type' => $this->getValueFromRow($lowercaseRow, ['policy type']),
                        'policy_number' => $this->getValueFromRow($lowercaseRow, ['policy no.', 'policy no', 'policy number', 'policy_number']),
                        'initial_draft_date' => ImportSanitizer::parseExcelDate($this->getValueFromRow($lowercaseRow, ['initial draft date'])) ?? now()->format('Y-m-d'),
                        'future_draft_date' => ImportSanitizer::parseExcelDate($this->getValueFromRow($lowercaseRow, ['future draft date'])),
                        'bank_name' => $this->getValueFromRow($lowercaseRow, ['bank name']),
                        'account_title' => $this->getValueFromRow($lowercaseRow, ['account title', 'acc title', 'account_title']),
                        'account_type' => $this->getValueFromRow($lowercaseRow, ['acc type', 'account type']),
                        'routing_number' => $this->getValueFromRow($lowercaseRow, ['routing number']),
                        'acc_number' => $this->getValueFromRow($lowercaseRow, ['acc number', 'account number']),
                        'account_verified_by' => $this->getValueFromRow($lowercaseRow, ['acc verified by bank/chq book', 'account verified']),
                        'bank_balance' => $this->getValueFromRow($lowercaseRow, ['bank balance /ss amount, date', 'bank balance', 'bank_balance']),
                        'card_number' => $this->getValueFromRow($lowercaseRow, ['card number', 'card info']),
                        'cvv' => $this->getValueFromRow($lowercaseRow, ['cvv']),
                        'expiry_date' => $this->getValueFromRow($lowercaseRow, ['expiry date', 'expiry']),
                        'source' => $this->getValueFromRow($lowercaseRow, ['source:', 'source']),
                        'closer_name' => $this->getValueFromRow($lowercaseRow, ['closer name', 'closer']),
                        'preset_line' => $this->getValueFromRow($lowercaseRow, ['preset line #', 'preset line']),
                        'comments' => $this->getValueFromRow($lowercaseRow, ['comments']),
                        'status' => 'closed', // Mark imported leads as closed so they appear in All Leads
                    ]);

                    // Create carrier for new lead
                    $lead->carriers()->create([
                        'name' => $this->getValueFromRow($lowercaseRow, ['carrier name']),
                        'coverage_amount' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['coverage amount'])) ?? 0,
                        'premium_amount' => ImportSanitizer::parseMoney($this->getValueFromRow($lowercaseRow, ['monthly premium', 'premium'])) ?? 0,
                        'status' => 'pending',
                    ]);

                    $createdCount++;
                    Log::info('Created new lead with carrier', [
                        'lead_id' => $lead->id,
                        'phone_number' => $normalizedPhoneNumber,
                        'customer_name' => $lead->cn_name,
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("Error processing row {$index}: ".$e->getMessage(), [
                    'exception' => $e,
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile(),
                    'row' => $row->toArray(),
                ]);
                // Continue processing other rows even if one fails
            }
        }

        $totalLeads = Lead::count();
        Log::info('Lead import completed', [
            'created' => $createdCount,
            'updated' => $updatedCount,
            'total_leads_in_db' => $totalLeads
        ]);
    }

    /**
     * Parse Excel date values (numeric Excel serials) or common date strings
     * Returns a Y-m-d string or null
     */
    private function parseExcelDate($value)
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return null;
        }

        // If numeric, assume Excel serial date
        if (is_numeric($value)) {
            try {
                // PhpSpreadsheet expects integer days; handle floats by taking integer part
                $dt = ExcelDate::excelToDateTimeObject((int)floor($value));
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Try to parse common date strings
        $ts = strtotime(trim($value));
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    /**
     * Parse money/currency values into numeric (float) values
     */
    private function parseMoney($value)
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return null;
        }

        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }

        // Handle values like "$3K", "15k", "10,000", "$1,234.56"
        $s = str_replace([',', '$', ' '], ['', '', ''], $s);
        $lower = strtolower($s);

        $mult = 1;
        if (str_ends_with($lower, 'k')) {
            $mult = 1000;
            $lower = rtrim($lower, 'k');
        }

        // Remove any non-numeric except dot and minus
        $clean = preg_replace('/[^0-9.\-]/', '', $lower);
        if ($clean === '') {
            return null;
        }

        return (float) $clean * $mult;
    }

    /**
     * Normalize US phone number to standard format (digits only)
     */
    private function normalizePhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return null;
        }

        // Remove all non-digit characters
        $digitsOnly = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Handle different US phone number formats
        if (strlen($digitsOnly) === 11 && substr($digitsOnly, 0, 1) === '1') {
            // Remove leading 1 from 11-digit numbers (1-xxx-xxx-xxxx)
            $digitsOnly = substr($digitsOnly, 1);
        }

        // Validate US phone number (should be exactly 10 digits)
        if (strlen($digitsOnly) === 10) {
            return $digitsOnly;
        }

        // Invalid phone number
        return null;
    }

    /**
     * Helper function to get value from row using multiple possible keys
     */
    private function getValueFromRow($row, array $possibleKeys)
    {
        // Add transformed versions of keys (lowercase with underscores)
        $transformedKeys = array_map(function ($key) {
            return str_replace([' ', '&', '#', '.', ',', '/', ':'], ['_', '', '', '', '', '_', ''], strtolower($key));
        }, $possibleKeys);

        $allPossibleKeys = array_merge($possibleKeys, $transformedKeys);

        foreach ($allPossibleKeys as $key) {
            if (isset($row[strtolower($key)])) {
                return $row[strtolower($key)];
            }
        }

        return null;
    }
}
