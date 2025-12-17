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
}
