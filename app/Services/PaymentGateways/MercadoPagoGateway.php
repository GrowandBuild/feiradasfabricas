<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        try {
            $accessToken = $this->config['access_token'] ?? setting('mercadopago_access_token');
            
            if (empty($accessToken)) {
                throw new \Exception('Token de acesso do Mercado Pago não configurado');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => $amount,
                'payment_method_id' => $data['payment_method_id'] ?? 'visa',
                'payer' => [
                    'email' => $data['email'] ?? '',
                ],
                'description' => $data['description'] ?? 'Venda PDV',
                'external_reference' => $data['sale_id'] ?? null,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['id'],
                    'status' => $result['status'],
                    'payment_id' => $result['id'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Erro ao processar pagamento',
            ];

        } catch (\Exception $e) {
            Log::error('Mercado Pago Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function confirmPayment(string $transactionId): array
    {
        // Mercado Pago confirma automaticamente via webhook
        return ['success' => true, 'message' => 'Pagamento confirmado'];
    }

    public function cancelPayment(string $transactionId): array
    {
        try {
            $accessToken = $this->config['access_token'] ?? setting('mercadopago_access_token');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->put("https://api.mercadopago.com/v1/payments/{$transactionId}", [
                'status' => 'cancelled'
            ]);

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getStatus(string $transactionId): array
    {
        try {
            $accessToken = $this->config['access_token'] ?? setting('mercadopago_access_token');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://api.mercadopago.com/v1/payments/{$transactionId}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'],
                    'data' => $data,
                ];
            }

            return ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}


