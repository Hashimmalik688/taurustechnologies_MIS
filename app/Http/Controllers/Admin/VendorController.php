<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vendors = Vendor::with('ledgerEntries')->select('vendors.*');

            return DataTables::of($vendors)
                ->addIndexColumn()
                ->addColumn('balance', function ($vendor) {
                    return '$' . number_format($vendor->balance, 2);
                })
                ->addColumn('status_badge', function ($vendor) {
                    $badges = [
                        'active' => '<span class="badge bg-success">Active</span>',
                        'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                        'suspended' => '<span class="badge bg-danger">Suspended</span>',
                    ];
                    return $badges[$vendor->status] ?? '';
                })
                ->addColumn('type_badge', function ($vendor) {
                    $badges = [
                        'US Agent' => '<span class="badge bg-primary">US Agent</span>',
                        'Vendor' => '<span class="badge bg-info">Vendor</span>',
                        'Supplier' => '<span class="badge bg-warning">Supplier</span>',
                    ];
                    return $badges[$vendor->type] ?? '';
                })
                ->addColumn('action', function ($vendor) {
                    $showBtn = '<a href="' . route('vendors.show', $vendor->id) . '" class="btn btn-sm btn-info">View</a>';
                    $editBtn = '<a href="' . route('vendors.edit', $vendor->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                    $deleteBtn = '<button class="btn btn-sm btn-danger delete-vendor" data-id="' . $vendor->id . '">Delete</button>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['status_badge', 'type_badge', 'action'])
                ->make(true);
        }

        return view('admin.vendors.index');
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        return view('admin.vendors.create');
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(StoreVendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show($id)
    {
        $vendor = Vendor::with(['ledgerEntries' => function ($query) {
            $query->orderBy('transaction_date', 'desc');
        }])->findOrFail($id);

        $ledgerSummary = [
            'total_credits' => $vendor->ledgerEntries()->where('type', 'credit')->sum('amount'),
            'total_debits' => $vendor->ledgerEntries()->where('type', 'debit')->sum('amount'),
            'balance' => $vendor->balance,
            'entry_count' => $vendor->ledgerEntries()->count(),
        ];

        return view('admin.vendors.show', compact('vendor', 'ledgerSummary'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(UpdateVendorRequest $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->validated());

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage (soft delete).
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully.'
        ]);
    }
}
