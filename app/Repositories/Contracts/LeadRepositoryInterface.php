<?php

namespace App\Repositories\Contracts;

interface LeadRepositoryInterface
{
    /**
     * Get all leads
     */
    public function getAllLeads();

    /**
     * Get lead by ID
     */
    public function getLeadById($id);

    /**
     * Create a new lead
     */
    public function createLead(array $data);

    /**
     * Update a lead
     */
    public function updateLead($id, array $data);

    /**
     * Get leads by status
     */
    public function getLeadsByStatus($status);

    /**
     * Get leads by user
     */
    public function getLeadsByUser($userId);
}
