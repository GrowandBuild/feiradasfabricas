<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the kernel
$console = $app->make(Illuminate\Contracts\Console\Kernel::class);
$console->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

// Example payload
$payloadData = [
    'id' => 'evt_test_'.uniqid(),
    'type' => 'payment_intent.succeeded',
    'data' => [
        'object' => [
            'id' => 'pi_test_'.uniqid(),
            'amount' => 1000,
            'currency' => 'brl'
        ]
    ]
];

$payload = json_encode($payloadData);
$timestamp = time();

// Read secret from settings
$secret = \App\Models\Setting::get('stripe_webhook_secret');
if (!$secret) {
    echo "Stripe webhook secret not configured.\n";
    exit(1);
}

$sig = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
$header = "t={$timestamp},v1={$sig}";

// Create request
$server = [
    'HTTP_STRIPE_SIGNATURE' => $header,
    'CONTENT_TYPE' => 'application/json',
];

$request = Request::create('/payment/stripe/webhook', 'POST', [], [], [], $server, $payload);

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo (string) $response->getContent() . "\n";

// Print recent log tail
$log = file_exists(__DIR__ . '/../storage/logs/laravel.log') ? file_get_contents(__DIR__ . '/../storage/logs/laravel.log') : '';
echo "\n--- Recent log tail ---\n";
echo substr($log, -2000) . "\n";

$kernel->terminate($request, $response);
