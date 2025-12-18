<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TonGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        // Implementar quando tiver documentação da API
        return ['success' => false, 'error' => 'Gateway Ton não implementado ainda'];
    }

    public function confirmPayment(string $transactionId): array
    {
        return ['success' => false, 'error' => 'Não implementado'];
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


