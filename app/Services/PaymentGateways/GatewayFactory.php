<?php

namespace App\Services\PaymentGateways;

class GatewayFactory
{
    public static function create(string $gateway, array $config = []): BaseGateway
    {
        $gateways = [
            'mercadopago' => MercadoPagoGateway::class,
            'cielo' => CieloGateway::class,
            'pagseguro' => PagSeguroGateway::class,
            'ton' => TonGateway::class,
            'sumup' => SumUpGateway::class,
            'getnet' => GetNetGateway::class,
            'yelly' => YellyGateway::class,
            'magalu' => MagaluPayGateway::class,
            'turbopay' => TurboPayGateway::class,
            'infinity' => InfinityGateway::class,
        ];

        $gatewayClass = $gateways[strtolower($gateway)] ?? null;

        if (!$gatewayClass || !class_exists($gatewayClass)) {
            throw new \Exception("Gateway '{$gateway}' não suportado ou não implementado");
        }

        return new $gatewayClass($config);
    }

    public static function getAvailableGateways(): array
    {
        return [
            'mercadopago' => 'Mercado Pago',
            'cielo' => 'Cielo',
            'pagseguro' => 'PagSeguro',
            'ton' => 'Ton',
            'sumup' => 'SumUp',
            'getnet' => 'GetNet',
            'yelly' => 'Yelly',
            'magalu' => 'Magalu Pay',
            'turbopay' => 'Turbo Pay',
            'infinity' => 'Infinity',
        ];
    }
}





