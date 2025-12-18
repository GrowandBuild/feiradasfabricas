<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SumUpGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        try {
            $accessToken = $this->config['access_token'] ?? setting('sumup_access_token');
            
            if (empty($accessToken)) {
                throw new \Exception('Token de acesso SumUp não configurado');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.sumup.com/v0.1/checkouts', [
                'amount' => $amount,
                'currency' => 'BRL',
                'description' => $data['description'] ?? 'Venda PDV',
                'reference' => $data['sale_id'] ?? uniqid(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['id'],
                    'checkout_url' => $result['redirect_url'] ?? null,
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
        try {
            $accessToken = $this->config['access_token'] ?? setting('sumup_access_token');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://api.sumup.com/v0.1/checkouts/{$transactionId}");

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}





