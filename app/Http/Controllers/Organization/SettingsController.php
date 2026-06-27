<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Show payment settings for the organization.
     */
    public function payment()
    {
        $organization = Auth::user()->organization;
        
        if (!$organization) {
            abort(403, 'You do not belong to an organization.');
        }

        return Inertia::render('Organizations/Settings/Payment', [
            'organization' => $organization,
            'settings' => $organization->settings['payment_gateways'] ?? [
                'bkash' => [
                    'app_key' => '',
                    'app_secret' => '',
                    'username' => '',
                    'password' => '',
                    'sandbox' => true,
                    'active' => false,
                ],
                'sslcommerz' => [
                    'store_id' => '',
                    'store_password' => '',
                    'sandbox' => true,
                    'active' => false,
                ]
            ],
        ]);
    }

    /**
     * Update payment settings.
     */
    public function updatePayment(Request $request)
    {
        $organization = Auth::user()->organization;

        $request->validate([
            'bkash.app_key' => 'nullable|string',
            'bkash.app_secret' => 'nullable|string',
            'bkash.username' => 'nullable|string',
            'bkash.password' => 'nullable|string',
            'bkash.sandbox' => 'boolean',
            'bkash.active' => 'boolean',
            'sslcommerz.store_id' => 'nullable|string',
            'sslcommerz.store_password' => 'nullable|string',
            'sslcommerz.sandbox' => 'boolean',
            'sslcommerz.active' => 'boolean',
        ]);

        $settings = $organization->settings ?? [];
        $settings['payment_gateways'] = $request->only(['bkash', 'sslcommerz']);

        $organization->update(['settings' => $settings]);

        return back()->with('message', 'Payment settings updated successfully.');
    }

    /**
     * Show the organization profile edit form.
     */
    public function editOrganization()
    {
        $user = Auth::user();
        if (!$user->hasRole(['super-admin', 'admin'])) {
            abort(403, 'This action is unauthorized.');
        }

        $organization = $user->organization;

        if (!$organization) {
            abort(403, 'You do not belong to an organization.');
        }

        return Inertia::render('Organizations/Edit', [
            'organization' => $organization,
            'isSuperAdmin' => false,
        ]);
    }

    /**
     * Update the organization profile.
     */
    public function updateOrganization(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['super-admin', 'admin'])) {
            abort(403, 'This action is unauthorized.');
        }

        $organization = $user->organization;

        if (!$organization) {
            abort(403, 'You do not belong to an organization.');
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
                \Illuminate\Support\Facades\Storage::disk('public')->delete($organization->logo);
            }
            $data['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }

        if ($organization->name !== $request->name) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        $organization->update($data);

        return redirect()->back()->with('message', 'Organization profile updated successfully.');
    }
}
