<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

$publicDisk = Storage::disk('public');
$base = public_path('storage');
echo "public storage base: $base\n";

$id = $argv[1] ?? null;

if ($id) {
    $banner = Banner::find((int) $id);
    if (!$banner) { echo "Banner not found\n"; exit(1);} 
    $banners = collect([$banner]);
} else {
    $banners = Banner::orderBy('id')->get();
}

$total = 0; $missing = 0;

foreach ($banners as $b) {
    $total++;
    echo "\n# Banner {$b->id} - {$b->title} (active: " . ($b->is_active ? 'yes' : 'no') . ")\n";

    // Desktop image
    if ($b->image) {
        $url = $publicDisk->url($b->image);
        $path = $publicDisk->path($b->image);
        $existsDisk = $publicDisk->exists($b->image) ? 'YES' : 'NO';
        $existsFs = file_exists($path) ? 'YES' : 'NO';
        echo " desktop: {$b->image}\n  url: $url\n  diskPath: $path\n  exists(disk): $existsDisk exists(fs): $existsFs\n";
        if ($existsDisk === 'NO' || $existsFs === 'NO') { $missing++; }
    } else {
        echo " desktop: (none)\n";
    }

    // Mobile image
    if ($b->mobile_image) {
        $urlM = $publicDisk->url($b->mobile_image);
        $pathM = $publicDisk->path($b->mobile_image);
        $existsDiskM = $publicDisk->exists($b->mobile_image) ? 'YES' : 'NO';
        $existsFsM = file_exists($pathM) ? 'YES' : 'NO';
        echo " mobile: {$b->mobile_image}\n  url: $urlM\n  diskPath: $pathM\n  exists(disk): $existsDiskM exists(fs): $existsFsM\n";
        if ($existsDiskM === 'NO' || $existsFsM === 'NO') { $missing++; }
    } else {
        echo " mobile: (none)\n";
    }
}

echo "\nSummary: $total banner(s) checked, $missing missing file reference(s).\n";
