<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$enrollment = \App\Models\Enrollment::where('user_id', $user->id)->first();
if (!$enrollment) {
    $batch = \App\Models\Batch::first();
    $enrollment = \App\Models\Enrollment::create([
        'organization_id' => $batch->organization_id,
        'user_id' => $user->id,
        'batch_id' => $batch->id,
        'enrollment_no' => 'ENR-TEST',
        'price_at_enrollment' => 100,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);
}

$request = \Illuminate\Http\Request::create('/payments/initiate', 'GET', ['enrollment_id' => $enrollment->id]);
$controller = new \App\Http\Controllers\PaymentController();

try {
    $response = $controller->initiate($request);
    echo "SUCCESS: " . get_class($response) . "\n";
    if ($response instanceof \Inertia\Response) {
        $props = $response->toResponse($request)->getOriginalContent()->getData();
        print_r($props);
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
