<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Teste direto das credenciais
$clientId = '23498';
$clientSecret = 'QeyfPPka9kmLKRNRerIJHDJBXbb61OYwgfu7F6Bj';
$baseUrl = 'https://www.melhorenvio.com.br';

echo "Testando credenciais do Melhor Envio...\n";
echo "Client ID: $clientId\n";
echo "Environment: Produção\n";
echo "API URL: $baseUrl\n\n";

// Teste 1: OAuth client_credentials
echo "Teste 1: OAuth client_credentials\n";
$response = Http::asForm()
    ->withBasicAuth($clientId, $clientSecret)
    ->post($baseUrl . '/oauth/token', [
        'grant_type' => 'client_credentials',
        'scope' => 'shipping-calculate shipping-read'
    ]);

echo "Status: " . $response->status() . "\n";
echo "Success: " . ($response->successful() ? 'true' : 'false') . "\n";
echo "Response: " . $response->body() . "\n\n";

// Teste 2: Verificar se o app existe
echo "Teste 2: Verificar informações do usuário\n";
$response2 = Http::withHeaders([
    'Accept' => 'application/json',
    'Authorization' => 'Bearer ' . $clientId,
])->get($baseUrl . '/api/v2/me');

echo "Status: " . $response2->status() . "\n";
echo "Success: " . ($response2->successful() ? 'true' : 'false') . "\n";
echo "Response: " . $response2->body() . "\n\n";

echo "Teste concluído!\n";
