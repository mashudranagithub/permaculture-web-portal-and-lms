<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationApproved;
use App\Notifications\OrganizationRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationRegistrationController extends Controller
{
    /**
     * Show the org registration form (public, guest only).
     */
    public function create(): Response
    {
        return Inertia::render('Auth/OrgRegister');
    }

    /**
     * Handle the org registration form submission.
     * Creates the organization (pending) and its admin user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'org_name'             => 'required|string|max:255',
            'org_email'            => 'required|email|unique:organizations,email',
            'org_phone'            => 'nullable|string|max:30',
            'org_address'          => 'nullable|string|max:500',
            'org_website'          => 'nullable|url|max:255',
            'org_description'      => 'nullable|string|max:1000',
            'org_logo'             => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            // Admin account credentials
            'admin_name'           => 'required|string|max:255',
            'admin_email'          => 'required|email|unique:users,email',
            'password'             => 'required|confirmed|min:8',
        ]);

        // 1. Handle logo upload
        $logoPath = null;
        if ($request->hasFile('org_logo')) {
            $logoPath = $request->file('org_logo')->store('organizations/logos', 'public');
        }

        // 2. Create the organization (status = pending)
        $organization = Organization::create([
            'name'        => $request->org_name,
            'email'       => $request->org_email,
            'phone'       => $request->org_phone,
            'address'     => $request->org_address,
            'website'     => $request->org_website,
            'description' => $request->org_description,
            'logo'        => $logoPath,
            'status'      => 'pending',
        ]);

        // 3. Create the org admin user
        $user = User::create([
            'name'            => $request->admin_name,
            'email'           => $request->admin_email,
            'password'        => Hash::make($request->password),
            'organization_id' => $organization->id,
            'is_approved'     => false, // blocked until org is approved
        ]);

        // 4. Assign org-admin role
        $orgAdminRole = Role::where('slug', 'org-admin')->first();
        if ($orgAdminRole) {
            $user->roles()->attach($orgAdminRole->id);
        }

        // 5. Fire Registered event → triggers email verification
        event(new Registered($user));

        // 6. Log the user in and redirect to verify-email screen
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
