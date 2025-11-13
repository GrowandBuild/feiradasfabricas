<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = \App\Models\Product::select('id','name','is_active','is_unavailable','in_stock','images','variation_images')
    ->where('name','like','%iPhone%')
    ->orderBy('id')
    ->get();

foreach ($rows as $p) {
    $imgCount = is_array($p->images) ? count($p->images) : (is_string($p->images) ? strlen($p->images) : 0);
    echo $p->id.'|'.$p->name.'|active:'.($p->is_active?'1':'0').' unavailable:'.($p->is_unavailable?'1':'0').' stock:'.($p->in_stock?'1':'0')."\n";
}
