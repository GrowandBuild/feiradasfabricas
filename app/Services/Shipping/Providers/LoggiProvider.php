<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoggiProvider implements ShippingProviderInterface
{
    public function getName(): string { return 'loggi'; }

    public function quote(array $origin, array $destination, array $packages): array
    {
        $apiKey = setting('loggi_api_key');
        $sandbox = setting('loggi_sandbox', true);
        if (empty($apiKey)) {
            return [[
                'provider'=>'loggi','service_code'=>null,'service_name'=>'Loggi','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>'API Key Loggi não configurada'
            ]];
        }
        $baseUrl = $sandbox ? 'https://api-sandbox.loggi.com' : 'https://api.loggi.com';
        $p = $packages[0] ?? [];
        try {
            $response = Http::withHeaders([
                'Authorization'=>'Bearer '.$apiKey,
                'Content-Type'=>'application/json'
            ])->post($baseUrl.'/v1/quotes', [
                'origin' => [
                    'address' => $origin['address'] ?? '',
                    'coordinates' => [
                        'lat' => $origin['lat'] ?? 0,
                        'lng' => $origin['lng'] ?? 0
                    ]
                ],
                'destination' => [
                    'address' => $destination['address'] ?? '',
                    'coordinates' => [
                        'lat' => $destination['lat'] ?? 0,
                        'lng' => $destination['lng'] ?? 0
                    ]
                ],
                'package' => [
                    'weight' => $p['weight'] ?? 1,
                    'dimensions' => [
                        'length' => $p['length'] ?? 20,
                        'height' => $p['height'] ?? 20,
                        'width'  => $p['width'] ?? 20
                    ]
                ]
            ]);
            if (!$response->successful()) {
                return [ $this->errorQuote(null,'Erro HTTP: '.$response->status()) ];
            }
            $data = $response->json();
            $quotes = [];
            if (isset($data['estimates'])) {
                foreach ($data['estimates'] as $estimate) {
                    $quotes[] = [
                        'provider'=>'loggi',
                        'service_code'=>$estimate['type'] ?? null,
                        'service_name'=>$this->serviceName($estimate['type'] ?? ''),
                        'price'=>(float)($estimate['price'] ?? 0),
                        'delivery_time'=> (int) ceil(($estimate['delivery_time_minutes'] ?? 0)/60),
                        'delivery_time_text'=> ($estimate['delivery_time_minutes'] ?? 0).' minutos',
                        'error'=>null
                    ];
                }
            }
            return $quotes ?: [ $this->errorQuote(null,'Nenhuma estimativa retornada') ];
        } catch (\Throwable $e) {
            Log::error('Loggi quote error: '.$e->getMessage());
            return [ $this->errorQuote(null,'Exception: '.$e->getMessage()) ];
        }
    }

    public function track(string $trackingCode): array
    { return ['success'=>false,'error'=>'Tracking Loggi não implementado','events'=>[],'raw'=>null]; }
    public function create(array $shipmentData): array
    { return ['success'=>false,'error'=>'Criação Loggi não implementada']; }

    private function errorQuote($serviceCode,string $msg): array
    { return ['provider'=>'loggi','service_code'=>$serviceCode,'service_name'=>'Loggi','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>$msg]; }
    private function serviceName(string $type): string
    { return ['express'=>'Expresso','standard'=>'Padrão','economy'=>'Econômico'][$type] ?? ucfirst($type ?: 'Loggi'); }
}
