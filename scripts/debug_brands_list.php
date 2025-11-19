<?php
require __DIR__ . '/../vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Now use the controller directly
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request as HttpRequest;

$ctrl = new ProductController();
$slugs = [null, 'eletronicos', 'vestuario-feminino', 'reserva'];
foreach ($slugs as $s) {
    $r = HttpRequest::create('/admin/products/brands-list', 'GET', $s ? ['department' => $s] : []);
    $res = $ctrl->brandsList($r);
    $payload = method_exists($res, 'getContent') ? $res->getContent() : json_encode($res);
    echo "\n--- department=" . ($s ?? '(none)') . " ---\n";
    echo $payload . "\n";
}

$kernel->terminate($request, $response);
