<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\OrganizationApproved;
use App\Notifications\OrganizationRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    /**
     * List all organizations (LMS Admin only).
     */
    public function index(Request $request): Response
    {
        $organizations = Organization::withTrashed()
            ->with('approvedBy:id,name')
            ->withCount(['users', 'courses'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Organizations/Index', [
            'organizations' => $organizations,
            'filters'       => $request->only(['search', 'status']),
            'pendingCount'  => Organization::where('status', 'pending')->count(),
        ]);
    }

    /**
     * Show the pending approvals queue.
     */
    public function approvalQueue(): Response
    {
        $pending = Organization::where('status', 'pending')
            ->with('users:id,name,email,organization_id')
            ->withCount('users')
            ->latest()
            ->get();

        return Inertia::render('Organizations/ApprovalQueue', [
            'pendingOrganizations' => $pending,
        ]);
    }

    /**
     * Show one organization's detail.
     */
    public function show(Organization $organization): Response
    {
        $organization->load([
            'approvedBy:id,name',
            'users:id,name,email,organization_id,created_at',
            'courses:id,organization_id,title,status',
        ]);
        $organization->append(['logo_url', 'status_badge']);

        return Inertia::render('Organizations/Show', [
            'organization' => $organization,
        ]);
    }

    /**
     * Approve an organization.
     */
    public function approve(Organization $organization): RedirectResponse
    {
        $organization->update([
            'status'      => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Activate all users belonging to this org
        $organization->users()->update(['is_approved' => true]);

        // Notify each admin user
        $organization->users()->each(function (User $user) use ($organization) {
            $user->notify(new OrganizationApproved($organization));
        });

        return redirect()->route('admin.organizations.queue')
            ->with('message', "Organization '{$organization->name}' has been approved.");
    }

    /**
     * Reject an organization.
     */
    public function reject(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $organization->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // Notify each user
        $organization->users()->each(function (User $user) use ($organization, $request) {
            $user->notify(new OrganizationRejected($organization, $request->reason));
        });

        return redirect()->route('admin.organizations.queue')
            ->with('message', "Organization '{$organization->name}' has been rejected.");
    }

    /**
     * Suspend an active organization.
     */
    public function suspend(Organization $organization): RedirectResponse
    {
        $organization->update(['status' => 'suspended']);
        $organization->users()->update(['is_approved' => false]);

        return redirect()->route('admin.organizations.index')
            ->with('message', "Organization '{$organization->name}' has been suspended.");
    }

    /**
     * Reactivate a suspended organization.
     */
    public function reactivate(Organization $organization): RedirectResponse
    {
        $organization->update([
            'status'      => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        $organization->users()->update(['is_approved' => true]);

        return redirect()->route('admin.organizations.index')
            ->with('message', "Organization '{$organization->name}' has been reactivated.");
    }

    /**
     * Soft-delete an organization.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('message', "Organization '{$organization->name}' has been deleted.");
    }
}
