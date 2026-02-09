<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return User::with(['roles', 'userDetail'])->get();
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return User::with(['roles', 'userDetail'])->findOrFail($id);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = strtolower($data['email']); // Convert to lowercase for consistency
        $user->password = Hash::make($data['password']);
        $user->zoom_number = $data['zoom_number'] ?? null;
        $user->save();

        // Assign role if provided
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        // Create user details if provided
        if (isset($data['phone']) || isset($data['dob']) || isset($data['gender']) ||
            isset($data['join_date']) || isset($data['address']) || isset($data['city'])) {
            $userDetail = new UserDetail;
            $userDetail->user_id = $user->id;
            $userDetail->phone = $data['phone'] ?? null;
            $userDetail->dob = $data['dob'] ?? null;
            $userDetail->gender = $data['gender'] ?? null;
            $userDetail->join_date = $data['join_date'] ?? null;
            $userDetail->address = $data['address'] ?? null;
            $userDetail->city = $data['city'] ?? null;
            $userDetail->save();
        }

        return $user;
    }

    /**
     * Update a user
     */
    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);

        // Update user basic info
        $user->name = $data['name'] ?? $user->name;
        $user->email = isset($data['email']) ? strtolower($data['email']) : $user->email; // Convert to lowercase for consistency
        $user->zoom_number = $data['zoom_number'] ?? $user->zoom_number;

        // Update password only if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update or create user details
        $userDetail = $user->userDetail ?? new UserDetail(['user_id' => $user->id]);
        $userDetail->phone = $data['phone'] ?? $userDetail->phone;
        $userDetail->dob = $data['dob'] ?? $userDetail->dob;
        $userDetail->gender = $data['gender'] ?? $userDetail->gender;
        $userDetail->join_date = $data['join_date'] ?? $userDetail->join_date;
        $userDetail->address = $data['address'] ?? $userDetail->address;
        $userDetail->city = $data['city'] ?? $userDetail->city;
        $userDetail->save();

        // Update user role if provided
        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return User::role($role)->with('userDetail')->get();
    }
}
