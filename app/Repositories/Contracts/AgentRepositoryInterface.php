<?php

namespace App\Repositories\Contracts;

interface AgentRepositoryInterface
{
    /**
     * Get all agents
     */
    public function getAllAgents();

    /**
     * Get agent by ID
     */
    public function getAgentById($id);

    /**
     * Create a new agent
     */
    public function createAgent(array $data);

    /**
     * Update an agent
     */
    public function updateAgent($id, array $data);

    /**
     * Get agents by state
     */
    public function getAgentsByState($state);
}
