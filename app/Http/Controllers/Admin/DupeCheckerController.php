<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadDeduplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DupeCheckerController extends Controller
{
    protected $dedupeService;

    public function __construct(LeadDeduplicationService $dedupeService)
    {
        $this->dedupeService = $dedupeService;
    }

    /**
     * Show the dupe checker interface
     */
    public function index()
    {
        return view('admin.dupe-checker.index');
    }

    /**
     * Run self-check on all leads in the database
     */
    public function selfCheck(Request $request)
    {
        try {
            $checkBy = $request->input('check_by', 'phone'); // phone, ssn, account, or all

            $duplicates = [];

            // Check by phone number
            if ($checkBy === 'phone' || $checkBy === 'all') {
                $phonedupes = Lead::select('phone_number', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as lead_ids'))
                    ->whereNotNull('phone_number')
                    ->where('phone_number', '!=', '')
                    ->groupBy('phone_number')
                    ->having('count', '>', 1)
                    ->get();

                foreach ($phonedupes as $dupe) {
                    $duplicates[] = [
                        'type' => 'Phone Number',
                        'value' => $dupe->phone_number,
                        'count' => $dupe->count,
                        'lead_ids' => $dupe->lead_ids,
                    ];
                }
            }

            // Check by SSN
            if ($checkBy === 'ssn' || $checkBy === 'all') {
                $ssnDupes = Lead::select('ssn', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as lead_ids'))
                    ->whereNotNull('ssn')
                    ->where('ssn', '!=', '')
                    ->groupBy('ssn')
                    ->having('count', '>', 1)
                    ->get();

                foreach ($ssnDupes as $dupe) {
                    $duplicates[] = [
                        'type' => 'SSN',
                        'value' => $dupe->ssn,
                        'count' => $dupe->count,
                        'lead_ids' => $dupe->lead_ids,
                    ];
                }
            }

            // Check by account number
            if ($checkBy === 'account' || $checkBy === 'all') {
                $accountDupes = Lead::select('acc_number', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as lead_ids'))
                    ->whereNotNull('acc_number')
                    ->where('acc_number', '!=', '')
                    ->groupBy('acc_number')
                    ->having('count', '>', 1)
                    ->get();

                foreach ($accountDupes as $dupe) {
                    $duplicates[] = [
                        'type' => 'Account Number',
                        'value' => $dupe->acc_number,
                        'count' => $dupe->count,
                        'lead_ids' => $dupe->lead_ids,
                    ];
                }
            }

            // Export to CSV
            return $this->exportDuplicatesToCsv($duplicates, 'self_check_duplicates.csv');

        } catch (\Exception $e) {
            Log::error('Error in self-check: ' . $e->getMessage());
            return back()->with('error', 'Error running self-check: ' . $e->getMessage());
        }
    }

    /**
     * Compare two uploaded files
     */
    public function fileComparison(Request $request)
    {
        $request->validate([
            'file1' => 'required|file|mimes:xlsx,xls,csv',
            'file2' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            // Load file 1 (master file)
            $file1Path = $request->file('file1')->getRealPath();
            $file1Data = IOFactory::load($file1Path);
            $file1Sheet = $file1Data->getActiveSheet();
            $file1Rows = $file1Sheet->toArray();

            // Load file 2 (file to check)
            $file2Path = $request->file('file2')->getRealPath();
            $file2Data = IOFactory::load($file2Path);
            $file2Sheet = $file2Data->getActiveSheet();
            $file2Rows = $file2Sheet->toArray();

            // Extract phone numbers from file 1 (skip header)
            $file1Phones = [];
            foreach ($file1Rows as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                // Try to find phone number in the row (search all columns)
                foreach ($row as $cell) {
                    $phone = $this->normalizePhoneNumber($cell);
                    if ($phone) {
                        $file1Phones[$phone] = true;
                        break;
                    }
                }
            }

            // Check file 2 phones against file 1
            $results = [];
            foreach ($file2Rows as $index => $row) {
                if ($index === 0) {
                    // Add header row with status column
                    $results[] = array_merge($row, ['Status']);
                    continue;
                }

                $phone = null;
                foreach ($row as $cell) {
                    $phone = $this->normalizePhoneNumber($cell);
                    if ($phone) break;
                }

                if ($phone) {
                    $status = isset($file1Phones[$phone]) ? 'Duplicate' : 'Unique';
                } else {
                    $status = 'No Phone Found';
                }

                $results[] = array_merge($row, [$status]);
            }

            // Export results to CSV
            return $this->exportComparisonToCsv($results, 'file_comparison_results.csv');

        } catch (\Exception $e) {
            Log::error('Error in file comparison: ' . $e->getMessage());
            return back()->with('error', 'Error comparing files: ' . $e->getMessage());
        }
    }

    /**
     * Run automatic deduplication
     */
    public function runDeduplication()
    {
        try {
            $result = $this->dedupeService->deduplicateByPhone();

            return back()->with('success', "Deduplication completed! Found {$result['duplicates_found']} duplicate phone numbers. Merged {$result['leads_merged']} leads and removed {$result['leads_deleted']} duplicates.");
        } catch (\Exception $e) {
            Log::error('Error running deduplication: ' . $e->getMessage());
            return back()->with('error', 'Error running deduplication: ' . $e->getMessage());
        }
    }

    /**
     * Export duplicates to CSV
     */
    private function exportDuplicatesToCsv($duplicates, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Type');
        $sheet->setCellValue('B1', 'Value');
        $sheet->setCellValue('C1', 'Count');
        $sheet->setCellValue('D1', 'Lead IDs');
        $sheet->setCellValue('E1', 'Status');

        // Add data
        $row = 2;
        foreach ($duplicates as $duplicate) {
            $sheet->setCellValue('A' . $row, $duplicate['type']);
            $sheet->setCellValue('B' . $row, $duplicate['value']);
            $sheet->setCellValue('C' . $row, $duplicate['count']);
            $sheet->setCellValue('D' . $row, $duplicate['lead_ids']);
            $sheet->setCellValue('E' . $row, 'Duplicate');
            $row++;
        }

        // Create CSV writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        
        // Set headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export comparison results to CSV
     */
    private function exportComparisonToCsv($results, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add all rows
        $row = 1;
        foreach ($results as $resultRow) {
            $col = 'A';
            foreach ($resultRow as $cell) {
                $sheet->setCellValue($col . $row, $cell);
                $col++;
            }
            $row++;
        }

        // Create CSV writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        
        // Set headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Normalize phone number (extract digits only)
     */
    private function normalizePhoneNumber($value)
    {
        if (empty($value)) {
            return null;
        }

        // Remove all non-digit characters
        $digits = preg_replace('/[^0-9]/', '', (string)$value);

        // Handle 11-digit numbers starting with 1
        if (strlen($digits) === 11 && substr($digits, 0, 1) === '1') {
            $digits = substr($digits, 1);
        }

        // Valid 10-digit phone number
        if (strlen($digits) === 10) {
            return $digits;
        }

        return null;
    }
}
