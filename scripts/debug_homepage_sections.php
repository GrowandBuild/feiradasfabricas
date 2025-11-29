<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HomepageSection;

$sections = HomepageSection::orderBy('position')->get();
if ($sections->count() === 0) {
    echo "No homepage sections found.\n";
    exit(0);
}

foreach ($sections as $s) {
    echo "ID: {$s->id} | Title: {$s->title} | Enabled:" . ($s->enabled ? '1' : '0') . " | Dept:" . ($s->department_id ?: 'null') . " | Limit:" . ($s->limit ?: '(null)') . "\n";
    echo " product_ids: " . json_encode($s->product_ids) . "\n";
    $products = $s->getProducts();
    echo " products count: " . $products->count() . "\n";
    foreach ($products as $p) {
        echo "  - {$p->id} | active:" . ($p->is_active ? '1' : '0') . " | in_stock:" . ($p->in_stock ? '1' : '0') . " | name: {$p->name}\n";
    }
    echo "----\n";
}
