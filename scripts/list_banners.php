<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;

$banners = Banner::active()
    ->where('position', 'hero')
    ->get([
        'id','title','description','link','show_title','show_description','show_overlay',
        'show_primary_button_desktop','show_primary_button_mobile',
        'show_secondary_button_desktop','show_secondary_button_mobile'
    ])
    ->toArray();

echo json_encode($banners, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
