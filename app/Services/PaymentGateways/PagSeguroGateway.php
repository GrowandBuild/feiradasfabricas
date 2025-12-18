<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagSeguroGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        try {
            $email = $this->config['email'] ?? setting('pagseguro_email');
            $token = $this->config['token'] ?? setting('pagseguro_token');
            $sandbox = $this->config['sandbox'] ?? setting('pagseguro_sandbox', true);
            
            $baseUrl = $sandbox 
                ? 'https://ws.sandbox.pagseguro.uol.com.br'
                : 'https://ws.pagseguro.uol.com.br';

            $response = Http::asForm()->post("{$baseUrl}/v2/transactions", [
                'email' => $email,
                'token' => $token,
                'paymentMode' => 'default',
                'paymentMethod' => 'creditCard',
                'currency' => 'BRL',
                'itemId1' => '1',
                'itemDescription1' => $data['description'] ?? 'Venda PDV',
                'itemAmount1' => number_format($amount, 2, '.', ''),
                'itemQuantity1' => '1',
                'reference' => $data['sale_id'] ?? uniqid(),
                'creditCardToken' => $data['card_token'] ?? '',
                'installmentQuantity' => $data['installments'] ?? 1,
                'installmentValue' => number_format($amount / ($data['installments'] ?? 1), 2, '.', ''),
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                return [
                    'success' => true,
                    'transaction_id' => (string)$xml->code,
                    'status' => (string)$xml->status,
                    'reference' => (string)$xml->reference,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento PagSeguro',
            ];

        } catch (\Exception $e) {
            Log::error('PagSeguro Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function confirmPayment(string $transactionId): array
    {
        return ['success' => true];
    }

    public function cancelPayment(string $transactionId): array
    {
        try {
            $email = $this->config['email'] ?? setting('pagseguro_email');
            $token = $this->config['token'] ?? setting('pagseguro_token');
            $sandbox = $this->config['sandbox'] ?? setting('pagseguro_sandbox', true);
            
            $baseUrl = $sandbox 
                ? 'https://ws.sandbox.pagseguro.uol.com.br'
                : 'https://ws.pagseguro.uol.com.br';

            $response = Http::asForm()->post("{$baseUrl}/v2/transactions/cancels", [
                'email' => $email,
                'token' => $token,
                'transactionCode' => $transactionId,
            ]);

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getStatus(string $transactionId): array
    {
        try {
            $email = $this->config['email'] ?? setting('pagseguro_email');
            $token = $this->config['token'] ?? setting('pagseguro_token');
            $sandbox = $this->config['sandbox'] ?? setting('pagseguro_sandbox', true);
            
            $baseUrl = $sandbox 
                ? 'https://ws.sandbox.pagseguro.uol.com.br'
                : 'https://ws.pagseguro.uol.com.br';

            $response = Http::get("{$baseUrl}/v2/transactions/{$transactionId}", [
                'email' => $email,
                'token' => $token,
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                return [
                    'success' => true,
                    'status' => (string)$xml->status,
                    'data' => $xml,
                ];
            }

            return ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}





