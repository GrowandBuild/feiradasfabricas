<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadlogProvider implements ShippingProviderInterface
{
    public function getName(): string { return 'jadlog'; }

    public function quote(array $origin, array $destination, array $packages): array
    {
        $cnpj = setting('jadlog_cnpj');
        $apiKey = setting('jadlog_api_key');
        $sandbox = setting('jadlog_sandbox', true);
        if (empty($cnpj) || empty($apiKey)) {
            return [[
                'provider' => 'jadlog',
                'service_code' => null,
                'service_name' => 'Jadlog',
                'price' => 0,
                'delivery_time' => 0,
                'delivery_time_text' => 'Indisponível',
                'error' => 'Credenciais Jadlog não configuradas'
            ]];
        }
        // Simplificação: usar primeiro pacote
        $p = $packages[0] ?? [];
        $baseUrl = $sandbox ? 'https://api-sandbox.jadlog.com.br' : 'https://api.jadlog.com.br';
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json'
            ])->post($baseUrl.'/v1/quotes', [
                'cnpj' => $cnpj,
                'origin' => [
                    'cep' => preg_replace('/[^0-9]/','', $origin['cep'] ?? ''),
                    'city' => $origin['city'] ?? '',
                    'state' => $origin['state'] ?? ''
                ],
                'destination' => [
                    'cep' => preg_replace('/[^0-9]/','', $destination['cep'] ?? ''),
                    'city' => $destination['city'] ?? '',
                    'state' => $destination['state'] ?? ''
                ],
                'package' => [
                    'weight' => $p['weight'] ?? 1,
                    'length' => $p['length'] ?? 20,
                    'height' => $p['height'] ?? 20,
                    'width'  => $p['width'] ?? 20,
                    'value'  => $p['value'] ?? 0
                ]
            ]);
            if (!$response->successful()) {
                return [ $this->errorQuote(null, 'Erro HTTP: '.$response->status()) ];
            }
            $data = $response->json();
            $quotes = [];
            if (isset($data['services']) && is_array($data['services'])) {
                foreach ($data['services'] as $service) {
                    $quotes[] = [
                        'provider' => 'jadlog',
                        'service_code' => $service['code'] ?? null,
                        'service_name' => $service['name'] ?? 'Jadlog',
                        'price' => (float) ($service['price'] ?? 0),
                        'delivery_time' => (int) ($service['delivery_days'] ?? 0),
                        'delivery_time_text' => ($service['delivery_days'] ?? 0).' dias úteis',
                        'error' => null
                    ];
                }
            }
            return $quotes ?: [ $this->errorQuote(null,'Nenhum serviço retornado') ];
        } catch (\Throwable $e) {
            Log::error('Jadlog quote error: '.$e->getMessage());
            return [ $this->errorQuote(null,'Exception: '.$e->getMessage()) ];
        }
    }

    public function track(string $trackingCode): array
    {
        return ['success'=>false,'error'=>'Tracking Jadlog não implementado','events'=>[],'raw'=>null];
    }

    public function create(array $shipmentData): array
    {
        return ['success'=>false,'error'=>'Criação Jadlog não implementada'];
    }

    private function errorQuote($serviceCode, string $msg): array
    {
        return [
            'provider'=>'jadlog',
            'service_code'=>$serviceCode,
            'service_name'=>'Jadlog',
            'price'=>0,
            'delivery_time'=>0,
            'delivery_time_text'=>'Indisponível',
            'error'=>$msg
        ];
    }
}
