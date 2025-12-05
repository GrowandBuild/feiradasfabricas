<?php
// Simple script to call controller methods for local inspection
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$productId = isset($argv[1]) ? (int)$argv[1] : null;
$deptId = isset($argv[2]) ? (int)$argv[2] : null;

if (!$productId) {
    echo "Usage: php scripts/check_endpoints.php <productId> [departmentId]\n";
    exit(1);
}

use App\Models\Product;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request;

$product = Product::find($productId);
if (!$product) {
    echo "Product {$productId} not found\n";
    exit(1);
}

$ctrl = new ProductController();

echo "--- getVariations ({$productId}) ---\n";
$res = $ctrl->getVariations($product);
if (method_exists($res, 'getData')) {
    echo json_encode($res->getData(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Non JSON response\n";
}

if ($deptId) {
    echo "--- attributesList (department={$deptId}) ---\n";
    $req = Request::create('/admin/attributes/list', 'GET', ['department' => $deptId]);
    $res2 = $ctrl->attributesList($req);
    if (method_exists($res2, 'getData')) {
        echo json_encode($res2->getData(), JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Non JSON response\n";
    }
} else {
    echo "No departmentId provided; skipping attributesList.\n";
}
