<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\SSLCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SSLCommerzController extends Controller
{
    protected SSLCommerzService $sslService;

    public function __construct(SSLCommerzService $sslService)
    {
        $this->sslService = $sslService;
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
        $this->sslService->useOrganizationConfig($enrollment->organization);

        $tranId = 'SSL-' . $enrollment->id . '-' . time();
        
        $paymentData = [
            'total_amount' => $enrollment->price_at_enrollment,
            'tran_id' => $tranId,
            'cus_name' => Auth::user()->name,
            'cus_phone' => Auth::user()->phone ?? '01XXXXXXXXX',
            'cus_email' => Auth::user()->email,
            'product_name' => $enrollment->batch->course->translate('title'),
            'enrollment_id' => $enrollment->id,
        ];

        $response = $this->sslService->initiatePayment($paymentData);

        if ($response && isset($response['GatewayPageURL'])) {
            // Create a pending payment record
            Payment::updateOrCreate(
                ['enrollment_id' => $enrollment->id, 'status' => 'pending'],
                [
                    'organization_id' => $enrollment->organization_id,
                    'user_id' => $enrollment->user_id,
                    'batch_id' => $enrollment->batch_id,
                    'amount' => $enrollment->price_at_enrollment,
                    'net_amount' => $enrollment->price_at_enrollment,
                    'gateway' => 'sslcommerz',
                    'transaction_id' => $tranId,
                    'payment_details' => ['sessionkey' => $response['sessionkey']],
                ]
            );

            return Inertia::location($response['GatewayPageURL']);
        }

        return back()->with('error', 'Failed to initiate SSLCommerz payment.');
    }

    /**
     * Success Callback.
     */
    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $payment = Payment::withoutGlobalScopes()->where('transaction_id', $tranId)->first();
        if (!$payment) {
            return redirect()->route('enrollments.my-courses')->with('error', 'Payment record not found.');
        }

        // Validate payment
        $this->sslService->useOrganizationConfig($payment->organization);
        $validation = $this->sslService->validatePayment($valId);

        if ($validation && $validation['status'] === 'VALID') {
            $this->finalizePayment($payment, $validation);
            return redirect()->route('enrollments.my-courses')
                ->with('message', 'Payment successful! Your course is now active.');
        }

        Log::error('SSLCommerz Validation Failed', ['response' => $validation]);
        return redirect()->route('enrollments.my-courses')
            ->with('error', 'Payment validation failed.');
    }

    /**
     * Fail Callback.
     */
    public function fail(Request $request)
    {
        return redirect()->route('enrollments.my-courses')
            ->with('error', 'Payment failed.');
    }

    /**
     * Cancel Callback.
     */
    public function cancel(Request $request)
    {
        return redirect()->route('enrollments.my-courses')
            ->with('error', 'Payment cancelled.');
    }

    /**
     * IPN Callback.
     */
    public function ipn(Request $request)
    {
        Log::info('SSLCommerz IPN Received', $request->all());
        // Handle IPN logic here if needed (similar to success validation)
    }

    /**
     * Finalize the payment in database.
     */
    protected function finalizePayment(Payment $payment, array $response)
    {
        $payment->update([
            'status' => 'completed',
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
