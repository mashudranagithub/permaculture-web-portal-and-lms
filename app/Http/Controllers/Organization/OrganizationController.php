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
use Illuminate\Support\Facades\Storage;
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
        $organization->loadCount(['users', 'courses', 'batches']);
        $organization->load([
            'approvedBy:id,name',
            'users:id,name,email,organization_id,created_at',
        ]);
        
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

    /**
     * Show the edit form for an organization (Super Admin).
     */
    public function edit(Organization $organization): Response
    {
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        return Inertia::render('Organizations/Edit', [
            'organization' => $organization,
            'isSuperAdmin' => true,
        ]);
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        if (!$request->user()->hasRole('super-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:organizations,email,' . $organization->id,
            'phone'       => 'nullable|string|max:30',
            'website'     => 'nullable|url|max:255',
            'address'     => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'website', 'address', 'description']);

        if ($request->hasFile('logo')) {
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $data['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }

        if ($organization->name !== $request->name) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        $organization->update($data);

        return redirect()->route('admin.organizations.show', $organization->id)
            ->with('message', 'Organization profile updated successfully.');
    }
}
