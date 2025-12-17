<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    /**
     * Get all leads
     */
    public function getAllLeads()
    {
        return Lead::with(['carriers', 'forwardedBy', 'managedBy'])->get();
    }

    /**
     * Get lead by ID
     */
    public function getLeadById($id)
    {
        return Lead::with(['carriers', 'forwardedBy', 'managedBy'])->findOrFail($id);
    }

    /**
     * Create a new lead
     */
    public function createLead(array $data)
    {
        // Remove carrier-related fields from lead data since they'll go in the carrier table
        $carrierData = [
            'name' => $data['carrier_name'] ?? null,
            'coverage_amount' => $data['coverage_amount'] ?? null,
            'premium_amount' => $data['monthly_premium'] ?? null,
            'status' => 'pending',
        ];

        // Remove carrier fields from lead data to avoid issues
        $leadData = $data;
        unset($leadData['carrier_name']);

        // Create the lead
        $lead = Lead::create($leadData);

        // Create associated carrier if carrier name is provided
        if (!empty($carrierData['name'])) {
            $lead->carriers()->create($carrierData);
        }

        return $lead;
    }

    /**
     * Update a lead
     */
    public function updateLead($id, array $data)
    {
        $lead = Lead::findOrFail($id);
        $lead->update($data);
        return $lead;
    }

    /**
     * Get leads by status
     */
    public function getLeadsByStatus($status)
    {
        return Lead::where('status', $status)
            ->with(['carriers', 'forwardedBy', 'managedBy'])
            ->get();
    }

    /**
     * Get leads by user
     */
    public function getLeadsByUser($userId)
    {
        return Lead::where('forwarded_by', $userId)
            ->orWhere('managed_by', $userId)
            ->with(['carriers', 'forwardedBy', 'managedBy'])
            ->get();
    }
}
