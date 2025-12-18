<?php

namespace App\Services\PaymentGateways;

abstract class BaseGateway
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    abstract public function processPayment(float $amount, array $data): array;
    abstract public function confirmPayment(string $transactionId): array;
    abstract public function cancelPayment(string $transactionId): array;
    abstract public function getStatus(string $transactionId): array;
}





