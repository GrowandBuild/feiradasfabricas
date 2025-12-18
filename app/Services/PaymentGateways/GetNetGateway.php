<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetNetGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        try {
            $clientId = $this->config['client_id'] ?? setting('getnet_client_id');
            $clientSecret = $this->config['client_secret'] ?? setting('getnet_client_secret');
            $environment = $this->config['environment'] ?? setting('getnet_environment', 'sandbox');
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.getnet.com.br'
                : 'https://api-sandbox.getnet.com.br';

            // Obter token de acesso
            $tokenResponse = Http::asForm()->post("{$baseUrl}/auth/oauth/v2/token", [
                'scope' => 'oob',
                'grant_type' => 'client_credentials',
            ])->withBasicAuth($clientId, $clientSecret);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Erro ao obter token GetNet');
            }

            $token = $tokenResponse->json()['access_token'];

            // Processar pagamento
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/v1/payments/credit", [
                'seller_id' => $this->config['seller_id'] ?? setting('getnet_seller_id'),
                'amount' => (int)($amount * 100),
                'currency' => 'BRL',
                'order' => [
                    'order_id' => $data['sale_id'] ?? uniqid(),
                ],
                'credit' => [
                    'delayed' => false,
                    'authenticated' => false,
                    'number_of_installments' => $data['installments'] ?? 1,
                    'soft_descriptor' => 'PDV',
                    'card' => [
                        'number_token' => $data['card_token'] ?? '',
                        'cardholder_name' => $data['card_holder'] ?? '',
                        'expiration_month' => substr($data['card_expiration'] ?? '', 0, 2),
                        'expiration_year' => '20' . substr($data['card_expiration'] ?? '', 2, 2),
                    ],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['payment_id'],
                    'status' => $result['status'],
                ];
            }

            return ['success' => false, 'error' => 'Erro ao processar pagamento'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function confirmPayment(string $transactionId): array
    {
        return ['success' => true];
    }

    public function cancelPayment(string $transactionId): array
    {
        return ['success' => false, 'error' => 'Não implementado'];
    }

    public function getStatus(string $transactionId): array
    {
        return ['success' => false, 'error' => 'Não implementado'];
    }
}


