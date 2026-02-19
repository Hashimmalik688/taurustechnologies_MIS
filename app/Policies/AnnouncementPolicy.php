<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
{
    /**
     * Only Super Admin and Co-ordinator can manage announcements
     */
    private function isAuthorized(User $user): bool
    {
        return $user->hasRole([Roles::SUPER_ADMIN, Roles::COORDINATOR]);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $announcement): bool
    {
        return $this->isAuthorized($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $this->isAuthorized($user);
    }
}
