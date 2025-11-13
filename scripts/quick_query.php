<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$color = $argv[1] ?? 'preto';
$colors = ['preto','black','grafite','graphite','black titanium','titanium black'];

$db = $app->make('db');
$q = $db->table('products')
    ->leftJoin('product_variations as pv', 'pv.product_id', '=', 'products.id')
    ->where('products.is_unavailable', false)
    ->where(function($q) use ($colors) {
        foreach ($colors as $c) {
            $q->orWhereRaw('LOWER(pv.color) LIKE ?', ['%'.mb_strtolower($c).'%']);
        }
    })
    ->selectRaw('count(distinct products.id) as cnt')
    ->first();

var_dump($q);
