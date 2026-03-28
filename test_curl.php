<?php

// Teste direto com cURL das credenciais do Melhor Envio
$clientId = '23498';
$clientSecret = 'QeyfPPka9kmLKRNRerIJHDJBXbb61OYwgfu7F6Bj';
$baseUrl = 'https://www.melhorenvio.com.br';

echo "Testando credenciais do Melhor Envio com cURL...\n";
echo "Client ID: $clientId\n";
echo "Environment: Produção\n";
echo "API URL: $baseUrl\n\n";

// Teste 1: OAuth client_credentials
echo "Teste 1: OAuth client_credentials\n";
$ch = curl_init();

$data = [
    'grant_type' => 'client_credentials',
    'scope' => 'shipping-calculate shipping-read'
];

curl_setopt($ch, CURLOPT_URL, $baseUrl . '/oauth/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n\n";

// Teste 2: Verificar URL de autorização
echo "Teste 2: Gerar URL de autorização\n";
$params = [
    'client_id' => $clientId,
    'redirect_uri' => 'https://rosybrown-jackal-637541.hostingersite.com/admin/melhor-envio/callback',
    'response_type' => 'code',
    'state' => 'test123',
    'scope' => 'shipping-calculate shipping-read'
];

$authUrl = $baseUrl . '/oauth/authorize?' . http_build_query($params);
echo "URL de Autorização: $authUrl\n\n";

// Teste 3: Verificar se a URL de autorização funciona
echo "Teste 3: Verificar URL de autorização (HEAD request)\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $authUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n\n";

echo "Teste concluído!\n";
