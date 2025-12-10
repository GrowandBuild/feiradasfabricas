<?php
use Illuminate\Support\Str;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$color = $argv[1] ?? 'preto';
$synonyms = [
    'preto' => ['preto','black','grafite','graphite','black titanium','titanium black'],
    'branco' => ['branco','white','starlight','luz das estrelas','white titanium'],
    'azul' => ['azul','blue','sierra blue','pacific blue','blue titanium'],
    'rosa' => ['rosa','pink','rose','rose gold'],
];
$terms = $synonyms[$color] ?? [$color];

// Sistema de variações não implementado - script desabilitado
// Busca por cores nos nomes dos produtos como alternativa
$matches = \App\Models\Product::where(function($q) use ($terms) {
        foreach ($terms as $t) {
            $q->orWhereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($t).'%']);
        }
    })
    ->limit(10)
    ->get(['id','name','slug']);

echo "Found products: ".count($matches)."\n";
foreach ($matches as $p) {
    echo "#{$p->id} {$p->name}\n";
}
