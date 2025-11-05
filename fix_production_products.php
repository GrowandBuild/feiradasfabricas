<?php

/**
 * Script para corrigir produtos em produção
 * 
 * Este script verifica e corrige produtos que não aparecem na página de eletrônicos:
 * 1. Verifica se têm department_id correto
 * 2. Verifica se estão ativos (is_active = true)
 * 3. Verifica se estão em estoque (in_stock = true)
 * 
 * Execute: php fix_production_products.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Department;

echo "🔍 Verificando produtos em produção...\n\n";

$department = Department::where('slug', 'eletronicos')->first();

if (!$department) {
    echo "❌ Erro: Departamento de eletrônicos não encontrado!\n";
    exit(1);
}

echo "📦 Departamento de eletrônicos encontrado (ID: {$department->id})\n\n";

// Verificar produtos Apple
echo "🍎 Verificando produtos Apple:\n";
$appleProducts = Product::where('brand', 'Apple')->get();
echo "   Total encontrado: {$appleProducts->count()}\n";

$appleFixed = 0;
$appleIssues = [];

foreach ($appleProducts as $product) {
    $issues = [];
    
    if (!$product->department_id || $product->department_id != $department->id) {
        $issues[] = 'department_id incorreto';
        $product->update(['department_id' => $department->id]);
        $appleFixed++;
    }
    
    if (!$product->is_active) {
        $issues[] = 'is_active = false';
        $product->update(['is_active' => true]);
        $appleFixed++;
    }
    
    if (!$product->in_stock) {
        $issues[] = 'in_stock = false';
        $product->update(['in_stock' => true]);
        $appleFixed++;
    }
    
    if (!empty($issues)) {
        $appleIssues[] = "   - {$product->name}: " . implode(', ', $issues) . "\n";
    }
}

if (!empty($appleIssues)) {
    echo "   Produtos corrigidos:\n";
    foreach ($appleIssues as $issue) {
        echo $issue;
    }
}

echo "   ✅ {$appleFixed} produtos Apple corrigidos\n\n";

// Verificar produtos Infinix
echo "📱 Verificando produtos Infinix:\n";
$infinixProducts = Product::where('brand', 'Infinix')->get();
echo "   Total encontrado: {$infinixProducts->count()}\n";

$infinixFixed = 0;
$infinixIssues = [];

foreach ($infinixProducts as $product) {
    $issues = [];
    
    if (!$product->department_id || $product->department_id != $department->id) {
        $issues[] = 'department_id incorreto';
        $product->update(['department_id' => $department->id]);
        $infinixFixed++;
    }
    
    if (!$product->is_active) {
        $issues[] = 'is_active = false';
        $product->update(['is_active' => true]);
        $infinixFixed++;
    }
    
    if (!$product->in_stock) {
        $issues[] = 'in_stock = false';
        $product->update(['in_stock' => true]);
        $infinixFixed++;
    }
    
    if (!empty($issues)) {
        $infinixIssues[] = "   - {$product->name}: " . implode(', ', $issues) . "\n";
    }
}

if (!empty($infinixIssues)) {
    echo "   Produtos corrigidos:\n";
    foreach ($infinixIssues as $issue) {
        echo $issue;
    }
}

echo "   ✅ {$infinixFixed} produtos Infinix corrigidos\n\n";

// Verificar quantos produtos aparecem agora
echo "📊 Verificação final:\n";
$appleVisible = $department->products()
    ->where('brand', 'Apple')
    ->active()
    ->inStock()
    ->count();

$infinixVisible = $department->products()
    ->where('brand', 'Infinix')
    ->active()
    ->inStock()
    ->count();

echo "   Produtos Apple visíveis: {$appleVisible}\n";
echo "   Produtos Infinix visíveis: {$infinixVisible}\n\n";

echo "✅ Correção concluída!\n";
echo "🔄 Atualize a página do site para ver os produtos.\n";

