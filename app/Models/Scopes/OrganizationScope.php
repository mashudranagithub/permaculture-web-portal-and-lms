<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();

            // LMS Super Admins can see everything
            if ($user->hasRole(['super-admin', 'admin'])) {
                return;
            }

            // Scope by organization_id for organization users
            if ($user->organization_id) {
                $builder->where($model->getTable() . '.organization_id', $user->organization_id);
            }
        }
    }
}
