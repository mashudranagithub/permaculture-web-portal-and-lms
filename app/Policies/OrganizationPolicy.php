<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin']) && !$user->organization_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        // Global admin or user belonging to the org
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || $user->organization_id === $organization->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only global admin can manually create orgs in backend
        return $user->hasRole(['super-admin', 'admin']) && !$user->organization_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        // Global admin or Org Admin of this specific org
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || ($user->organization_id === $organization->id && $user->hasRole('org-admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->hasRole(['super-admin', 'admin']) && !$user->organization_id;
    }
}
