<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;

class BatchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-batches');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Batch $batch): bool
    {
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || $user->organization_id === $batch->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-batches');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Batch $batch): bool
    {
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || ($user->organization_id === $batch->organization_id && $user->hasPermission('edit-batches'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Batch $batch): bool
    {
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || ($user->organization_id === $batch->organization_id && $user->hasPermission('delete-batches'));
    }
}
