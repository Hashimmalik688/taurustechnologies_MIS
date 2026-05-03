<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\InsuranceCarrier;
use App\Models\AgentCarrierState;
use App\Services\CommissionCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    /**
     * Display a listing of partners.
     */
    public function index()
    {
        $partners = Partner::with(['carrierStates.insuranceCarrier'])
            ->orderBy('name')
            ->get();

        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create()
    {
        $insuranceCarriers = InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.partners.create', compact('insuranceCarriers'));
    }

    /**
     * Store a newly created partner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:partners,name',
            'code' => 'required|string|max:10|unique:partners,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'ssn_last4' => 'nullable|string|size:4',
            'is_active' => 'boolean',
            'our_commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $partner = Partner::create($validated);

            // Save carrier-state relationships
            $this->syncCarrierStates($partner, $request);
        });

        return redirect()
            ->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified partner.
     */
    public function show($id)
    {
        $partner = Partner::with(['carrierStates.insuranceCarrier'])
            ->findOrFail($id);

        return view('admin.partners.show', compact('partner'));
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit($id)
    {
        $partner = Partner::findOrFail($id);
        
        $insuranceCarriers = InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get existing partner-carrier-state records grouped by carrier
        $partnerCarrierStates = AgentCarrierState::where('partner_id', $id)
            ->with('insuranceCarrier')
            ->get()
            ->groupBy('insurance_carrier_id');

        return view('admin.partners.edit', compact('partner', 'insuranceCarriers', 'partnerCarrierStates'));
    }

    /**
     * Update the specified partner.
     */
    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:partners,name,' . $id,
            'code' => 'required|string|max:10|unique:partners,code,' . $id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'ssn_last4' => 'nullable|string|size:4',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'our_commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Remove password from validated data if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        DB::transaction(function () use ($request, $partner, $validated) {
            $partner->update($validated);

            // Delete existing carrier-state records
            AgentCarrierState::where('partner_id', $partner->id)->delete();

            // Save new carrier-state relationships
            $this->syncCarrierStates($partner, $request);
        });

        // Recalculate agent_commission on all this partner's leads using new rates
        $this->recalculatePartnerLeadCommissions($partner->id);

        return redirect()
            ->route('admin.partners.edit', $partner->id)
            ->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified partner.
     */
    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);

        // Check if partner has leads - prevent deletion if they do
        $leadCount = Lead::where('partner_id', $partner->id)->count();
        if ($leadCount > 0) {
            return redirect()
                ->route('admin.partners.index')
                ->with('error', "Cannot delete partner {$partner->code} because they have {$leadCount} associated lead(s). Please reassign or remove the leads first.");
        }

        // Log the deletion attempt for audit
        \Log::warning("Partner deletion attempted", [
            'partner_id' => $partner->id,
            'partner_code' => $partner->code,
            'partner_name' => $partner->name,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);

        DB::transaction(function () use ($partner) {
            // Delete all carrier-state relationships
            AgentCarrierState::where('partner_id', $partner->id)->delete();
            
            // Delete the partner
            $partner->delete();
        });

        return redirect()
            ->route('admin.partners.index')
            ->with('success', "Partner {$partner->code} deleted successfully.");
    }

    /**
     * Sync carrier-state relationships for a partner
     */
    protected function syncCarrierStates(Partner $partner, Request $request)
    {
        // carrier_states[carrier_id][] = state
        $carrierStates = $request->input('carrier_states', []);
        
        // Carrier-level commission rates (not state-specific)
        $settlementLevel = $request->input('settlement_level', []);
        $settlementGraded = $request->input('settlement_graded', []);
        $settlementGi = $request->input('settlement_gi', []);
        $settlementModified = $request->input('settlement_modified', []);

        foreach ($carrierStates as $carrierId => $states) {
            if (empty($states)) {
                continue;
            }

            foreach ($states as $state) {
                // Rates may be keyed by [carrierId][state] or just [carrierId] (scalar fallback)
                $levelRaw    = $settlementLevel[$carrierId]    ?? null;
                $gradedRaw   = $settlementGraded[$carrierId]   ?? null;
                $giRaw       = $settlementGi[$carrierId]       ?? null;
                $modifiedRaw = $settlementModified[$carrierId] ?? null;

                AgentCarrierState::create([
                    'partner_id'              => $partner->id,
                    'user_id'                 => null,
                    'insurance_carrier_id'    => $carrierId,
                    'state'                   => $state,
                    'settlement_level_pct'    => is_array($levelRaw)    ? ($levelRaw[$state]    ?? null) : $levelRaw,
                    'settlement_graded_pct'   => is_array($gradedRaw)   ? ($gradedRaw[$state]   ?? null) : $gradedRaw,
                    'settlement_gi_pct'       => is_array($giRaw)       ? ($giRaw[$state]       ?? null) : $giRaw,
                    'settlement_modified_pct' => is_array($modifiedRaw) ? ($modifiedRaw[$state] ?? null) : $modifiedRaw,
                ]);
            }
        }
    }

    /**
     * Recalculate agent_commission for all pending/active leads of a partner.
     * Called automatically after partner rates are updated.
     */
    protected function recalculatePartnerLeadCommissions(int $partnerId): void
    {
        $commSvc = new CommissionCalculationService();

        Lead::where('partner_id', $partnerId)
            ->whereNotNull('pending_contract_at')
            ->where('monthly_premium', '>', 0)
            ->whereNotNull('insurance_carrier_id')
            ->whereNotNull('state')
            ->chunk(100, function ($leads) use ($commSvc) {
                foreach ($leads as $lead) {
                    $settlement = CommissionCalculationService::normalizeSettlementType(
                        $lead->settlement_type ?? $lead->policy_type
                    );
                    $result = $commSvc->calculateCommission(
                        $lead->partner_id,
                        $lead->insurance_carrier_id,
                        $lead->state,
                        $settlement,
                        (float) $lead->monthly_premium
                    );
                    if ($result['success']) {
                        $lead->timestamps = false; // don't bump updated_at
                        $lead->agent_commission             = $result['commission'];
                        $lead->commission_calculation_notes = '[auto-recalc] ' . $result['message'];
                        $lead->save();
                    }
                }
            });
    }

    /**
     * Remove carrier assignment from partner.
     */
    public function removeCarrierAssignment($partnerId, $carrierId)
    {
        try {
            $partner = Partner::findOrFail($partnerId);
            $carrier = InsuranceCarrier::findOrFail($carrierId);
            
            // Remove all AgentCarrierState records for this partner-carrier combination
            $deletedCount = AgentCarrierState::where('partner_id', $partnerId)
                ->where('insurance_carrier_id', $carrierId)
                ->delete();
                
            if ($deletedCount > 0) {
                return redirect()->route('admin.insurance-carriers.index')
                    ->with('success', "Successfully removed {$carrier->name} assignment from partner {$partner->name} ({$deletedCount} states removed).");
            } else {
                return redirect()->route('admin.insurance-carriers.index')
                    ->with('warning', "No carrier assignments found for {$carrier->name} and partner {$partner->name}.");
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.insurance-carriers.index')
                ->with('error', 'Failed to remove carrier assignment: ' . $e->getMessage());
        }
    }
}
