<?php

namespace App\Services\PaymentGateways;

class MagaluPayGateway extends BaseGateway
{
    public function processPayment(float $amount, array $data): array
    {
        return ['success' => false, 'error' => 'Gateway Magalu Pay não implementado - aguardando documentação da API'];
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





