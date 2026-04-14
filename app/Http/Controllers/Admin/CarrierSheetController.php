<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarrierSheetEntry;
use App\Models\CarrierSheetOpeningCb;
use App\Models\CarrierSheetRate;
use App\Services\CarrierSheetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CarrierSheetController extends Controller
{
    public function __construct(
        private CarrierSheetService $service
    ) {}

    /* ================================================================
     *  DASHBOARD  (D.B sheet — all carriers summary)
     * ================================================================ */
    public function dashboard(Request $request)
    {
        $periodMonth = $request->input('month');
        $summary = $this->service->getDashboardSummary($periodMonth);
        $months = $this->service->getAvailableMonths();
        $carriers = CarrierSheetRate::active()->ordered()->get();

        return view('admin.reports.carrier-sheet.dashboard', [
            'rows'        => $summary['rows'],
            'totals'      => $summary['totals'],
            'months'      => $months,
            'carriers'    => $carriers,
            'periodMonth' => $periodMonth,
        ]);
    }

    /* ================================================================
     *  SHOW CARRIER SHEET  (single carrier — all entries)
     * ================================================================ */
    public function show(CarrierSheetRate $rate, Request $request)
    {
        $periodMonth = $request->input('month');
        $query = $rate->entries()->withoutTrashed()->orderBy('sr_number');
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        $entries = $query->get();

        $summary = $this->service->getCarrierSummary($rate, $periodMonth);
        $months = $this->service->getAvailableMonths($rate->id);

        // Opening chargeback for this carrier/month (always load; period=null returns a default stub)
        if ($periodMonth) {
            $openingCb = CarrierSheetOpeningCb::firstOrCreate(
                ['carrier_sheet_rate_id' => $rate->id, 'period_month' => $periodMonth],
                ['amount' => 0, 'opening_balance' => 0]
            );
        } else {
            // Stub object so the view always renders the fields
            $openingCb = new CarrierSheetOpeningCb(['amount' => 0, 'opening_balance' => 0]);
        }

        $dailySummary = $this->service->getDailySummary($rate, $periodMonth);

        return view('admin.reports.carrier-sheet.show', [
            'rate'         => $rate,
            'entries'      => $entries,
            'summary'      => $summary,
            'months'       => $months,
            'periodMonth'  => $periodMonth,
            'openingCb'    => $openingCb,
            'dailySummary' => $dailySummary,
        ]);
    }

    /* ================================================================
     *  STORE ENTRY  (add new policy row)
     * ================================================================ */
    public function storeEntry(CarrierSheetRate $rate, Request $request)
    {
        $validated = $request->validate([
            'entry_date'        => 'nullable|date',
            'policy_number'     => 'nullable|string|max:60',
            'name'              => 'nullable|string|max:120',
            'face_value'        => 'nullable|string|max:20',
            'premium'           => 'nullable|numeric|min:0',
            'policy_type'       => 'nullable|string|max:30',
            'status'            => 'required|string|in:approved,paid,chargeback,declined',
            'draft_date'        => 'nullable|date',
            'payment_date'      => 'nullable|date',
            'paid_amount'       => 'nullable|numeric|min:0',
            'chargeback_amount' => 'nullable|numeric|min:0',
            'rate_override'     => 'nullable|numeric',
            'notes'             => 'nullable|string|max:500',
            'period_month'      => 'nullable|date',
        ]);

        // Auto-assign SR number
        $maxSr = $rate->entries()->withoutTrashed()
            ->when($validated['period_month'] ?? null, fn ($q, $m) => $q->where('period_month', $m))
            ->max('sr_number');
        $validated['sr_number'] = ($maxSr ?? 0) + 1;
        $validated['carrier_sheet_rate_id'] = $rate->id;
        $validated['created_by'] = auth()->id();
        $validated['premium'] = $validated['premium'] ?? 0;
        $validated['paid_amount'] = round(($validated['paid_amount'] ?? 0) / 2, 2);
        $validated['chargeback_amount'] = $validated['chargeback_amount'] ?? 0;

        $entry = new CarrierSheetEntry($validated);
        $this->service->recalculateEntry($entry);
        $entry->save();

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $validated['period_month'] ?? null);
            return response()->json([
                'success' => true,
                'entry'   => $entry->fresh(),
                'summary' => $summary,
            ]);
        }

        return back()->with('success', 'Entry added successfully.');
    }

    /* ================================================================
     *  UPDATE ENTRY
     * ================================================================ */
    public function updateEntry(CarrierSheetEntry $entry, Request $request)
    {
        $validated = $request->validate([
            'entry_date'        => 'nullable|date',
            'policy_number'     => 'nullable|string|max:60',
            'name'              => 'nullable|string|max:120',
            'face_value'        => 'nullable|string|max:20',
            'premium'           => 'nullable|numeric|min:0',
            'policy_type'       => 'nullable|string|max:30',
            'status'            => 'nullable|string|in:approved,paid,chargeback,declined',
            'draft_date'        => 'nullable|date',
            'payment_date'      => 'nullable|date',
            'paid_amount'       => 'nullable|numeric|min:0',
            'chargeback_amount' => 'nullable|numeric|min:0',
            'rate_override'     => 'nullable|numeric',
            'commission'        => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        $commissionOverride = array_key_exists('commission', $validated) && $validated['commission'] !== null
            ? (float) $validated['commission']
            : null;
        unset($validated['commission']); // don't let fill() overwrite yet

        $entry->fill($validated);

        if ($commissionOverride !== null) {
            // User forced commission — just recalculate balance with the given commission
            $entry->commission = round($commissionOverride, 2);
            $entry->balance = $this->service->calculateBalance(
                $entry->status,
                $entry->commission,
                (float) $entry->paid_amount,
                (float) $entry->chargeback_amount
            );
        } else {
            $this->service->recalculateEntry($entry);
        }
        $entry->save();

        if ($request->expectsJson()) {
            $rate = $entry->carrierRate;
            $summary = $this->service->getCarrierSummary($rate, $entry->period_month?->format('Y-m-01'));
            return response()->json([
                'success' => true,
                'entry'   => $entry->fresh(),
                'summary' => $summary,
            ]);
        }

        return back()->with('success', 'Entry updated.');
    }

    /* ================================================================
     *  DELETE ENTRY  (soft delete)
     * ================================================================ */
    public function deleteEntry(CarrierSheetEntry $entry, Request $request)
    {
        $rate = $entry->carrierRate;
        $periodMonth = $entry->period_month?->format('Y-m-01');
        $entry->delete();

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $periodMonth);
            return response()->json(['success' => true, 'summary' => $summary]);
        }

        return back()->with('success', 'Entry deleted.');
    }

    /* ================================================================
     *  UPDATE OPENING CHARGEBACK  (Row 3)
     * ================================================================ */
    public function updateOpeningChargeback(CarrierSheetRate $rate, Request $request)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0',
            'period_month' => 'required|date',
        ]);

        $cb = CarrierSheetOpeningCb::updateOrCreate(
            ['carrier_sheet_rate_id' => $rate->id, 'period_month' => $validated['period_month']],
            ['amount' => $validated['amount']]
        );

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $validated['period_month']);
            return response()->json(['success' => true, 'opening_cb' => $cb, 'summary' => $summary]);
        }

        return back()->with('success', 'Opening chargeback updated.');
    }

    /* ================================================================
     *  UPDATE OPENING BALANCE
     * ================================================================ */
    public function updateOpeningBalance(CarrierSheetRate $rate, Request $request)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric',
            'period_month'    => 'required|date',
        ]);

        $cb = CarrierSheetOpeningCb::updateOrCreate(
            ['carrier_sheet_rate_id' => $rate->id, 'period_month' => $validated['period_month']],
            ['opening_balance' => $validated['opening_balance']]
        );

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $validated['period_month']);
            return response()->json(['success' => true, 'record' => $cb, 'summary' => $summary]);
        }

        return back()->with('success', 'Opening balance updated.');
    }

    /* ================================================================
     *  RATES MANAGEMENT
     * ================================================================ */
    public function rates()
    {
        $carriers = CarrierSheetRate::ordered()->get();
        return view('admin.reports.carrier-sheet.rates', compact('carriers'));
    }

    public function updateRate(CarrierSheetRate $rate, Request $request)
    {
        $validated = $request->validate([
            'level_rate'    => 'nullable|numeric|min:0|max:9.9999',
            'graded_rate'   => 'nullable|numeric|min:0|max:9.9999',
            'gi_rate'       => 'nullable|numeric|min:0|max:9.9999',
            'modified_rate' => 'nullable|numeric|min:0|max:9.9999',
            'gi_multiplier' => 'nullable|integer|in:1,9',
        ]);

        $rate->update($validated);

        // Recalculate all entries for this carrier
        $recalculated = $this->service->recalculateAllEntries($rate);

        if ($request->expectsJson()) {
            return response()->json([
                'success'       => true,
                'recalculated'  => $recalculated,
                'rate'          => $rate->fresh(),
            ]);
        }

        return back()->with('success', "Rates updated. {$recalculated} entries recalculated.");
    }

    /* ================================================================
     *  LEAD LOOKUP  (autocomplete for Add Entry modal)
     * ================================================================ */
    public function leadLookup(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $leads = \App\Models\Lead::query()
            ->where(function ($query) use ($q) {
                $query->where('cn_name', 'like', "%{$q}%")
                      ->orWhere('policy_number', 'like', "%{$q}%");
            })
            ->whereNotNull('cn_name')
            ->whereNotNull('sale_at')
            ->select(['id', 'cn_name', 'policy_number', 'coverage_amount', 'monthly_premium', 'policy_type', 'initial_draft_date', 'future_draft_date'])
            ->orderBy('cn_name')
            ->limit(12)
            ->get()
            ->map(function ($lead) {
                $fv = null;
                if ($lead->coverage_amount) {
                    $amt = (float) $lead->coverage_amount;
                    $fv  = $amt >= 1000 ? round($amt / 1000) . 'K' : (string) $amt;
                }
                return [
                    'id'            => $lead->id,
                    'name'          => $lead->cn_name,
                    'policy_number' => $lead->policy_number,
                    'face_value'    => $fv,
                    'premium'       => $lead->monthly_premium ? round((float) $lead->monthly_premium, 2) : null,
                    'policy_type'   => $lead->policy_type,
                    'draft_date'    => $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : null,
                    'payment_date'  => $lead->future_draft_date  ? \Carbon\Carbon::parse($lead->future_draft_date)->format('Y-m-d')  : null,
                ];
            });

        return response()->json($leads);
    }

    /* ================================================================
     *  IMPORT (.xlsx — multi-sheet)
     * ================================================================ */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');

        // Map sheet names → carrier slugs
        $sheetMap = [
            'T.A F-1'   => 'ta-f1',
            'T.A Y-1'   => 'ta-y1',
            'AIG Y-1'   => 'aig-y1',
            'AIG E-1'   => 'aig-e1',
            'AMAM Y-1'  => 'amam-y1',
            'SEC F-1'   => 'sec-f1',
            'R.A F-1'   => 'ra-f1',
            'AETNA Y-1' => 'aetna-y1',
            // Alternate names from the Excel tabs
            'TA F-1'    => 'ta-f1',
            'TA Y-1'    => 'ta-y1',
        ];

        // Preload carrier rates keyed by slug
        $carriers = CarrierSheetRate::all()->keyBy('carrier_slug');

        $spreadsheet = IOFactory::load($file->getPathname());
        $results = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $trimmed = trim($sheetName);

            // Skip non-carrier sheets
            if (in_array(strtoupper($trimmed), ['RATES', 'D.B', 'DB', 'DASHBOARD'])) {
                continue;
            }

            $slug = $sheetMap[$trimmed] ?? $sheetMap[strtoupper($trimmed)] ?? null;
            if (!$slug || !isset($carriers[$slug])) {
                $results[] = ['sheet' => $sheetName, 'status' => 'skipped', 'reason' => 'No matching carrier'];
                continue;
            }

            $rate = $carriers[$slug];
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $highestRow = $sheet->getHighestRow();

            // Delete all existing entries for this carrier before re-importing to avoid duplicates.
            CarrierSheetEntry::where('carrier_sheet_rate_id', $rate->id)->forceDelete();

            // Opening chargeback (row 3, col N) is skipped during multi-month import.
            // Users can set it per-period manually from the carrier sheet view.

            $imported = 0;
            $skipped = 0;

            for ($row = 4; $row <= $highestRow; $row++) {
                // Read columns A–N — use getCalculatedValue() so formula cells return their result
                $srNum       = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $dateVal     = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $policyNum   = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $nameVal     = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                $fvVal       = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                $premVal     = $sheet->getCellByColumnAndRow(6, $row)->getCalculatedValue();
                $policyType  = $sheet->getCellByColumnAndRow(7, $row)->getValue();
                $statusVal   = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                $draftDate   = $sheet->getCellByColumnAndRow(9, $row)->getValue();
                $paymentDate = $sheet->getCellByColumnAndRow(10, $row)->getValue();
                // Column K (11) = Commission — skip (server-calculated)
                $paidVal     = $sheet->getCellByColumnAndRow(12, $row)->getCalculatedValue();
                // Column M (13) = Balance — skip (server-calculated)
                $cbVal       = $sheet->getCellByColumnAndRow(14, $row)->getCalculatedValue();

                // Skip empty rows
                if (!$srNum && !$policyNum && !$nameVal) {
                    $skipped++;
                    continue;
                }

                $entry = new CarrierSheetEntry([
                    'carrier_sheet_rate_id' => $rate->id,
                    'sr_number'             => is_numeric($srNum) ? (int) $srNum : null,
                    'entry_date'            => $this->parseDate($dateVal),
                    'policy_number'         => $policyNum ? trim((string) $policyNum) : null,
                    'name'                  => $nameVal ? trim((string) $nameVal) : null,
                    'face_value'            => $fvVal ? trim((string) $fvVal) : null,
                    'premium'               => is_numeric($premVal) ? round((float) $premVal, 2) : 0,
                    'policy_type'           => $this->normalizePolicyType($policyType),
                    'status'                => $this->normalizeStatus($statusVal),
                    'draft_date'            => $this->parseDate($draftDate),
                    'payment_date'          => $this->parseDate($paymentDate),
                    'paid_amount'           => is_numeric($paidVal) ? round((float) $paidVal, 2) : 0,
                    'chargeback_amount'     => is_numeric($cbVal) ? round(abs((float) $cbVal), 2) : 0,
                    'period_month'          => $this->derivePeriodMonth($dateVal),
                    'created_by'            => auth()->id(),
                ]);

                $this->service->recalculateEntry($entry);
                $entry->save();
                $imported++;
            }

            $results[] = [
                'sheet'    => $sheetName,
                'carrier'  => $rate->carrier_label,
                'status'   => 'imported',
                'imported' => $imported,
                'skipped'  => $skipped,
            ];
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'results' => $results]);
        }

        return redirect()
            ->route('settings.reports.carrier-sheet.dashboard')
            ->with('success', 'Import completed.')
            ->with('import_results', $results);
    }

    /* ================================================================
     *  EXPORT (CSV for one carrier sheet)
     * ================================================================ */
    public function export(CarrierSheetRate $rate, Request $request)
    {
        $periodMonth = $request->input('month');
        $query = $rate->entries()->withoutTrashed()->orderBy('sr_number');
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        $entries = $query->get();

        $filename = str_replace(' ', '_', $rate->carrier_label) . '_' . ($periodMonth ?: 'all') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($entries) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['SR#', 'Date', 'Policy#', 'Name', 'FV', 'Premium', 'Policy Type', 'Status', 'Draft Date', 'Payment Date', 'Commission', 'Paid', 'Balance', 'Chargeback']);
            foreach ($entries as $e) {
                fputcsv($fh, [
                    $e->sr_number,
                    $e->entry_date?->format('d-M-y'),
                    $e->policy_number,
                    $e->name,
                    $e->face_value,
                    $e->premium,
                    $e->policy_type,
                    $e->status,
                    $e->draft_date?->format('d-M-y'),
                    $e->payment_date?->format('d-M-y'),
                    $e->commission,
                    $e->paid_amount,
                    $e->balance,
                    $e->chargeback_amount,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ── Private helpers ──────────────────────────────── */

    private function parseDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        // Numeric Excel serial date
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // String date
        try {
            return Carbon::parse(trim((string) $value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /** Derive first-of-month from an entry date value (string or Excel serial). Falls back to current month. */
    private function derivePeriodMonth($dateVal): string
    {
        $parsed = $this->parseDate($dateVal);
        if ($parsed) {
            return Carbon::parse($parsed)->startOfMonth()->format('Y-m-d');
        }
        return Carbon::now()->startOfMonth()->format('Y-m-d');
    }

    private function normalizePolicyType($value): ?string
    {
        if (!$value) {
            return null;
        }
        $v = strtolower(trim((string) $value));
        $map = [
            'level'           => 'level',
            'graded'          => 'graded',
            'gi'              => 'gi',
            'modified'        => 'modified',
            'preferred'       => 'preferred',
            'standard'        => 'standard',
            'super preferred' => 'super_preferred',
        ];
        return $map[$v] ?? $v;
    }

    private function normalizeStatus($value): string
    {
        if (!$value) {
            return 'approved';
        }
        $v = strtolower(trim((string) $value));
        $map = [
            'approved'   => 'approved',
            'paid'       => 'paid',
            'chargeback' => 'chargeback',
            'declined'   => 'declined',
            'cancelled'  => 'declined',   // legacy mapping
        ];
        return $map[$v] ?? 'approved';
    }
}
