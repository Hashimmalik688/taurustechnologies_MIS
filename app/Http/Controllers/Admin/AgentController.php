<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::role('Agent')
            ->with(['userDetail', 'carrierCommissions.insuranceCarrier', 'carrierStates'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        $insuranceCarriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('admin.agents.create', compact('insuranceCarriers'));
    }

    public function store(StoreAgentRequest $request)
    {
        try {
            \DB::transaction(function () use ($request) {

                // Create the user
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = \Hash::make($request->password);
                $user->save();

                // Assign the Agent role
                $user->assignRole('Agent');

                // Create user details
                $userDetail = new UserDetail;
                $userDetail->user_id = $user->id;
                $userDetail->state = $request->state;
                $userDetail->address = $request->address;
                $userDetail->phone = $request->phone;
                $userDetail->ssn_last4 = $request->ssn_last4;
                $userDetail->dob = $request->dob;

                // Store active states as JSON if provided
                if (! empty($request->active_states)) {
                    $userDetail->active_states = json_encode($request->active_states);
                }

                // Get selected carrier names for backward compatibility
                if (! empty($request->selected_carriers)) {
                    $carrierNames = \App\Models\InsuranceCarrier::whereIn('id', $request->selected_carriers)
                        ->pluck('name')
                        ->toArray();
                    $userDetail->carriers = json_encode($carrierNames);
                }

                $userDetail->save();

                // Create agent-carrier commission records
                if (! empty($request->selected_carriers)) {
                    foreach ($request->selected_carriers as $carrierId) {
                        $commissionPercentage = $request->carrier_commissions[$carrierId] ?? null;
                        
                        // Only create if commission is set, otherwise use carrier's base rate
                        if ($commissionPercentage !== null && $commissionPercentage !== '') {
                            \App\Models\AgentCarrierCommission::create([
                                'user_id' => $user->id,
                                'insurance_carrier_id' => $carrierId,
                                'commission_percentage' => $commissionPercentage,
                            ]);
                        }

                        // Create agent-carrier-state records if states are provided
                        if ($request->has("carrier_states.{$carrierId}") && !empty($request->carrier_states[$carrierId])) {
                            foreach ($request->carrier_states[$carrierId] as $state) {
                                \App\Models\AgentCarrierState::create([
                                    'user_id' => $user->id,
                                    'insurance_carrier_id' => $carrierId,
                                    'state' => $state,
                                    'settlement_level_pct' => $request->input("settlement_level.{$carrierId}.{$state}"),
                                    'settlement_graded_pct' => $request->input("settlement_graded.{$carrierId}.{$state}"),
                                    'settlement_gi_pct' => $request->input("settlement_gi.{$carrierId}.{$state}"),
                                    'settlement_modified_pct' => $request->input("settlement_modified.{$carrierId}.{$state}"),
                                ]);
                            }
                        }
                    }
                }
            });

            return redirect()->route('agents.index')->with('success', 'Agent created successfully.');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Agent creation failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create agent. Please try again.');
        }

    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.agents.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $insuranceCarriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get existing carrier commissions
        $agentCommissions = \App\Models\AgentCarrierCommission::where('user_id', $id)
            ->pluck('commission_percentage', 'insurance_carrier_id')
            ->toArray();

        // Get existing agent-carrier-state records
        $agentCarrierStates = \App\Models\AgentCarrierState::where('user_id', $id)
            ->get()
            ->groupBy('insurance_carrier_id');

        return view('admin.agents.edit', compact('user', 'insuranceCarriers', 'agentCommissions', 'agentCarrierStates'));
    }

    public function update(UpdateAgentRequest $request, $id)
    {
        try {
            \DB::transaction(function () use ($request, $id) {
                $user = User::findOrFail($id);
                $user->name = $request->name;
                $user->email = $request->email;

                if ($request->password) {
                    $user->password = \Hash::make($request->password);
                }

                $user->save();

                // Update user details
                $userDetail = $user->userDetail ?? new UserDetail(['user_id' => $user->id]);
                $userDetail->state = $request->state;
                $userDetail->address = $request->address;
                $userDetail->phone = $request->phone;
                $userDetail->ssn_last4 = $request->ssn_last4;
                $userDetail->dob = $request->dob;

                // Update active states if provided
                if ($request->has('active_states')) {
                    $userDetail->active_states = json_encode($request->active_states);
                }

                // Get selected carrier names for backward compatibility
                if ($request->has('selected_carriers') && !empty($request->selected_carriers)) {
                    $carrierNames = \App\Models\InsuranceCarrier::whereIn('id', $request->selected_carriers)
                        ->pluck('name')
                        ->toArray();
                    $userDetail->carriers = json_encode($carrierNames);
                } else {
                    $userDetail->carriers = json_encode([]);
                }

                $userDetail->save();

                // Sync agent-carrier commission records
                // Delete old records
                \App\Models\AgentCarrierCommission::where('user_id', $user->id)->delete();
                \App\Models\AgentCarrierState::where('user_id', $user->id)->delete();
                
                // Create new records
                if ($request->has('selected_carriers') && !empty($request->selected_carriers)) {
                    foreach ($request->selected_carriers as $carrierId) {
                        $commissionPercentage = $request->carrier_commissions[$carrierId] ?? null;
                        
                        // Only create if commission is set
                        if ($commissionPercentage !== null && $commissionPercentage !== '') {
                            \App\Models\AgentCarrierCommission::create([
                                'user_id' => $user->id,
                                'insurance_carrier_id' => $carrierId,
                                'commission_percentage' => $commissionPercentage,
                            ]);
                        }

                        // Create agent-carrier-state records if states are provided
                        if ($request->has("carrier_states.{$carrierId}") && !empty($request->carrier_states[$carrierId])) {
                            foreach ($request->carrier_states[$carrierId] as $state) {
                                \App\Models\AgentCarrierState::create([
                                    'user_id' => $user->id,
                                    'insurance_carrier_id' => $carrierId,
                                    'state' => $state,
                                    'settlement_level_pct' => $request->input("settlement_level.{$carrierId}.{$state}"),
                                    'settlement_graded_pct' => $request->input("settlement_graded.{$carrierId}.{$state}"),
                                    'settlement_gi_pct' => $request->input("settlement_gi.{$carrierId}.{$state}"),
                                    'settlement_modified_pct' => $request->input("settlement_modified.{$carrierId}.{$state}"),
                                ]);
                            }
                        }
                    }
                }
            });

            return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Agent update failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update agent. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            \DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);
                
                // Delete related records first to prevent foreign key constraint errors
                \App\Models\AgentCarrierCommission::where('user_id', $user->id)->delete();
                \App\Models\AgentCarrierState::where('user_id', $user->id)->delete();
                
                // Delete user details if exists
                if ($user->userDetail) {
                    $user->userDetail->delete();
                }
                
                // Soft delete the user
                $user->delete();
            });

            return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Agent deletion failed: ' . $e->getMessage());
            return redirect()->route('agents.index')->with('error', 'Failed to delete agent. Please try again.');
        }
    }
}
