<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;
use App\Helpers\BannerHelper;

$b = Banner::orderBy('id','desc')->first();
if (!$b) {
    echo "NO BANNER\n";
    exit(0);
}
echo "id: {$b->id}\n";
echo "title: {$b->title}\n";
echo "image raw: ".($b->image ?? 'NULL')."\n";
echo "mobile raw: ".($b->mobile_image ?? 'NULL')."\n";
echo "is_active: ".($b->is_active ? '1' : '0')."\n";
echo "computed url: ".(BannerHelper::getBannerImageUrl($b) ?? 'NULL')."\n";

// Also show Storage::disk('public')->exists result for different variants
$variants = [
    $b->image,
    ltrim($b->image ?? '', '/'),
    preg_replace('#^storage/#','', $b->image ?? ''),
    preg_replace('#^public/#','', $b->image ?? ''),
];
foreach (array_unique($variants) as $v) {
    if (!$v) continue;
    echo "exists('{$v}'): ".(Storage::disk('public')->exists($v) ? 'yes' : 'no')."\n";
}
