<?php
// Quick script to hit the live search endpoint inside the Laravel app
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$q = $argv[1] ?? 'preto';

$request = Request::create('/api/search/live', 'GET', ['q' => $q]);
$response = $kernel->handle($request);

http_response_code($response->getStatusCode());

echo $response->getContent();

$kernel->terminate($request, $response);
