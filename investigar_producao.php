<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INVESTIGANDO PROBLEMA EM PRODUÇÃO ===\n\n";

echo "SINTOMAS:\n";
echo "- Melhor Envio está autorizado em produção\n";
echo "- Mas página de produto mostra 'Cálculo de frete indisponível no momento'\n";
echo "- CEP: 72878-012 | Quantidade: 1\n\n";

echo "POSSÍVEIS CAUSAS:\n";
echo "1. O endpoint /shipping/quote não está encontrando as credenciais\n";
echo "2. O token expirou e precisa ser renovado\n";
echo "3. O JavaScript da página não está chamando a API corretamente\n";
echo "4. O produto não tem dimensões configuradas\n";
echo "5. O ambiente de produção está usando APP_ENV=local\n\n";

echo "=== VERIFICANDO CONFIGURAÇÕES ATUAIS ===\n";

// Verificar se as credenciais existem
$clientId = setting('melhor_envio_client_id');
$clientSecret = setting('melhor_envio_client_secret');
$token = setting('melhor_envio_token');
$connected = setting('melhor_envio_connected', false);
$enabled = setting('melhor_envio_enabled', false);

echo "Client ID: " . ($clientId ?: 'NULL') . "\n";
echo "Client Secret: " . ($clientSecret ? 'SET' : 'NULL') . "\n";
echo "Token: " . ($token ? substr($token, 0, 20) . '...' : 'NULL') . "\n";
echo "Connected: " . ($connected ? 'YES' : 'NO') . "\n";
echo "Enabled: " . ($enabled ? 'YES' : 'NO') . "\n";

if (empty($token)) {
    echo "\n❌ PROBLEMA ENCONTRADO: Não há token de acesso!\n";
    echo "Mesmo que o sistema diga 'conectado', não há token para fazer requisições.\n";
    
    echo "\n=== SOLUÇÃO: GERAR TOKEN AUTOMATICAMENTE ===\n";
    
    if (!empty($clientId) && !empty($clientSecret)) {
        echo "Tentando gerar token com as credenciais existentes...\n";
        
        try {
            // Tentar descriptografar o client secret
            $secretRecord = App\Models\Setting::where('key', 'melhor_envio_client_secret')->first();
            $decryptedSecret = null;
            
            if ($secretRecord) {
                try {
                    $decryptedSecret = decrypt($secretRecord->value);
                    echo "✅ Client Secret descriptografado\n";
                } catch (Exception $e) {
                    echo "❌ Erro ao descriptografar Client Secret\n";
                    echo "Precisa atualizar as credenciais no painel admin\n";
                    exit;
                }
            }
            
            if ($decryptedSecret) {
                // Gerar token
                $sandbox = setting('melhor_envio_sandbox', false);
                $baseUrl = $sandbox ? 'https://sandbox.melhorenvio.com.br' : 'https://www.melhorenvio.com.br';
                
                echo "Gerando token para ambiente: " . ($sandbox ? 'SANDBOX' : 'PRODUÇÃO') . "\n";
                
                $response = Illuminate\Support\Facades\Http::asForm()
                    ->withBasicAuth($clientId, $decryptedSecret)
                    ->post($baseUrl . '/oauth/token', [
                        'grant_type' => 'client_credentials',
                        'scope' => 'shipping-calculate'
                    ]);
                
                echo "Response: " . $response->status() . "\n";
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['access_token'])) {
                        // Salvar o token
                        App\Models\Setting::set('melhor_envio_token', $data['access_token'], 'string', 'delivery');
                        if (!empty($data['refresh_token'])) {
                            App\Models\Setting::set('melhor_envio_refresh_token', $data['refresh_token'], 'string', 'delivery');
                        }
                        if (!empty($data['expires_in'])) {
                            App\Models\Setting::set('melhor_envio_token_expires_at', now()->addSeconds((int)$data['expires_in'])->toIso8601String(), 'string', 'delivery');
                        }
                        
                        echo "\n🎉 TOKEN GERADO COM SUCESSO!\n";
                        echo "Token salvo no banco de dados\n";
                        
                        // Testar imediatamente
                        echo "\n=== TESTANDO CÁLCULO DE FRETE ===\n";
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
                        
                        if ($response->getStatusCode() == 200) {
                            echo "\n✅ SUCESSO! O cálculo de frete agora funciona!\n";
                            echo "A página do produto deve mostrar as opções de frete.\n";
                        } else {
                            echo "\n❌ Ainda há problemas. Verificando outras causas...\n";
                        }
                    }
                } else {
                    echo "❌ Falha ao gerar token: " . $response->body() . "\n";
                    echo "Possíveis causas:\n";
                    echo "- Credenciais inválidas\n";
                    echo "- Problema com a conta do Melhor Envio\n";
                    echo "- Ambiente incorreto (sandbox vs produção)\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ ERRO: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "\n✅ Token encontrado. Verificando se está válido...\n";
    
    $service = new App\Services\MelhorEnvioService();
    $result = $service->testConnection();
    
    echo "Teste de conexão: " . ($result['success'] ? 'SUCESSO' : 'FALHA') . "\n";
    if (!$result['success']) {
        echo "Erro: " . $result['message'] . "\n";
        
        if (str_contains($result['message'], 'expirado') || str_contains($result['message'], 'inválido')) {
            echo "\n❌ Token expirou! Precisa renovar.\n";
            echo "Solução: Gerar novo token automaticamente (mesmo processo acima)\n";
        }
    }
}
