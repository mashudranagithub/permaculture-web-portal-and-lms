<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Initiate a payment for an enrollment.
     */
    public function initiate(Request $request): Response|RedirectResponse
    {
        $enrollmentId = $request->query('enrollment_id') ?? $request->input('enrollment_id');
        $enrollment = Enrollment::with('batch.course')->findOrFail($enrollmentId);

        // Security check
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($enrollment->payment_status === 'paid') {
            return redirect()->route('enrollments.my-courses')->with('message', 'This enrollment is already paid.');
        }

        return Inertia::render('Payment/Initiate', [
            'enrollment' => [
                'id' => $enrollment->id,
                'no' => $enrollment->enrollment_no,
                'amount' => $enrollment->price_at_enrollment,
                'course_title' => $enrollment->batch->course->translate('title'),
                'batch_title' => $enrollment->batch->translate('title'),
            ]
        ]);
    }

    /**
     * Mock a successful payment (for development).
     */
    public function mockSuccess(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $amount = $enrollment->price_at_enrollment;
        
        $payment = Payment::create([
            'organization_id' => $enrollment->organization_id,
            'user_id' => $enrollment->user_id,
            'batch_id' => $enrollment->batch_id,
            'enrollment_id' => $enrollment->id,
            'amount' => $amount,
            'discount_applied' => 0,
            'net_amount' => $amount,
            'transaction_id' => 'MOCK-' . strtoupper(uniqid()),
            'gateway' => 'mock',
            'status' => 'completed',
            'payment_date' => now(),
            'payment_details' => ['mock' => true, 'timestamp' => now()],
        ]);

        $enrollment->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'enrolled_at' => now(),
        ]);

        return redirect()->route('enrollments.my-courses')
            ->with('message', 'Payment successful! Your enrollment is now active.');
    }
}
