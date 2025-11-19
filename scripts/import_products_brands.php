<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$distinct = Product::query()
    ->whereNotNull('brand')
    ->where('brand', '<>', '')
    ->distinct()
    ->pluck('brand')
    ->map(function($b){ return trim((string)$b); })
    ->filter()
    ->unique()
    ->values();

echo "Found " . $distinct->count() . " distinct product.brand values\n";

foreach ($distinct as $name) {
    // Skip empty
    if ($name === '') continue;
    // Check if brand exists (case-insensitive)
    $exists = Brand::whereRaw('LOWER(name) = LOWER(?)', [$name])->first();
    if ($exists) {
        echo "Skip existing: {$name} (id={$exists->id})\n";
        continue;
    }

    // Try to find a department_id from a product that uses this brand
    $productWithDept = Product::whereRaw('LOWER(brand) = LOWER(?)', [$name])
        ->whereNotNull('department_id')
        ->where('department_id', '<>', 0)
        ->first();

    $departmentId = $productWithDept ? $productWithDept->department_id : null;

    $brand = Brand::create([
        'name' => $name,
        'slug' => Str::slug($name),
        'department_id' => $departmentId,
    ]);

    echo "Created brand: {$brand->name} (id={$brand->id}) dept_id=" . ($departmentId ?? 'NULL') . "\n";
}

$kernel->terminate($request, $response);
echo "Done.\n";
