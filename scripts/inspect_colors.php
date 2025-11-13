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

$matches = \App\Models\Product::with(['variations' => function($q){ $q->select('*'); }])
    ->whereHas('variations', function($q) use ($terms) {
        $q->where(function($qq) use ($terms) {
            foreach ($terms as $t) {
                $qq->orWhereRaw('LOWER(color) LIKE ?', ['%'.mb_strtolower($t).'%']);
            }
        });
    })
    ->limit(10)
    ->get(['id','name','slug']);

echo "Found products: ".count($matches)."\n";
foreach ($matches as $p) {
    $vs = $p->variations->pluck('color')->unique()->take(5)->implode(', ');
    echo "#{$p->id} {$p->name} | colors: {$vs}\n";
}
