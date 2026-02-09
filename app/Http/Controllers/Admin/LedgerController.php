<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerEntryRequest;
use App\Models\LedgerEntry;
use App\Models\Vendor;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
    /**
     * Display a listing of ledger entries.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $entries = LedgerEntry::with(['vendor', 'user', 'lead'])
                ->select('ledger_entries.*');

            // Apply filters
            if ($request->has('vendor_id') && $request->vendor_id) {
                $entries->where('vendor_id', $request->vendor_id);
            }

            if ($request->has('type') && $request->type) {
                $entries->where('type', $request->type);
            }

            if ($request->has('category') && $request->category) {
                $entries->where('category', $request->category);
            }

            if ($request->has('start_date') && $request->start_date) {
                $entries->where('transaction_date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $entries->where('transaction_date', '<=', $request->end_date);
            }

            return DataTables::of($entries)
                ->addIndexColumn()
                ->addColumn('vendor_name', function ($entry) {
                    return $entry->vendor->name ?? 'N/A';
                })
                ->addColumn('recorded_by', function ($entry) {
                    return $entry->user->name ?? 'N/A';
                })
                ->addColumn('type_badge', function ($entry) {
                    $badges = [
                        'debit' => '<span class="badge bg-danger">Debit</span>',
                        'credit' => '<span class="badge bg-success">Credit</span>',
                    ];
                    return $badges[$entry->type] ?? '';
                })
                ->addColumn('formatted_amount', function ($entry) {
                    $sign = $entry->type === 'debit' ? '-' : '+';
                    return $sign . '$' . number_format($entry->amount, 2);
                })
                ->addColumn('formatted_date', function ($entry) {
                    return $entry->transaction_date->format('M d, Y');
                })
                ->addColumn('action', function ($entry) {
                    return '<a href="' . route('ledger.show', $entry->id) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['type_badge', 'action'])
                ->make(true);
        }

        $vendors = Vendor::active()->orderBy('name')->get();
        return view('admin.ledger.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new ledger entry.
     */
    public function create()
    {
        $vendors = Vendor::active()->orderBy('name')->get();
        $leads = Lead::orderBy('first_name')->get();
        return view('admin.ledger.create', compact('vendors', 'leads'));
    }

    /**
     * Store a newly created ledger entry in storage.
     */
    public function store(StoreLedgerEntryRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $entry = LedgerEntry::create($data);

        return redirect()
            ->route('ledger.index')
            ->with('success', 'Ledger entry created successfully.');
    }

    /**
     * Display the specified ledger entry.
     */
    public function show($id)
    {
        $entry = LedgerEntry::with(['vendor', 'user', 'lead'])->findOrFail($id);
        return view('admin.ledger.show', compact('entry'));
    }

    /**
     * Display ledger entries for a specific vendor.
     */
    public function vendorLedger(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        if ($request->ajax()) {
            $entries = LedgerEntry::with(['user', 'lead'])
                ->where('vendor_id', $vendorId)
                ->select('ledger_entries.*')
                ->orderBy('transaction_date', 'desc');

            return DataTables::of($entries)
                ->addIndexColumn()
                ->addColumn('recorded_by', function ($entry) {
                    return $entry->user->name ?? 'N/A';
                })
                ->addColumn('type_badge', function ($entry) {
                    $badges = [
                        'debit' => '<span class="badge bg-danger">Debit</span>',
                        'credit' => '<span class="badge bg-success">Credit</span>',
                    ];
                    return $badges[$entry->type] ?? '';
                })
                ->addColumn('formatted_amount', function ($entry) {
                    $sign = $entry->type === 'debit' ? '-' : '+';
                    return $sign . '$' . number_format($entry->amount, 2);
                })
                ->addColumn('formatted_date', function ($entry) {
                    return $entry->transaction_date->format('M d, Y');
                })
                ->addColumn('action', function ($entry) {
                    return '<a href="' . route('ledger.show', $entry->id) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['type_badge', 'action'])
                ->make(true);
        }

        $summary = [
            'total_credits' => $vendor->ledgerEntries()->where('type', 'credit')->sum('amount'),
            'total_debits' => $vendor->ledgerEntries()->where('type', 'debit')->sum('amount'),
            'balance' => $vendor->balance,
            'entry_count' => $vendor->ledgerEntries()->count(),
        ];

        return view('admin.ledger.vendor', compact('vendor', 'summary'));
    }

    /**
     * Export ledger entries to CSV or PDF.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');

        $entries = LedgerEntry::with(['vendor', 'user', 'lead']);

        // Apply filters
        if ($request->has('vendor_id') && $request->vendor_id) {
            $entries->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $entries->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $entries->where('transaction_date', '<=', $request->end_date);
        }

        $entries = $entries->orderBy('transaction_date', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.ledger.export-pdf', compact('entries'));
            return $pdf->download('ledger-entries-' . date('Y-m-d') . '.pdf');
        }

        // CSV Export
        $filename = 'ledger-entries-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($entries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Vendor', 'Type', 'Amount', 'Category', 'Reference', 'Description', 'Recorded By']);

            foreach ($entries as $entry) {
                fputcsv($file, [
                    $entry->transaction_date->format('Y-m-d'),
                    $entry->vendor->name ?? 'N/A',
                    ucfirst($entry->type),
                    number_format($entry->amount, 2),
                    $entry->category ?? 'N/A',
                    $entry->reference_number ?? 'N/A',
                    $entry->description,
                    $entry->user->name ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display financial summary.
     */
    public function summary(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $summary = [
            'total_credits' => LedgerEntry::credits()
                ->dateRange($startDate, $endDate)
                ->sum('amount'),
            'total_debits' => LedgerEntry::debits()
                ->dateRange($startDate, $endDate)
                ->sum('amount'),
            'entry_count' => LedgerEntry::dateRange($startDate, $endDate)->count(),
        ];

        $summary['net_balance'] = $summary['total_credits'] - $summary['total_debits'];

        // Category breakdown
        $categoryBreakdown = LedgerEntry::dateRange($startDate, $endDate)
            ->selectRaw('category, type, SUM(amount) as total')
            ->groupBy('category', 'type')
            ->get()
            ->groupBy('category');

        // Vendor breakdown
        $vendorBreakdown = LedgerEntry::with('vendor')
            ->dateRange($startDate, $endDate)
            ->selectRaw('vendor_id, type, SUM(amount) as total')
            ->groupBy('vendor_id', 'type')
            ->get()
            ->groupBy('vendor_id');

        return view('admin.ledger.summary', compact('summary', 'categoryBreakdown', 'vendorBreakdown', 'startDate', 'endDate'));
    }

    /**
     * Display petty cash ledger
     */
    public function pettyCashIndex(Request $request)
    {
        // Get all entries in chronological order for balance calculations and serial numbers
        $allEntries = \App\Models\PettyCashLedger::orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
        // Calculate running balances and serial numbers
        $runningBalance = 0;
        $balanceMap = [];
        $serialNumberMap = [];
        
        foreach ($allEntries as $index => $entry) {
            $runningBalance += $entry->debit - $entry->credit;
            $balanceMap[$entry->id] = $runningBalance;
            $serialNumberMap[$entry->id] = $index + 1;  // Serial number starts from 1
        }

        // Get unique heads/categories
        $heads = \App\Models\PettyCashLedger::distinct()->pluck('head')->sort()->values();

        // Get filter parameters
        $selectedHead = $request->input('head', '');
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');
        
        $categoryTotal = 0;
        $categoryMonthTotal = 0;

        // Build base query
        $query = \App\Models\PettyCashLedger::query();

        // Apply date filters
        if ($fromDate) {
            $query->where('date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('date', '<=', $toDate);
        }

        // Filter by head/category if provided
        if ($selectedHead) {
            $query->where('head', $selectedHead);
        }

        // Get filtered entries sorted by date descending (newest first for display)
        $entries = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        // Calculate category totals
        if ($selectedHead) {
            $categoryQuery = \App\Models\PettyCashLedger::where('head', $selectedHead);
            
            // All-time total
            $categoryTotal = $categoryQuery->sum('credit');
            
            // For the selected date range or current month
            $categoryMonthQuery = \App\Models\PettyCashLedger::where('head', $selectedHead);
            if ($fromDate && $toDate) {
                $categoryMonthQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $categoryMonthQuery->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
            }
            $categoryMonthTotal = $categoryMonthQuery->sum('credit');
        }

        return view('admin.finance.petty-cash', compact('entries', 'balanceMap', 'serialNumberMap', 'heads', 'selectedHead', 'categoryTotal', 'categoryMonthTotal', 'fromDate', 'toDate'));
    }

    /**
     * Print petty cash ledger in general ledger format
     */
    public function pettyCashPrint(Request $request)
    {
        // Get all entries in chronological order for balance calculations
        $allEntries = \App\Models\PettyCashLedger::orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
        // Calculate running balances
        $runningBalance = 0;
        $balanceMap = [];
        
        foreach ($allEntries as $entry) {
            $runningBalance += $entry->debit - $entry->credit;
            $balanceMap[$entry->id] = $runningBalance;
        }

        // Get unique heads/categories
        $heads = \App\Models\PettyCashLedger::distinct()->pluck('head')->sort()->values();

        // Get filter parameters
        $selectedHead = $request->input('head', '');
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');
        
        $categoryTotal = 0;
        $categoryMonthTotal = 0;

        // Build base query
        $query = \App\Models\PettyCashLedger::query();

        // Apply date filters
        if ($fromDate) {
            $query->where('date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('date', '<=', $toDate);
        }

        // Filter by head/category if provided
        if ($selectedHead) {
            $query->where('head', $selectedHead);
        }

        // Get filtered entries
        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        // Calculate category totals
        if ($selectedHead) {
            $categoryQuery = \App\Models\PettyCashLedger::where('head', $selectedHead);
            
            // All-time total
            $categoryTotal = $categoryQuery->sum('credit');
            
            // For the selected date range or current month
            $categoryMonthQuery = \App\Models\PettyCashLedger::where('head', $selectedHead);
            if ($fromDate && $toDate) {
                $categoryMonthQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $categoryMonthQuery->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
            }
            $categoryMonthTotal = $categoryMonthQuery->sum('credit');
        }

        return view('admin.finance.petty-cash-print', compact('entries', 'balanceMap', 'heads', 'selectedHead', 'categoryTotal', 'categoryMonthTotal', 'fromDate', 'toDate'));
    }

    /**
     * Export petty cash ledger to CSV
     */
    public function pettyCashExport(Request $request)
    {
        // Get filter parameters
        $selectedHead = $request->input('head', '');
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');

        // Build base query
        $query = \App\Models\PettyCashLedger::query();

        // Apply date filters
        if ($fromDate) {
            $query->where('date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('date', '<=', $toDate);
        }

        // Filter by head/category if provided
        if ($selectedHead) {
            $query->where('head', $selectedHead);
        }

        // Get filtered entries
        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        // Calculate running balances
        $runningBalance = 0;
        $csvData = [];
        
        // Add headers
        $csvData[] = ['TAURUS TECHNOLOGIES - PETTY CASH LEDGER'];
        $csvData[] = [];
        $csvData[] = ['Report Date', date('m-d-Y')];
        $csvData[] = ['User', auth()->user()->name];
        
        if ($selectedHead) {
            $csvData[] = ['Category', $selectedHead];
        }
        
        if ($fromDate && $toDate) {
            $csvData[] = ['Date Range', date('M d, Y', strtotime($fromDate)) . ' - ' . date('M d, Y', strtotime($toDate))];
        }
        
        $csvData[] = [];
        $csvData[] = ['G/L No.', 'Date', 'Head', 'Description', 'Debit', 'Credit', 'Balance'];
        
        // Add data rows
        foreach ($entries as $entry) {
            $runningBalance += $entry->debit - $entry->credit;
            $csvData[] = [
                $entry->serial_number,
                $entry->date->format('m/d/Y'),
                $entry->head,
                $entry->description,
                $entry->debit > 0 ? number_format($entry->debit, 2) : '',
                $entry->credit > 0 ? number_format($entry->credit, 2) : '',
                number_format($runningBalance, 2),
            ];
        }
        
        // Add totals row
        $csvData[] = [];
        $csvData[] = ['TOTALS', '', '', '', number_format($entries->sum('debit'), 2), number_format($entries->sum('credit'), 2), number_format($runningBalance, 2)];
        
        // Create CSV response
        $filename = 'petty-cash-ledger-' . date('Y-m-d-His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Get petty cash entry for editing
     */
    public function pettyCashEdit($id)
    {
        $entry = \App\Models\PettyCashLedger::findOrFail($id);
        return response()->json([
            'id' => $entry->id,
            'date' => $entry->date->format('Y-m-d'),
            'description' => $entry->description,
            'head' => $entry->head,
            'debit' => $entry->debit,
            'credit' => $entry->credit,
        ]);
    }

    /**
     * Store new petty cash entry
     */
    public function pettyCashStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'head' => 'required|string|max:100',
                'debit' => 'nullable|numeric|min:0',
                'credit' => 'nullable|numeric|min:0',
            ]);

            $debit = $request->debit ?? 0;
            $credit = $request->credit ?? 0;

            // Calculate the next serial number (excluding soft-deleted records)
            $maxSerial = \App\Models\PettyCashLedger::withoutTrashed()->max('serial_number');
            $nextSerialNumber = ($maxSerial ?? 0) + 1;

            // Create new entry
            \App\Models\PettyCashLedger::create([
                'serial_number' => $nextSerialNumber,
                'date' => $request->date,
                'description' => $request->description,
                'head' => $request->head,
                'debit' => $debit,
                'credit' => $credit,
            ]);

            return redirect()->route('petty-cash.index')->with('success', 'Entry created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating entry: ' . $e->getMessage());
        }
    }

    /**
     * Update existing petty cash entry
     */
    public function pettyCashUpdate(Request $request, $id)
    {
        try {
            $entry = \App\Models\PettyCashLedger::findOrFail($id);

            $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'head' => 'required|string|max:100',
                'debit' => 'nullable|numeric|min:0',
                'credit' => 'nullable|numeric|min:0',
            ]);

            $debit = $request->debit ?? 0;
            $credit = $request->credit ?? 0;

            // Recalculate balance for this entry and all subsequent entries
            $oldDebit = $entry->debit;
            $oldCredit = $entry->credit;
            $difference = ($debit - $credit) - ($oldDebit - $oldCredit);

            $entry->update([
                'date' => $request->date,
                'description' => $request->description,
                'head' => $request->head,
                'debit' => $debit,
                'credit' => $credit,
            ]);

            // Update balance for all entries after this one
            $laterEntries = \App\Models\PettyCashLedger::where('id', '>', $id)->orderBy('id', 'asc')->get();
            foreach ($laterEntries as $laterEntry) {
                $laterEntry->update(['balance' => $laterEntry->balance + $difference]);
            }

            return redirect()->route('petty-cash.index')->with('success', 'Entry updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating entry: ' . $e->getMessage());
        }
    }

    /**
     * Delete petty cash entry
     */
    public function pettyCashDestroy($id)
    {
        try {
            $entry = \App\Models\PettyCashLedger::findOrFail($id);
            $difference = $entry->debit - $entry->credit;

            // Update balance for all entries after this one
            $laterEntries = \App\Models\PettyCashLedger::where('id', '>', $id)->orderBy('id', 'asc')->get();
            foreach ($laterEntries as $laterEntry) {
                $laterEntry->update(['balance' => $laterEntry->balance - $difference]);
            }

            $entry->delete();

            return redirect()->route('petty-cash.index')->with('success', 'Entry deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting entry: ' . $e->getMessage());
        }
    }
}
