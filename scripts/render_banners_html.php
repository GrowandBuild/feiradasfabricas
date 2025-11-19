<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\BannerHelper;
use App\Models\Banner;

// get global hero banners
$banners = Banner::active()->where('position','hero')->get();

// render the component view directly
echo view('components.banner-slider', [
    'departmentId' => null,
    'position' => 'hero',
    'limit' => 5,
    'showDots' => false,
    'showArrows' => false,
    'autoplay' => false,
    'interval' => 5000,
])->render();
