<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BkashController extends Controller
{
    protected BkashService $bkashService;

    public function __construct(BkashService $bkashService)
    {
        $this->bkashService = $bkashService;
    }

    /**
     * Initiate payment.
     */
    public function pay(Request $request, Enrollment $enrollment)
    {
        // Security check
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($enrollment->payment_status === 'paid') {
            return redirect()->route('enrollments.my-courses')->with('error', 'Already paid.');
        }

        // Load organization config
        $this->bkashService->useOrganizationConfig($enrollment->organization);

        $token = $this->bkashService->getToken();
        if (!$token) {
            return back()->with('error', 'Could not connect to bKash. Please try again.');
        }

        $paymentData = [
            'amount' => $enrollment->price_at_enrollment,
            'invoiceNumber' => $enrollment->enrollment_no,
            'payerReference' => Auth::user()->phone ?? '01XXXXXXXXX',
        ];

        $response = $this->bkashService->createPayment($token, $paymentData);

        if ($response && isset($response['bkashURL'])) {
            // Create a pending payment record
            Payment::updateOrCreate(
                ['enrollment_id' => $enrollment->id, 'status' => 'pending'],
                [
                    'organization_id' => $enrollment->organization_id,
                    'user_id' => $enrollment->user_id,
                    'batch_id' => $enrollment->batch_id,
                    'amount' => $enrollment->price_at_enrollment,
                    'net_amount' => $enrollment->price_at_enrollment,
                    'gateway' => 'bkash',
                    'payment_details' => ['paymentID' => $response['paymentID']],
                ]
            );

            return \Inertia\Inertia::location($response['bkashURL']);
        }

        return back()->with('error', 'Failed to create bKash payment.');
    }

    /**
     * Callback from bKash.
     */
    public function callback(Request $request)
    {
        $status = $request->query('status');
        $paymentID = $request->query('paymentID');

        if ($status === 'success') {
            // Find payment to get organization context
            $payment = Payment::withoutGlobalScopes()->whereJsonContains('payment_details->paymentID', $paymentID)->first();
            if (!$payment) {
                return redirect()->route('enrollments.my-courses')->with('error', 'Payment record not found.');
            }

            $this->bkashService->useOrganizationConfig($payment->organization);

            $token = $this->bkashService->getToken();
            $response = $this->bkashService->executePayment($token, $paymentID);

            if ($response && $response['statusCode'] === '0000') {
                // Payment successful
                $this->finalizePayment($response);

                return redirect()->route('enrollments.my-courses')
                    ->with('message', 'Payment successful! Your course is now active.');
            } else {
                Log::error('bKash Execution Failed', ['response' => $response]);
                return redirect()->route('enrollments.my-courses')
                    ->with('error', 'Payment execution failed: ' . ($response['statusMessage'] ?? 'Unknown error'));
            }
        }

        return redirect()->route('enrollments.my-courses')
            ->with('error', 'Payment ' . $status);
    }

    /**
     * Finalize the payment in database.
     */
    protected function finalizePayment(array $response)
    {
        $payment = Payment::whereJsonContains('payment_details->paymentID', $response['paymentID'])->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $response['trxID'],
                'payment_details' => array_merge($payment->payment_details ?? [], $response),
            ]);

            $enrollment = Enrollment::find($payment->enrollment_id);
            if ($enrollment) {
                $enrollment->update([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'enrolled_at' => now(),
                ]);
            }
        }
    }
}
