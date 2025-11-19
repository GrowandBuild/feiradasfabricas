<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$brands = \App\Models\Brand::pluck('name')->toArray();
echo json_encode($brands, JSON_PRETTY_PRINT) . "\n";

$kernel->terminate($request, $response);
