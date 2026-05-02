<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationApproved
{
    /**
     * Block org-admin and student users if their organization is not active.
     * LMS Super Admins (no organization_id) are always allowed through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not logged in — let other middleware handle it
        if (!$user) {
            return $next($request);
        }

        // LMS admin users have no organization — always allowed
        if (!$user->organization_id) {
            return $next($request);
        }

        // Load the organization and check its status
        $org = $user->organization;

        if (!$org || !$org->isActive()) {
            // Show a pending/rejected/suspended page
            return inertia('Auth/OrgPending', [
                'status'           => $org?->status ?? 'unknown',
                'rejectionReason'  => $org?->rejection_reason,
                'orgName'          => $org?->name,
            ])->toResponse($request);
        }

        return $next($request);
    }
}
