<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of chart of accounts.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accounts = ChartOfAccount::with('parentAccount')
                ->select('chart_of_accounts.*');

            // Apply filters
            if ($request->has('account_type') && $request->account_type) {
                $accounts->where('account_type', $request->account_type);
            }

            if ($request->has('is_active') && $request->is_active !== '') {
                $accounts->where('is_active', $request->is_active);
            }

            return DataTables::of($accounts)
                ->addIndexColumn()
                ->addColumn('parent_account_name', function ($account) {
                    return $account->parentAccount->account_name ?? 'N/A';
                })
                ->addColumn('balance_formatted', function ($account) {
                    return '$' . number_format($account->current_balance, 2);
                })
                ->addColumn('status', function ($account) {
                    return $account->is_active 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($account) {
                    return '
                        <a href="' . route('chart-of-accounts.show', $account->id) . '" class="action-btn action-btn-view">
                            <i class="bx bx-show"></i>
                        </a>
                        <a href="' . route('chart-of-accounts.edit', $account->id) . '" class="action-btn action-btn-edit">
                            <i class="bx bx-edit"></i>
                        </a>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $accountTypes = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];

        return view('admin.chart-of-accounts.index', compact('accountTypes'));
    }

    /**
     * Show the form for creating a new chart of account.
     */
    public function create()
    {
        $parentAccounts = ChartOfAccount::where('is_active', true)->get();
        $accountTypes = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        $accountCategories = [
            'Current Asset',
            'Fixed Asset',
            'Current Liability',
            'Long-term Liability',
            'Owner Equity',
            'Operating Revenue',
            'Non-operating Revenue',
            'Operating Expense',
            'Non-operating Expense',
            'Cost of Goods Sold'
        ];

        return view('admin.chart-of-accounts.create', compact('parentAccounts', 'accountTypes', 'accountCategories'));
    }

    /**
     * Store a newly created chart of account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:255|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'account_category' => 'nullable|string',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        ChartOfAccount::create($validated);

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'Chart of Account created successfully.');
    }

    /**
     * Display the specified chart of account.
     */
    public function show($id)
    {
        $account = ChartOfAccount::with(['parentAccount', 'childAccounts'])->findOrFail($id);

        return view('admin.chart-of-accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified chart of account.
     */
    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->where('id', '!=', $id)
            ->get();
        $accountTypes = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        $accountCategories = [
            'Current Asset',
            'Fixed Asset',
            'Current Liability',
            'Long-term Liability',
            'Owner Equity',
            'Operating Revenue',
            'Non-operating Revenue',
            'Operating Expense',
            'Non-operating Expense',
            'Cost of Goods Sold'
        ];

        return view('admin.chart-of-accounts.edit', compact('account', 'parentAccounts', 'accountTypes', 'accountCategories'));
    }

    /**
     * Update the specified chart of account in storage.
     */
    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'account_code' => 'required|string|max:255|unique:chart_of_accounts,account_code,' . $id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'account_category' => 'nullable|string',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'Chart of Account updated successfully.');
    }

    /**
     * Remove the specified chart of account from storage.
     */
    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        // Check if account has child accounts
        if ($account->childAccounts()->count() > 0) {
            return back()->with('error', 'Cannot delete account with sub-accounts.');
        }

        $account->delete();

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'Chart of Account deleted successfully.');
    }
}
