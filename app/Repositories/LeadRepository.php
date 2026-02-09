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

    /**
     * Get leads available for calling (exclude sold leads except those sold by the user)
     */
    public function getLeadsForCalling($userId, $userName)
    {
        return Lead::where(function($query) use ($userName) {
            // Include leads that are not sold yet
            $query->where(function($q) {
                $q->whereNull('sale_at')
                  ->orWhere('status', '!=', 'accepted');
            })
            // OR include leads sold by current user (so they can see their own sales)
            ->orWhere('closer_name', $userName);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Check if a lead has been sold within the last N months
     */
    public function checkRepeatSale($phone, $ssn, $excludeLeadId = null, $months = 3)
    {
        $dateThreshold = now()->subMonths($months);

        $query = Lead::whereNotNull('sale_at')
            ->where('sale_at', '>=', $dateThreshold);

        if ($excludeLeadId) {
            $query->where('id', '!=', $excludeLeadId);
        }

        // Check by phone OR SSN
        $query->where(function($q) use ($phone, $ssn) {
            if ($phone) {
                $q->where('phone_number', $phone);
            }
            if ($ssn) {
                $q->orWhere('ssn', $ssn);
            }
        });

        return $query->first();
    }
}
