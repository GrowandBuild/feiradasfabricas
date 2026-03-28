<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANÁLISE DA PÁGINA DO PRODUTO ===\n\n";

echo "URL: https://rosybrown-jackal-637541.hostingersite.com/produto/body-suplex-com-bojo\n";
echo "Produto: Body Suplex com bojo\n\n";

echo "OBSERVAÇÕES:\n";
echo "1. A página carrega e mostra o produto\n";
echo "2. A seção de cálculo de frete aparece (🚚 CALCULE O FRETE)\n";
echo "3. Tem as abas 'Entrega Local' e 'Correios'\n";
echo "4. Mas não consigo ver o formulário completo de CEP\n\n";

echo "=== VERIFICANDO SE HÁ ERROS NO JAVASCRIPT ===\n";

// Vou verificar se há algum problema no JavaScript da página
echo "Possíveis problemas:\n";
echo "1. O JavaScript da calculadora não está carregando\n";
echo "2. O formulário de CEP está oculto ou não renderizando\n";
echo "3. Há erro de JavaScript impedindo o funcionamento\n";
echo "4. O endpoint /shipping/quote está retornando erro\n\n";

echo "=== TESTANDO ENDPOINT DIRETAMENTE ===\n";

// Simular uma requisição como se fosse do JavaScript
try {
    $request = new Illuminate\Http\Request();
    $request->merge([
        'product_id' => 1, // Usar um produto existente
        'cep' => '72878-012',
        'quantity' => 1
    ]);

    $shippingController = new App\Http\Controllers\ShippingController();
    $response = $shippingController->quote($request);
    
    echo "Teste local do endpoint /shipping/quote:\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICANDO PRODUTO ESPECÍFICO ===\n";

// Tentar encontrar o produto "body-suplex-com-bojo"
$product = App\Models\Product::where('slug', 'body-suplex-com-bojo')->first();

if ($product) {
    echo "Produto encontrado:\n";
    echo "ID: " . $product->id . "\n";
    echo "Nome: " . $product->name . "\n";
    echo "Preço: R$ " . number_format($product->price, 2, ',', '.') . "\n";
    echo "Estoque: " . $product->stock_quantity . "\n";
    echo "Peso: " . ($product->weight ?: 'N/A') . "\n";
    echo "Dimensões: " . ($product->length ?: 'N/A') . "x" . ($product->width ?: 'N/A') . "x" . ($product->height ?: 'N/A') . "\n";
    
    // Testar com este produto
    echo "\nTestando cálculo de frete com este produto:\n";
    try {
        $request = new Illuminate\Http\Request();
        $request->merge([
            'product_id' => $product->id,
            'cep' => '72878-012',
            'quantity' => 1
        ]);

        $shippingController = new App\Http\Controllers\ShippingController();
        $response = $shippingController->quote($request);
        
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Response: " . $response->getContent() . "\n";
        
    } catch (Exception $e) {
        echo "ERRO: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "Produto 'body-suplex-com-bojo' não encontrado no banco local\n";
    echo "Isso explica por que o formulário pode não estar funcionando\n";
    
    // Listar produtos disponíveis
    echo "\nProdutos disponíveis no banco local:\n";
    $products = App\Models\Product::where('stock_quantity', '>', 0)->limit(5)->get();
    foreach ($products as $p) {
        echo "- ID: {$p->id} | Nome: {$p->name} | Slug: {$p->slug}\n";
    }
}

echo "\n=== DIAGNÓSTICO ===\n";
echo "Com base na análise:\n";
echo "1. A página em produção está carregando\n";
echo "2. A calculadora de frete aparece visualmente\n";
echo "3. Mas o formulário pode não estar funcional devido a:\n";
echo "   - Produto não encontrado no banco\n";
echo "   - Problemas com as credenciais do Melhor Envio\n";
echo "   - Erros de JavaScript na página\n";
echo "   - Endpoint retornando erro\n\n";

echo "=== RECOMENDAÇÕES ===\n";
echo "1. Verifique no console do navegador (F12) se há erros de JavaScript\n";
echo "2. Verifique se o produto existe no banco de dados de produção\n";
echo "3. Teste o endpoint /shipping/quote diretamente\n";
echo "4. Verifique as credenciais do Melhor Envio no admin de produção\n";
