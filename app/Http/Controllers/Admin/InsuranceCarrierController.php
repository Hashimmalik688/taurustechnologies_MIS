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
        $partnerCarriers = [];
        
        // Get partners from the new Partner model with partner_id in agent_carrier_states
        $newPartnerCarrierStates = \App\Models\AgentCarrierState::with(['partner', 'insuranceCarrier'])
            ->whereNotNull('partner_id')
            ->get()
            ->groupBy(function($item) {
                return $item->partner_id . '_' . $item->insurance_carrier_id;
            });

        foreach ($newPartnerCarrierStates as $group) {
            $firstRecord = $group->first();
            $partner = $firstRecord->partner;
            $carrier = $firstRecord->insuranceCarrier;
            
            if(!$partner || !$carrier) continue;
            
            // Get partner's states for this carrier
            $states = $group->pluck('state')->toArray();
            
            // Get leads count for this partner-carrier combo
            $leadsCount = \App\Models\Lead::where('insurance_carrier_id', $carrier->id)
                ->whereIn('state', $states)
                ->count();
            
            // Get average settlement percentages
            $avgLevel = $group->avg('settlement_level_pct');
            $avgGraded = $group->avg('settlement_graded_pct');
            $avgGi = $group->avg('settlement_gi_pct');
            $avgModified = $group->avg('settlement_modified_pct');
            
            // Create a partner object that has both id and name for consistency
            $partnerObj = (object) [
                'id' => $partner->id,
                'name' => $partner->name,
                'code' => $partner->code,
                'email' => $partner->email,
                'is_partner_model' => true,
            ];
            
            $partnerCarriers[] = [
                'partner' => $partnerObj,
                'carrier' => $carrier,
                'states' => $states,
                'state_count' => count($states),
                'leads_count' => $leadsCount,
                'avg_level' => $avgLevel,
                'avg_graded' => $avgGraded,
                'avg_gi' => $avgGi,
                'avg_modified' => $avgModified,
            ];
        }

        // Calculate summary stats
        // Count partner-carrier assignments (each assignment counts separately)
        $totalCarriers = count($partnerCarriers);
        
        // Count unique partners (not combinations)
        $uniquePartnerIds = array_unique(array_map(function($item) {
            return $item['partner']->id;
        }, $partnerCarriers));
        $totalPartners = count($uniquePartnerIds);
        
        $totalStates = !empty($partnerCarriers) ? count(array_unique(array_merge(...array_column($partnerCarriers, 'states')))) : 0;
        $totalLeads = !empty($partnerCarriers) ? array_sum(array_column($partnerCarriers, 'leads_count')) : 0;

        return view('admin.insurance-carriers.index', compact('partnerCarriers', 'totalCarriers', 'totalPartners', 'totalStates', 'totalLeads'));
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

        // Ensure is_active is properly set as boolean
        $validated['is_active'] = $request->boolean('is_active');

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

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Insurance carrier created successfully.',
                'carrier' => $carrier->load('commissionBrackets')
            ]);
        }

        return redirect()->route('admin.insurance-carriers.index')
            ->with('success', 'Insurance carrier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InsuranceCarrier $insuranceCarrier)
    {
        if (request()->expectsJson()) {
            return response()->json($insuranceCarrier->load('commissionBrackets'));
        }
        
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

        // Ensure is_active is properly set as boolean
        $validated['is_active'] = $request->boolean('is_active');

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

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Insurance carrier updated successfully.',
                'carrier' => $insuranceCarrier->fresh()->load('commissionBrackets')
            ]);
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
        $leadCount = $insuranceCarrier->leads()->count();
        if ($leadCount > 0) {
            return back()->with('error', "Cannot delete carrier '{$insuranceCarrier->name}' because it has {$leadCount} associated lead(s). Please reassign or delete the leads first.");
        }

        DB::transaction(function () use ($insuranceCarrier) {
            // First, delete all partner/agent carrier state assignments
            AgentCarrierState::where('insurance_carrier_id', $insuranceCarrier->id)->delete();
            
            // Then delete the carrier itself
            $insuranceCarrier->delete();
        });

        return redirect()->route('admin.insurance-carriers.index')
            ->with('success', "Insurance carrier '{$insuranceCarrier->name}' and all its assignments have been permanently deleted.");
    }
}
