<?php

// Script para debug das configurações do Melhor Envio
require_once 'bootstrap/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Carregar as settings
$clientId = \App\Helpers\Setting::get('melhor_envio_client_id', 'NÃO DEFINIDO');
$clientSecret = \App\Helpers\Setting::get('melhor_envio_client_secret', 'NÃO DEFINIDO');
$sandbox = \App\Helpers\Setting::get('melhor_envio_sandbox', false);
$connected = \App\Helpers\Setting::get('melhor_envio_connected', false);

echo "=== DEBUG DAS CONFIGURAÇÕES DO MELHOR ENVIO ===\n\n";
echo "Client ID: " . $clientId . "\n";
echo "Client Secret: " . (empty($clientSecret) ? 'VAZIO' : 'DEFINIDO (' . strlen($clientSecret) . ' caracteres)') . "\n";
echo "Ambiente: " . ($sandbox ? 'SANDBOX' : 'PRODUÇÃO') . "\n";
echo "Status Conexão: " . ($connected ? 'CONECTADO' : 'NÃO CONECTADO') . "\n";

if ($clientId === '23498') {
    echo "\n✅ Client ID correto para produção\n";
} elseif ($clientId === '23495') {
    echo "\n❌ Client ID do sandbox (não funciona em produção)\n";
} else {
    echo "\n❓ Client ID desconhecido\n";
}

echo "\n=== URL DE CALLBACK CONFIGURADA ===\n";
echo "Callback URL: https://rosybrown-jackal-637541.hostingersite.com/admin/melhor-envio/callback\n";

echo "\n=== RECOMENDAÇÕES ===\n";
if ($clientId !== '23498') {
    echo "- Atualize o Client ID para 23498\n";
}
if (empty($clientSecret)) {
    echo "- O Client Secret está vazio\n";
}
if ($sandbox) {
    echo "- Desmarque o modo sandbox para usar produção\n";
}

echo "\n=== FIM DO DEBUG ===\n";
