<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AgentRepository implements AgentRepositoryInterface
{
    /**
     * Get all agents
     */
    public function getAllAgents()
    {
        return User::role('Agent')
            ->with('userDetail')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get agent by ID
     */
    public function getAgentById($id)
    {
        return User::with('userDetail')->findOrFail($id);
    }

    /**
     * Create a new agent
     */
    public function createAgent(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create the user
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();

            // Assign the Agent role
            $user->assignRole('Agent');

            // Create user details
            $userDetail = new UserDetail;
            $userDetail->user_id = $user->id;
            $userDetail->state = $data['state'];
            $userDetail->address = $data['address'];

            // Store active states as JSON if provided
            if (!empty($data['active_states'])) {
                $userDetail->active_states = json_encode($data['active_states']);
            }

            // Filter out empty carriers and store as JSON if provided
            if (!empty($data['carriers'])) {
                $filteredCarriers = array_filter($data['carriers'], function ($carrier) {
                    return !empty(trim($carrier));
                });

                if (!empty($filteredCarriers)) {
                    $userDetail->carriers = json_encode(array_values($filteredCarriers));
                }
            }

            $userDetail->save();

            return $user;
        });
    }

    /**
     * Update an agent
     */
    public function updateAgent($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);
            $user->name = $data['name'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            // Update user details
            $userDetail = $user->userDetail ?? new UserDetail(['user_id' => $user->id]);
            $userDetail->state = $data['state'] ?? $userDetail->state;
            $userDetail->address = $data['address'] ?? $userDetail->address;

            // Update active states if provided
            if (isset($data['active_states'])) {
                $userDetail->active_states = json_encode($data['active_states']);
            }

            // Update carriers if provided
            if (isset($data['carriers'])) {
                $filteredCarriers = array_filter($data['carriers'], function ($carrier) {
                    return !empty(trim($carrier));
                });

                if (!empty($filteredCarriers)) {
                    $userDetail->carriers = json_encode(array_values($filteredCarriers));
                }
            }

            $userDetail->save();

            return $user;
        });
    }

    /**
     * Get agents by state
     */
    public function getAgentsByState($state)
    {
        return User::role('Agent')
            ->whereHas('userDetail', function ($query) use ($state) {
                $query->where('state', $state);
            })
            ->with('userDetail')
            ->get();
    }
}
