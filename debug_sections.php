<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== DEBUG SECTIONS ===\n";

// 1. Verificar seções existem
$sections = \App\Models\HomepageSection::all();
echo "Total de seções: " . $sections->count() . "\n";

foreach ($sections as $section) {
    echo "- ID: {$section->id} | Título: {$section->title} | Enabled: " . ($section->enabled ? 'Sim' : 'Não') . " | Dept ID: " . ($section->department_id ?: 'NULL') . "\n";
}

echo "\n=== DEBUG PRODUTOS COM SEÇÕES ===\n";

// 2. Verificar produtos com seções
$productsWithSections = \App\Models\Product::whereNotNull('homepage_section_ids')->get();
echo "Produtos com seções: " . $productsWithSections->count() . "\n";

foreach ($productsWithSections as $product) {
    echo "- ID: {$product->id} | Nome: {$product->name} | Seções: " . json_encode($product->homepage_section_ids) . "\n";
}

echo "\n=== TESTE GET PRODUCTS ===\n";

// 3. Testar getProducts para cada seção
foreach ($sections as $section) {
    echo "\nSeção: {$section->title}\n";
    $products = $section->getProducts();
    echo "Produtos encontrados: " . $products->count() . "\n";
    
    foreach ($products as $product) {
        echo "  - ID: {$product->id} | Nome: {$product->name} | Ativo: " . ($product->is_active ? 'Sim' : 'Não') . " | Estoque: " . ($product->in_stock ? 'Sim' : 'Não') . " | Disponível: " . ($product->is_unavailable ? 'Não' : 'Sim') . "\n";
    }
}

echo "\n=== VERIFICAÇÃO MANUAL DOS PRODUTOS COM SEÇÕES ===\n";

foreach ($productsWithSections as $product) {
    echo "Produto: {$product->name} (ID: {$product->id})\n";
    echo "  - is_active: " . ($product->is_active ? 'Sim' : 'Não') . "\n";
    echo "  - in_stock: " . ($product->in_stock ? 'Sim' : 'Não') . "\n";
    echo "  - is_unavailable: " . ($product->is_unavailable ? 'Sim' : 'Não') . "\n";
    echo "  - homepage_section_ids: " . json_encode($product->homepage_section_ids) . "\n";
    echo "\n";
}

echo "\n=== VERIFICAÇÃO ESPECÍFICA DO PRODUTO 'ENV' ===\n";

$envProduct = \App\Models\Product::where('slug', 'env')->first();
if ($envProduct) {
    echo "Produto ENV encontrado:\n";
    echo "  - ID: {$envProduct->id}\n";
    echo "  - Nome: {$envProduct->name}\n";
    echo "  - Slug: {$envProduct->slug}\n";
    echo "  - is_active: " . ($envProduct->is_active ? 'Sim' : 'Não') . "\n";
    echo "  - in_stock: " . ($envProduct->in_stock ? 'Sim' : 'Não') . "\n";
    echo "  - is_unavailable: " . ($envProduct->is_unavailable ? 'Sim' : 'Não') . "\n";
    echo "  - homepage_section_ids: " . json_encode($envProduct->homepage_section_ids) . "\n";
} else {
    echo "Produto 'env' não encontrado\n";
}

echo "\n=== TESTE DIRETO DA QUERY ===\n";

// Testar a query que deveria encontrar o produto .env
$sectionId = 1; // Seção Faca
$directQuery = \App\Models\Product::where(function($q) use ($sectionId) {
    $q->whereNotNull('homepage_section_ids')
      ->whereJsonContains('homepage_section_ids', $sectionId);
})
->where('is_active', true)
->where('in_stock', true);

echo "Query SQL: " . $directQuery->toSql() . "\n";
echo "Bindings: " . json_encode($directQuery->getBindings()) . "\n";

$directResults = $directQuery->get(['id', 'name', 'homepage_section_ids']);
echo "Resultados da query direta: " . $directResults->count() . "\n";

foreach ($directResults as $product) {
    echo "  - ID: {$product->id} | Nome: {$product->name} | Seções: " . json_encode($product->homepage_section_ids) . "\n";
}

echo "\n=== VERIFICAÇÃO DO LIMIT DA SEÇÃO ===\n";

$facaSection = \App\Models\HomepageSection::find(1);
if ($facaSection) {
    echo "Seção Faca (ID: 1):\n";
    echo "  - Título: {$facaSection->title}\n";
    echo "  - Limit: " . ($facaSection->limit ?? 'NULL (default 4)') . "\n";
    echo "  - product_ids: " . json_encode($facaSection->product_ids) . "\n";
    echo "  - department_id: " . ($facaSection->department_id ?? 'NULL') . "\n";
    
    // Testar getProducts sem limit
    echo "\nTeste getProducts() original:\n";
    $productsWithLimit = $facaSection->getProducts();
    echo "  - Produtos encontrados: " . $productsWithLimit->count() . "\n";
    
    // Simular getProducts sem limit
    echo "\nSimulação sem limit:\n";
    $productsWithoutLimit = \App\Models\Product::where(function($q) use ($facaSection) {
        $q->whereNotNull('homepage_section_ids')
          ->whereJsonContains('homepage_section_ids', $facaSection->id);
    })
    ->where('is_active', true)
    ->where('in_stock', true)
    ->with(['variations' => function($q) {
        $q->where('in_stock', true);
    }])
    ->get();
    
    echo "  - Produtos encontrados (sem limit): " . $productsWithoutLimit->count() . "\n";
    foreach ($productsWithoutLimit as $product) {
        echo "    * ID: {$product->id} | Nome: {$product->name}\n";
    }
}

echo "\n=== CONDIÇÕES DA HOME ===\n";

// 4. Simular condição da home
$homeDept = \App\Models\Department::where('slug', 'eletronicos')->first();
echo "Departamento eletronicos: " . ($homeDept ? 'Sim (ID: ' . $homeDept->id . ')' : 'Não') . "\n";

$homepageSectionsQuery = \App\Models\HomepageSection::where('enabled', true);
if ($homeDept) {
    $homepageSectionsQuery->where(function($q) use ($homeDept) {
        $q->whereNull('department_id')
          ->orWhere('department_id', $homeDept->id);
    });
} else {
    $homepageSectionsQuery->whereNull('department_id');
}

$homepageSections = $homepageSectionsQuery->orderBy('position')->get();
echo "Seções que aparecerão na home: " . $homepageSections->count() . "\n";

foreach ($homepageSections as $section) {
    echo "- {$section->title}\n";
}
