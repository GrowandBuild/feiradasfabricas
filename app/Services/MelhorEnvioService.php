<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

/**
 * Serviço para integração com a API do Melhor Envio
 * Documentação: https://melhorenvio.docs.apiary.io/
 */
class MelhorEnvioService
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $accessToken;
    private ?string $refreshToken;
    private bool $sandbox;

    public function __construct()
    {
        $this->clientId = setting('melhor_envio_client_id');
        $this->clientSecret = setting('melhor_envio_client_secret');
        $this->accessToken = setting('melhor_envio_token');
        $this->refreshToken = setting('melhor_envio_refresh_token');
        $this->sandbox = setting('melhor_envio_sandbox', false);
    }

    /**
     * Obter URL base da API conforme ambiente
     */
    private function getBaseUrl(): string
    {
        return $this->sandbox 
            ? 'https://sandbox.melhorenvio.com.br' 
            : 'https://api.melhorenvio.com.br';
    }

    /**
     * Validar credenciais sem salvar
     */
    public function validateCredentials(string $clientId, string $clientSecret): array
    {
        try {
            Log::info('Tentando validar credenciais', [
                'client_id' => $clientId,
                'client_id_length' => strlen($clientId),
                'has_secret' => !empty($clientSecret),
                'secret_length' => strlen($clientSecret),
                'environment' => $this->sandbox ? 'sandbox' : 'production',
                'api_url' => $this->getBaseUrl()
            ]);

            // Tentar obter informações do usuário autenticado
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $clientId, // Algumas APIs usam Client ID como Bearer
                'User-Agent' => 'EcommerceApp/1.0'
            ])->get($this->getBaseUrl() . '/api/v2/me');

            Log::info('Resposta da API de validação', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'Credenciais válidas',
                    'data' => $data
                ];
            }

            // Se falhou, tentar método alternativo (POST com client_credentials)
            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post($this->getBaseUrl() . '/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'scope' => 'shipping-calculate'
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Credenciais válidas (autenticação OAuth)'
                ];
            }

            return [
                'success' => false,
                'message' => 'Credenciais inválidas ou API indisponível',
                'debug' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao validar credenciais Melhor Envio', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao conectar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Conectar e salvar configurações
     */
    public function connect(string $clientId, string $clientSecret, string $cepOrigem): array
    {
        // Validar primeiro
        $validation = $this->validateCredentials($clientId, $clientSecret);

        if (!$validation['success']) {
            return $validation;
        }

        // Salvar configurações
        Setting::set('melhor_envio_client_id', $clientId, 'string', 'delivery');
        Setting::set('melhor_envio_client_secret', $clientSecret, 'string', 'delivery');
        Setting::set('melhor_envio_cep_origem', $cepOrigem, 'string', 'delivery');
        Setting::set('melhor_envio_connected', true, 'boolean', 'delivery');

        return [
            'success' => true,
            'message' => 'Conectado com sucesso ao Melhor Envio'
        ];
    }

    /**
     * Iniciar fluxo OAuth
     */
    public function getAuthorizationUrl(string $redirectUri): string
    {
        $state = bin2hex(random_bytes(16));
        session(['melhor_envio_oauth_state' => $state]);

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'state' => $state,
            'scope' => 'shipping-calculate'
        ];

        return $this->getBaseUrl() . '/oauth/authorize?' . http_build_query($params);
    }

    /**
     * Trocar código por token
     */
    public function exchangeCodeForToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->getBaseUrl() . '/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Erro ao trocar código por token: ' . $response->body()
                ];
            }

            $data = $response->json();

            // Salvar tokens
            Setting::set('melhor_envio_token', $data['access_token'], 'string', 'delivery');
            Setting::set('melhor_envio_refresh_token', $data['refresh_token'] ?? '', 'string', 'delivery');
            Setting::set('melhor_envio_token_expires_at', now()->addSeconds($data['expires_in'] ?? 3600)->toIso8601String(), 'string', 'delivery');

            return [
                'success' => true,
                'message' => 'Autorizado com sucesso',
                'token' => $data['access_token']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Testar conexão atual
     */
    public function testConnection(): array
    {
        if (empty($this->accessToken)) {
            return [
                'success' => false,
                'message' => 'Não há token de acesso. Conecte-se primeiro.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->getBaseUrl() . '/api/v2/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão OK',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Desconectar - limpar tokens
     */
    public function disconnect(): array
    {
        Setting::set('melhor_envio_token', '', 'string', 'delivery');
        Setting::set('melhor_envio_refresh_token', '', 'string', 'delivery');
        Setting::set('melhor_envio_token_expires_at', '', 'string', 'delivery');
        Setting::set('melhor_envio_connected', false, 'boolean', 'delivery');

        return [
            'success' => true,
            'message' => 'Desconectado com sucesso'
        ];
    }

    /**
     * Calcular frete
     */
    public function calculateShipping(string $cepOrigem, string $cepDestino, array $products): array
    {
        if (empty($this->accessToken)) {
            return [
                'success' => false,
                'message' => 'Token não configurado'
            ];
        }

        try {
            // Normalizar CEPs
            $cepOrigem = preg_replace('/\D/', '', $cepOrigem);
            $cepDestino = preg_replace('/\D/', '', $cepDestino);

            $items = [];
            foreach ($products as $product) {
                $items[] = [
                    'id' => $product['id'] ?? uniqid(),
                    'width' => $product['width'] ?? 11,
                    'height' => $product['height'] ?? 2,
                    'length' => $product['length'] ?? 16,
                    'weight' => $product['weight'] ?? 0.3,
                    'insurance_value' => $product['price'] ?? 1,
                    'quantity' => $product['quantity'] ?? 1
                ];
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken,
                'User-Agent' => 'EcommerceApp/1.0'
            ])->post($this->getBaseUrl() . '/api/v2/me/shipment/calculate', [
                'from' => ['postal_code' => $cepOrigem],
                'to' => ['postal_code' => $cepDestino],
                'products' => $items,
                'options' => [
                    'receipt' => false,
                    'own_hand' => false
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Formatar resposta
                $services = [];
                foreach ($data as $quote) {
                    if (isset($quote['price'])) {
                        $services[] = [
                            'id' => $quote['id'] ?? null,
                            'name' => $quote['name'] ?? 'Serviço ' . ($quote['id'] ?? ''),
                            'price' => $quote['price'],
                            'currency' => $quote['currency'] ?? 'R$',
                            'delivery_time' => $quote['delivery_time'] ?? null,
                            'company' => $quote['company'] ?? null
                        ];
                    }
                }

                return [
                    'success' => true,
                    'services' => $services
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro na cotação: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Melhor Envio', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar se está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Verificar se está conectado (tem token)
     */
    public function isConnected(): bool
    {
        return !empty($this->accessToken);
    }
}
