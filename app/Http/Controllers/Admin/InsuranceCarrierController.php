<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCarrier;
use Illuminate\Http\Request;

class InsuranceCarrierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carriers = InsuranceCarrier::with('commissionBrackets')->orderBy('name')->paginate(20);
        return view('admin.insurance-carriers.index', compact('carriers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.insurance-carriers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:insurance_carriers,name',
            'payment_module' => 'required|in:on_draft,on_issue,as_earned',
            'phone' => 'nullable|string|max:20',
            'ssn_last4' => 'nullable|string|size:4',
            'base_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'plan_types' => 'nullable|string',
            'calculation_notes' => 'nullable|string',
            'is_active' => 'boolean',
            'brackets' => 'nullable|array',
            'brackets.*.age_min' => 'required_with:brackets|integer|min:0|max:120',
            'brackets.*.age_max' => 'required_with:brackets|integer|min:0|max:120',
            'brackets.*.commission_percentage' => 'required_with:brackets|numeric|min:0|max:100',
            'brackets.*.notes' => 'nullable|string',
        ]);

        // Convert plan_types from comma-separated string to array
        if (!empty($validated['plan_types'])) {
            $validated['plan_types'] = array_map('trim', explode(',', $validated['plan_types']));
        }

        $validated['is_active'] = $request->has('is_active');

        $carrier = InsuranceCarrier::create($validated);

        // Add commission brackets if provided
        if ($request->has('brackets')) {
            foreach ($request->brackets as $bracketData) {
                $carrier->commissionBrackets()->create([
                    'age_min' => $bracketData['age_min'],
                    'age_max' => $bracketData['age_max'],
                    'commission_percentage' => $bracketData['commission_percentage'],
                    'notes' => $bracketData['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.insurance-carriers.index')
            ->with('success', 'Insurance carrier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InsuranceCarrier $insuranceCarrier)
    {
        return view('admin.insurance-carriers.show', compact('insuranceCarrier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InsuranceCarrier $insuranceCarrier)
    {
        $insuranceCarrier->load('commissionBrackets');
        return view('admin.insurance-carriers.edit', compact('insuranceCarrier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InsuranceCarrier $insuranceCarrier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:insurance_carriers,name,' . $insuranceCarrier->id,
            'payment_module' => 'required|in:on_draft,on_issue,as_earned',
            'phone' => 'nullable|string|max:20',
            'ssn_last4' => 'nullable|string|size:4',
            'base_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'plan_types' => 'nullable|string',
            'calculation_notes' => 'nullable|string',
            'is_active' => 'boolean',
            'brackets' => 'nullable|array',
            'brackets.*.age_min' => 'required_with:brackets|integer|min:0|max:120',
            'brackets.*.age_max' => 'required_with:brackets|integer|min:0|max:120',
            'brackets.*.commission_percentage' => 'required_with:brackets|numeric|min:0|max:100',
            'brackets.*.notes' => 'nullable|string',
        ]);

        // Convert plan_types from comma-separated string to array
        if (!empty($validated['plan_types'])) {
            $validated['plan_types'] = array_map('trim', explode(',', $validated['plan_types']));
        } else {
            $validated['plan_types'] = [];
        }

        $validated['is_active'] = $request->has('is_active');

        $insuranceCarrier->update($validated);

        // Handle commission brackets
        if ($request->has('brackets')) {
            $existingBracketIds = [];
            
            foreach ($request->brackets as $bracketData) {
                if (isset($bracketData['id'])) {
                    // Update existing bracket
                    $bracket = $insuranceCarrier->commissionBrackets()->find($bracketData['id']);
                    if ($bracket) {
                        $bracket->update([
                            'age_min' => $bracketData['age_min'],
                            'age_max' => $bracketData['age_max'],
                            'commission_percentage' => $bracketData['commission_percentage'],
                            'notes' => $bracketData['notes'] ?? null,
                        ]);
                        $existingBracketIds[] = $bracket->id;
                    }
                } else {
                    // Create new bracket
                    $newBracket = $insuranceCarrier->commissionBrackets()->create([
                        'age_min' => $bracketData['age_min'],
                        'age_max' => $bracketData['age_max'],
                        'commission_percentage' => $bracketData['commission_percentage'],
                        'notes' => $bracketData['notes'] ?? null,
                    ]);
                    $existingBracketIds[] = $newBracket->id;
                }
            }

            // Delete brackets that were removed
            $insuranceCarrier->commissionBrackets()
                ->whereNotIn('id', $existingBracketIds)
                ->delete();
        } else {
            // No brackets submitted, delete all existing
            $insuranceCarrier->commissionBrackets()->delete();
        }

        return redirect()->route('admin.insurance-carriers.index')
            ->with('success', 'Insurance carrier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InsuranceCarrier $insuranceCarrier)
    {
        // Check if carrier has leads
        if ($insuranceCarrier->leads()->count() > 0) {
            return back()->with('error', 'Cannot delete carrier that has associated leads.');
        }

        $insuranceCarrier->delete();

        return redirect()->route('admin.insurance-carriers.index')
            ->with('success', 'Insurance carrier deleted successfully.');
    }
}
