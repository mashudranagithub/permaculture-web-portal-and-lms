<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();

// Find the course (we use like to cover potential language/translation wrapping)
$course = \App\Models\Course::where('title->en', 'like', '%Permaculture Design Course%')
    ->orWhere('title->bn', 'like', '%Permaculture Design Course%')
    ->orWhere('title', 'like', '%Permaculture Design Course%')
    ->first();

if (!$course) {
    echo "Falling back to the first available course...\n";
    $course = \App\Models\Course::first();
}

if ($course) {
    $batchIds = $course->batches()->pluck('id');
    
    // We assume the first user is the test student, but we can do it for all users to be safe
    $enrollments = \App\Models\Enrollment::whereIn('batch_id', $batchIds)->get();
        
    if ($enrollments->count() > 0) {
        foreach ($enrollments as $enrollment) {
            $enrollment->update([
                'status' => 'pending',
                'payment_status' => 'pending',
                'enrolled_at' => null,
            ]);
            
            // If the price is 0, make it > 0 so it actually requires payment
            if ($enrollment->price_at_enrollment <= 0) {
                $enrollment->update(['price_at_enrollment' => 5000.00]);
            }
            
            \App\Models\Payment::where('enrollment_id', $enrollment->id)->delete();
            echo "Successfully marked enrollment (ID: {$enrollment->id}) for user {$enrollment->user_id} as unpaid for course: " . $course->translate('title') . "\n";
        }
    } else {
        echo "No enrollments found for this course.\n";
    }
} else {
    echo "No course found in the database.\n";
}
