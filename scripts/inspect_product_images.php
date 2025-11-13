<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

$id = (int)($argv[1] ?? 1);
$product = Product::find($id);
if (!$product) { echo "Product not found\n"; exit(1);} 

echo "Product #{$product->id} {$product->name}\n";
echo "images raw: ".json_encode($product->getAttribute('images'))."\n";
echo "all_images: ".json_encode($product->all_images, JSON_UNESCAPED_SLASHES)."\n";
echo "first_image: {$product->first_image}\n";

$publicDisk = Storage::disk('public');
$base = public_path('storage');
echo "public storage path: $base\n";

$images = $product->getAttribute('images') ?? [];
foreach ($images as $img) {
    $url = $publicDisk->url($img);
    $path = $publicDisk->path($img);
    $existsDisk = $publicDisk->exists($img) ? 'YES' : 'NO';
    $existsFs = file_exists($path) ? 'YES' : 'NO';
    echo "- img: $img\n  url: $url\n  diskPath: $path\n  exists(disk): $existsDisk exists(fs): $existsFs\n";
}
