<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$enrollment = \App\Models\Enrollment::where('user_id', $user->id)->first();
$request = \Illuminate\Http\Request::create('/payments/'.$enrollment->id.'/mock-success', 'POST');
$controller = new \App\Http\Controllers\PaymentController();

try {
    $response = $controller->mockSuccess($request, $enrollment);
    echo "SUCCESS: " . get_class($response) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
