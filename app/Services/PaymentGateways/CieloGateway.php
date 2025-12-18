<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CieloGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        try {
            $merchantId = $this->config['merchant_id'] ?? setting('cielo_merchant_id');
            $merchantKey = $this->config['merchant_key'] ?? setting('cielo_merchant_key');
            $environment = $this->config['environment'] ?? setting('cielo_environment', 'sandbox');
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.cieloecommerce.cielo.com.br'
                : 'https://apisandbox.cieloecommerce.cielo.com.br';

            $response = Http::withHeaders([
                'MerchantId' => $merchantId,
                'MerchantKey' => $merchantKey,
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/1/sales", [
                'MerchantOrderId' => $data['sale_id'] ?? uniqid(),
                'Payment' => [
                    'Type' => 'CreditCard',
                    'Amount' => (int)($amount * 100), // Cielo usa centavos
                    'Installments' => $data['installments'] ?? 1,
                    'CreditCard' => [
                        'CardNumber' => $data['card_number'] ?? '',
                        'Holder' => $data['card_holder'] ?? '',
                        'ExpirationDate' => $data['card_expiration'] ?? '',
                        'SecurityCode' => $data['card_cvv'] ?? '',
                        'Brand' => $data['card_brand'] ?? 'Visa',
                    ],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['Payment']['PaymentId'],
                    'status' => $result['Payment']['Status'],
                    'tid' => $result['Payment']['Tid'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['Message'] ?? 'Erro ao processar pagamento',
            ];

        } catch (\Exception $e) {
            Log::error('Cielo Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function confirmPayment(string $transactionId): array
    {
        // Cielo confirma automaticamente
        return ['success' => true];
    }

    public function cancelPayment(string $transactionId): array
    {
        try {
            $merchantId = $this->config['merchant_id'] ?? setting('cielo_merchant_id');
            $merchantKey = $this->config['merchant_key'] ?? setting('cielo_merchant_key');
            $environment = $this->config['environment'] ?? setting('cielo_environment', 'sandbox');
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.cieloecommerce.cielo.com.br'
                : 'https://apisandbox.cieloecommerce.cielo.com.br';

            $response = Http::withHeaders([
                'MerchantId' => $merchantId,
                'MerchantKey' => $merchantKey,
            ])->put("{$baseUrl}/1/sales/{$transactionId}/void");

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getStatus(string $transactionId): array
    {
        try {
            $merchantId = $this->config['merchant_id'] ?? setting('cielo_merchant_id');
            $merchantKey = $this->config['merchant_key'] ?? setting('cielo_merchant_key');
            $environment = $this->config['environment'] ?? setting('cielo_environment', 'sandbox');
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.cieloecommerce.cielo.com.br'
                : 'https://apisandbox.cieloecommerce.cielo.com.br';

            $response = Http::withHeaders([
                'MerchantId' => $merchantId,
                'MerchantKey' => $merchantKey,
            ])->get("{$baseUrl}/1/sales/{$transactionId}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['Payment']['Status'],
                    'data' => $data,
                ];
            }

            return ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}


