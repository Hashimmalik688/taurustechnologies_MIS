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

        // If numeric, assume Excel serial date
        if (is_numeric($value)) {
            try {
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
