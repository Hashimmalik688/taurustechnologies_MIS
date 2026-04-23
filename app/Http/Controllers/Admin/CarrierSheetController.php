<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarrierSheetEntry;
use App\Models\CarrierSheetOpeningCb;
use App\Models\CarrierSheetRate;
use App\Services\CarrierSheetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CarrierSheetController extends Controller
{
    public function __construct(
        private CarrierSheetService $service
    ) {}

    /**
     * DASHBOARD  (D.B sheet — all carriers summary)
     * Optimized with aggressive caching.
     */
    public function dashboard(Request $request)
    {
        $periodMonth = $request->input('month');
        
        // Use aggressive caching for dashboard (15 minutes)
        $summary = Cache::remember(
            'carrier_sheet:dashboard:' . ($periodMonth ?? 'all'),
            900, // 15 minutes
            fn() => $this->service->getDashboardSummary($periodMonth, false) // useCache=false to avoid double caching
        );
        
        $months = Cache::remember(
            'carrier_sheet:available_months',
            1800, // 30 minutes
            fn() => $this->service->getAvailableMonths()
        );
        
        $carriers = Cache::remember(
            'carrier_sheet:active_carriers',
            3600, // 1 hour
            fn() => CarrierSheetRate::active()->ordered()->get()
        );

        return view('admin.reports.carrier-sheet.dashboard', [
            'rows'        => $summary['rows'],
            'totals'      => $summary['totals'],
            'months'      => $months,
            'carriers'    => $carriers,
            'periodMonth' => $periodMonth,
        ]);
    }

    /**
     * SHOW CARRIER SHEET  (single carrier — all entries)
     * Heavily optimized: pagination, column selection, pre-attached leads, aggressive caching.
     */
    public function show(CarrierSheetRate $rate, Request $request)
    {
        $periodMonth = $request->input('month');
        $perPage = $request->input('per_page', 50); // Default 50 entries per page
        
        // Build optimized query with minimal column selection
        $query = $rate->entries()
            ->withoutTrashed()
            ->select([
                'id', 'carrier_sheet_rate_id', 'sr_number', 'entry_date', 
                'policy_number', 'name', 'face_value', 'premium', 'policy_type',
                'status', 'draft_date', 'payment_date', 'commission', 
                'paid_amount', 'balance', 'chargeback_amount', 'period_month'
            ])
            ->with([
                'carrierRate:id,carrier_label,carrier_slug,title_color',
                'creator:id,name'
            ])  // Eager load with specific columns
            ->orderBy('sr_number');
            
        if ($periodMonth) {
            $parsed = \Carbon\Carbon::parse($periodMonth);
            $query->where(function ($q) use ($parsed) {
                $q->where(function ($q2) use ($parsed) {
                    $q2->whereNotNull('period_month')
                       ->whereYear('period_month', $parsed->year)
                       ->whereMonth('period_month', $parsed->month);
                })->orWhere(function ($q2) use ($parsed) {
                    $q2->whereNull('period_month')
                       ->whereYear('entry_date', $parsed->year)
                       ->whereMonth('entry_date', $parsed->month);
                });
            });
        }
        
        // Paginate instead of loading all at once
        $entries = $query->paginate($perPage)->withQueryString();
        
        // Batch preload leads and pre-attach to entries to avoid lazy loading in views
        $entriesCollection = $entries->getCollection();
        \App\Models\CarrierSheetEntry::preloadLeads($entriesCollection);
        
        // Pre-attach leads to each entry to avoid calling ->lead() in Blade
        $entriesCollection->each(function($entry) {
            $entry->cached_lead = $entry->lead();
        });

        // Aggressively cache summary calculations (they're expensive)
        $summary = Cache::remember(
            "carrier_sheet:summary:{$rate->id}:" . ($periodMonth ?? 'all'),
            900, // 15 minutes
            fn() => $this->service->getCarrierSummary($rate, $periodMonth, false)
        );
        
        $months = Cache::remember(
            "carrier_sheet:months:{$rate->id}",
            1800, // 30 minutes
            fn() => $this->service->getAvailableMonths($rate->id)
        );

        // Opening chargeback - cached separately
        if ($periodMonth) {
            $openingCb = CarrierSheetOpeningCb::firstOrCreate(
                ['carrier_sheet_rate_id' => $rate->id, 'period_month' => $periodMonth],
                ['amount' => 0, 'opening_balance' => 0]
            );
        } else {
            $openingCb = new CarrierSheetOpeningCb(['amount' => 0, 'opening_balance' => 0]);
        }

        // Cache daily summary aggressively (15 minutes)
        $dailySummary = \Cache::remember(
            "carrier_sheet:daily:{$rate->id}:" . ($periodMonth ?? 'all'),
            900,
            fn() => $this->service->getDailySummary($rate, $periodMonth)
        );

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

    /**
     * STORE ENTRY  (add new policy row)
     * Clears cache after saving.
     */
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
            ->when($validated['period_month'] ?? null, function ($q, $m) {
                $parsed = \Carbon\Carbon::parse($m);
                $q->whereYear('period_month', $parsed->year)
                  ->whereMonth('period_month', $parsed->month);
            })
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
        
        // Clear cache for this carrier 
        $this->service->clearCache($rate->id, $validated['period_month'] ?? null);

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

    /**
     * UPDATE ENTRY
     * Clears cache after update.
     */
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
        
        // Clear cache for this carrier
        $this->service->clearCache($entry->carrier_sheet_rate_id, $entry->period_month?->format('Y-m-01'));

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

    /**
     * DELETE ENTRY  (soft delete)
     * Clears cache after deletion.
     */
    public function deleteEntry(CarrierSheetEntry $entry, Request $request)
    {
        $rate = $entry->carrierRate;
        $periodMonth = $entry->period_month?->format('Y-m-01');
        
        $entry->delete();
        
        // Clear cache for this carrier
        $this->service->clearCache($rate->id, $periodMonth);

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $periodMonth);
            return response()->json(['success' => true, 'summary' => $summary]);
        }

        return back()->with('success', 'Entry deleted.');
    }

    /**
     * UPDATE OPENING CHARGEBACK  (Row 3)
     * Clears cache after update.
     */
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
        
        // Clear cache for this carrier
        $this->service->clearCache($rate->id, $validated['period_month']);

        if ($request->expectsJson()) {
            $summary = $this->service->getCarrierSummary($rate, $validated['period_month']);
            return response()->json(['success' => true, 'opening_cb' => $cb, 'summary' => $summary]);
        }

        return back()->with('success', 'Opening chargeback updated.');
    }

    /**
     * UPDATE OPENING BALANCE
     * Clears cache after update.
     */
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
        
        // Clear cache for this carrier
        $this->service->clearCache($rate->id, $validated['period_month']);

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

    public function storeCarrier(Request $request)
    {
        $validated = $request->validate([
            'carrier_label'  => 'required|string|max:80',
            'title_color'    => 'required|string|max:20',
            'level_rate'     => 'nullable|numeric|min:0|max:9.9999',
            'graded_rate'    => 'nullable|numeric|min:0|max:9.9999',
            'gi_rate'        => 'nullable|numeric|min:0|max:9.9999',
            'modified_rate'  => 'nullable|numeric|min:0|max:9.9999',
            'gi_multiplier'  => 'nullable|integer|in:1,9',
        ]);

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $validated['carrier_label']));
        $slug = trim($slug, '-');
        // ensure unique slug
        $base = $slug;
        $i = 2;
        while (CarrierSheetRate::where('carrier_slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $maxOrder = CarrierSheetRate::max('sort_order') ?? 0;

        $carrier = CarrierSheetRate::create([
            'carrier_label'  => $validated['carrier_label'],
            'carrier_slug'   => $slug,
            'partner_code'   => $slug,
            'title_color'    => $validated['title_color'],
            'level_rate'     => $validated['level_rate'] ?? null,
            'graded_rate'    => $validated['graded_rate'] ?? null,
            'gi_rate'        => $validated['gi_rate'] ?? null,
            'modified_rate'  => $validated['modified_rate'] ?? null,
            'gi_multiplier'  => $validated['gi_multiplier'] ?? 9,
            'is_active'      => true,
            'sort_order'     => $maxOrder + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'carrier' => $carrier]);
        }

        return back()->with('success', "Carrier '{$carrier->carrier_label}' created successfully.");
    }

    public function deleteCarrier(CarrierSheetRate $rate, Request $request)
    {
        $label = $rate->carrier_label;
        // Also delete all entries for this carrier
        $rate->entries()->forceDelete();
        $rate->openingChargebacks()->delete();
        $rate->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', "Carrier '{$label}' deleted.");
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
        
        // Cache is cleared inside recalculateAllEntries

        if ($request->expectsJson()) {
            return response()->json([
                'success'       => true,
                'recalculated'  => $recalculated,
                'rate'          => $rate->fresh(),
            ]);
        }

        return back()->with('success', "Rates updated. {$recalculated} entries recalculated.");
    }

    /**
     * LEAD LOOKUP (autocomplete for Add Entry modal)
     * Enhanced with partner information for auto-sheet-matching
     */
    public function leadLookup(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Filter for leads that have reached "Pending Contract" stage and beyond
        // (includes Issued, Not Issued, Ready for Draft, Sent to Draft, Paid, etc.)
        $leads = \App\Models\Lead::query()
            ->whereNotNull('pending_contract_at') // Reached Pending Contract stage
            ->whereNull('policy_died_at') // Not cancelled/died
            ->where(function ($query) use ($q) {
                $query->where('cn_name', 'like', "%{$q}%")
                      ->orWhere('policy_number', 'like', "%{$q}%");
            })
            ->whereNotNull('cn_name')
            ->with(['partner:id,name,code']) // Eager load partner
            ->select(['id', 'cn_name', 'policy_number', 'coverage_amount', 'monthly_premium', 'policy_type', 'initial_draft_date', 'future_draft_date', 'partner_id', 'carrier_name', 'pending_contract_at', 'issuance_status'])
            ->orderByDesc('pending_contract_at')
            ->limit(12)
            ->get()
            ->map(function ($lead) {
                $fv = null;
                if ($lead->coverage_amount) {
                    $amt = (float) $lead->coverage_amount;
                    $fv  = $amt >= 1000 ? round($amt / 1000) . 'K' : (string) $amt;
                }
                
                // Determine suggested carrier sheet based on partner code and carrier name
                $suggestedSheet = $this->matchLeadToCarrierSheet($lead);
                
                return [
                    'id'              => $lead->id,
                    'name'            => $lead->cn_name,
                    'policy_number'   => $lead->policy_number,
                    'face_value'      => $fv,
                    'premium'         => $lead->monthly_premium ? round((float) $lead->monthly_premium, 2) : null,
                    'policy_type'     => $lead->policy_type,
                    'draft_date'      => $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : null,
                    'payment_date'    => $lead->future_draft_date  ? \Carbon\Carbon::parse($lead->future_draft_date)->format('Y-m-d')  : null,
                    'partner_code'    => $lead->partner?->code,
                    'carrier_name'    => $lead->carrier_name,
                    'issuance_status' => $lead->issuance_status,
                    'suggested_sheet' => $suggestedSheet,
                ];
            });

        return response()->json($leads);
    }
    
    /**
     * Auto-match lead to carrier sheet based on partner code and carrier name.
     * Returns carrier_sheet_rate_id or null.
     */
    private function matchLeadToCarrierSheet($lead): ?int
    {
        $partnerCode = $lead->partner?->code;
        $carrierName = strtolower($lead->carrier_name ?? '');
        
        // Build matching logic: carrier abbreviation + partner code
        $carrierMap = [
            'transamerica'      => 'ta',
            'ta'                => 'ta',
            't.a'               => 'ta',
            'aig'               => 'aig',
            'american general'  => 'aig',
            'amam'              => 'amam',
            'securian'          => 'sec',
            'sec'               => 'sec',
            'royal arcanum'     => 'ra',
            'r.a'               => 'ra',
            'aetna'             => 'aetna',
            'mutual of omaha'   => 'moo',
            'moo'               => 'moo',
        ];
        
        $carrierSlug = null;
        foreach ($carrierMap as $needle => $slug) {
            if (str_contains($carrierName, $needle)) {
                $carrierSlug = $slug;
                break;
            }
        }
        
        if (!$carrierSlug || !$partnerCode) {
            return null;
        }
        
        // Try to find matching carrier_sheet_rate
        $slug = $carrierSlug . '-' . strtolower($partnerCode);
        $rate = CarrierSheetRate::where('carrier_slug', $slug)->first();
        
        return $rate?->id;
    }

    /**
     * IMPORT (.xlsx — multi-sheet)
     * Optimized with batch inserts and cache clearing.
     */
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
            $batchSize = 100; // Process in batches to reduce memory usage
            $batch = [];

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
                $batch[] = $entry;
                $imported++;
                
                // Save in batches
                if (count($batch) >= $batchSize) {
                    foreach ($batch as $e) {
                        $e->save();
                    }
                    $batch = [];
                }
            }
            
            // Save remaining entries
            foreach ($batch as $e) {
                $e->save();
            }
            
            // Clear cache for this carrier
            $this->service->clearCache($rate->id);

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
            $parsed = \Carbon\Carbon::parse($periodMonth);
            $query->where(function ($q) use ($parsed) {
                $q->where(function ($q2) use ($parsed) {
                    $q2->whereNotNull('period_month')
                       ->whereYear('period_month', $parsed->year)
                       ->whereMonth('period_month', $parsed->month);
                })->orWhere(function ($q2) use ($parsed) {
                    $q2->whereNull('period_month')
                       ->whereYear('entry_date', $parsed->year)
                       ->whereMonth('entry_date', $parsed->month);
                });
            });
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
