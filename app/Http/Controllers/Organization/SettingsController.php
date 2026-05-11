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
}
