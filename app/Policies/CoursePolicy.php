<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-courses');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Scope handles the filtering, but double check org_id
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || $user->organization_id === $course->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-courses');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || ($user->organization_id === $course->organization_id && $user->hasPermission('edit-courses'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return ($user->hasRole(['super-admin', 'admin']) && !$user->organization_id)
            || ($user->organization_id === $course->organization_id && $user->hasPermission('delete-courses'));
    }
}
