<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::where('name','like','%max%')->get(['id','name','in_stock','is_unavailable','is_active']);
foreach($products as $p){
    echo "#{$p->id} {$p->name} active={$p->is_active} in_stock={$p->in_stock} unavailable={$p->is_unavailable}\n";
}
