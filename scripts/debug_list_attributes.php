<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attribute;

$attrs = Attribute::with('values')->get();
if ($attrs->isEmpty()) {
    echo "Nenhum atributo encontrado\n";
    exit(0);
}

foreach ($attrs as $a) {
    echo "ID: {$a->id} | Name: {$a->name} | Key: {$a->key} | Dept: {$a->department_id} | Active: " . ($a->is_active ? '1' : '0') . "\n";
    if ($a->values->isEmpty()) {
        echo "  (sem valores)\n";
    } else {
        foreach ($a->values as $v) {
            echo "  - Value ID: {$v->id} | value: {$v->value} | hex: {$v->hex} | active: " . ($v->is_active ? '1' : '0') . "\n";
        }
    }
}
