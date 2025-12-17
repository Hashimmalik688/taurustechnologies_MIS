<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Get all users
     */
    public function getAllUsers();

    /**
     * Get user by ID
     */
    public function getUserById($id);

    /**
     * Create a new user
     */
    public function createUser(array $data);

    /**
     * Update a user
     */
    public function updateUser($id, array $data);

    /**
     * Delete a user
     */
    public function deleteUser($id);

    /**
     * Get users by role
     */
    public function getUsersByRole($role);
}
