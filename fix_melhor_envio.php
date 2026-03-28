<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CORRIGINDO CONFIGURAÇÃO DO MELHOR ENVIO ===\n\n";

// 1. Resetar status de conexão
echo "1. Resetando status de conexão...\n";
\App\Models\Setting::set('melhor_envio_connected', false, 'boolean', 'delivery');
\App\Models\Setting::set('melhor_envio_token', '', 'string', 'delivery');
\App\Models\Setting::set('melhor_envio_refresh_token', '', 'string', 'delivery');
\App\Models\Setting::set('melhor_envio_token_expires_at', '', 'string', 'delivery');

// 2. Confirmar configurações de produção
echo "2. Configurando ambiente de produção...\n";
\App\Models\Setting::set('melhor_envio_client_id', '23498', 'string', 'delivery');
\App\Models\Setting::set('melhor_envio_sandbox', false, 'boolean', 'delivery');
\App\Models\Setting::set('melhor_envio_enabled', true, 'boolean', 'delivery');

// 3. Verificar configurações atuais
echo "\n3. Verificando configurações atuais:\n";
echo "Client ID: " . \App\Models\Setting::get('melhor_envio_client_id') . "\n";
echo "Client Secret: " . (empty(\App\Models\Setting::get('melhor_envio_client_secret')) ? 'VAZIO' : 'DEFINIDO') . "\n";
echo "Ambiente: " . (\App\Models\Setting::get('melhor_envio_sandbox') ? 'SANDBOX' : 'PRODUÇÃO') . "\n";
echo "Enabled: " . (\App\Models\Setting::get('melhor_envio_enabled') ? 'SIM' : 'NÃO') . "\n";
echo "Connected: " . (\App\Models\Setting::get('melhor_envio_connected') ? 'SIM' : 'NÃO') . "\n";
echo "Token: " . (empty(\App\Models\Setting::get('melhor_envio_token')) ? 'VAZIO' : 'DEFINIDO') . "\n";

echo "\n=== PRÓXIMOS PASSOS ===\n";
echo "1. Acesse o painel admin: /admin/settings/delivery\n";
echo "2. Clique em 'Conectar com Melhor Envio'\n";
echo "3. Faça a autorização na plataforma do Melhor Envio\n";
echo "4. Teste o cálculo de frete novamente\n";

echo "\n=== CONCLUÍDO ===\n";
