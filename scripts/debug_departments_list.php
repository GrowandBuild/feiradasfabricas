<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$depts = \App\Models\Department::select('id','slug','name')->orderBy('id')->get();
echo json_encode($depts->toArray(), JSON_PRETTY_PRINT) . "\n";

$kernel->terminate($request, $response);
