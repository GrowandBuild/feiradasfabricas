<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO AMBIENTE E CONFIGURAÇÕES ===\n\n";

echo "AMBIENTE ATUAL:\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";

echo "\nURL DO SITE EM PRODUÇÃO:\n";
echo "https://rosybrown-jackal-637541.hostingersite.com\n";

echo "\n=== VERIFICANDO SE O PROBLEMA É AMBIENTE ===\n";

// O problema pode ser que estamos em ambiente local (APP_ENV=local)
// mas as credenciais de produção são para o ambiente de produção

echo "Análise:\n";
echo "- Ambiente local: " . config('app.env') . "\n";
echo "- URL local: " . config('app.url') . "\n";
echo "- URL produção: https://rosybrown-jackal-637541.hostingersite.com\n";

echo "\n=== POSSÍVEIS PROBLEMAS ===\n";
echo "1. O ambiente local pode não ter as mesmas credenciais que produção\n";
echo "2. O callback URL pode estar apontando para produção\n";
echo "3. O token pode ter sido gerado em produção mas não existe localmente\n";

echo "\n=== SOLUÇÃO RECOMENDADA ===\n";
echo "1. Acesse o site em produção: https://rosybrown-jackal-637541.hostingersite.com\n";
echo "2. Faça login no admin\n";
echo "3. Vá para Configurações > Envio\n";
echo "4. Verifique se o Melhor Envio está conectado\n";
echo "5. Se estiver, teste uma página de produto lá\n";
echo "6. Se funcionar em produção, o problema é apenas no ambiente local\n";

echo "\n=== TESTANDO CÁLCULO DE FRETE LOCAL ===\n";

// Testar mesmo assim para ver o erro exato
try {
    $request = new Illuminate\Http\Request();
    $request->merge([
        'product_id' => 1,
        'cep' => '72878-012', 
        'quantity' => 1
    ]);

    $shippingController = new App\Http\Controllers\ShippingController();
    $response = $shippingController->quote($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
    
    if ($response->getStatusCode() == 400) {
        $data = json_decode($response->getContent(), true);
        if (isset($data['message'])) {
            echo "\nErro específico: " . $data['message'] . "\n";
            
            if (str_contains($data['message'], 'não configurada')) {
                echo "\nDIAGNÓSTICO: O problema é que não há token de acesso.\n";
                echo "Isso acontece porque:\n";
                echo "- O token foi gerado em produção mas não existe no ambiente local\n";
                echo "- Ou as credenciais locais são diferentes das de produção\n";
                echo "- Ou o fluxo OAuth precisa ser completado no ambiente local\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMENDAÇÃO FINAL ===\n";
echo "NÃO mexa nas credenciais de produção!\n";
echo "Em vez disso:\n";
echo "1. Teste o cálculo de frete diretamente no site de produção\n";
echo "2. Se funcionar lá, o problema é apenas sincronização local\n";
echo "3. Se não funcionar lá também, aí sim precisa verificar as credenciais\n";
echo "4. Para teste local, você pode precisar completar o OAuth no ambiente local\n";

// Limpar arquivos temporários
$files = [
    __DIR__ . '/verificar_producao_completo.php',
    __DIR__ . '/testar_formato_credenciais.php',
    __DIR__ . '/descriptografar_correto.php',
    __DIR__ . '/acessar_credenciais_existentes.php',
    __DIR__ . '/solucao_oauth.php',
    __DIR__ . '/analise_final.php',
    __DIR__ . '/gerar_token.php',
    __DIR__ . '/testar_api_frete.php',
    __DIR__ . '/verificar_producao.php',
    __DIR__ . '/diagnostico_final.php',
    __DIR__ . '/temp_check_product.php',
    __DIR__ . '/temp_test_shipping.php',
    __DIR__ . '/temp_test_melhor_envio.php',
    __DIR__ . '/temp_debug_settings.php',
    __DIR__ . '/descriptografar_credenciais.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

echo "\nArquivos temporários limpos.\n";
