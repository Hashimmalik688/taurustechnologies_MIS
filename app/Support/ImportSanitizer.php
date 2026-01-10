<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportSanitizer
{
    /**
     * Parse Excel date values (numeric Excel serials) or common date strings
     * Returns a Y-m-d string or null
     */
    public static function parseExcelDate($value)
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return null;
        }

        // If numeric, assume Excel serial date (only if it's a reasonable Excel date value)
        if (is_numeric($value)) {
            try {
                $numValue = (int)floor($value);
                // Excel dates range from 1 (1900-01-01) to 2958465 (9999-12-31)
                // Reasonable date range for leads: 1000 (1902) to 50000 (2037)
                if ($numValue > 100 && $numValue < 50000) {
                    $dt = ExcelDate::excelToDateTimeObject($numValue);
                    return $dt->format('Y-m-d');
                }
                // If it looks like a year (1900-2100), return Jan 1 of that year
                if ($numValue >= 1900 && $numValue <= 2100) {
                    return "$numValue-01-01";
                }
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
    public static function parseMoney($value)
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
}
