<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MelhorEnvioProvider implements ShippingProviderInterface
{
    public function getName(): string { return 'melhor_envio'; }

    public function quote(array $origin, array $destination, array $packages): array
    {
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $token = setting('melhor_envio_token');
        $sandbox = setting('melhor_envio_sandbox', true);
        if (empty($clientId) || empty($clientSecret)) {
            return [[
                'provider'=>'melhor_envio','service_code'=>null,'service_name'=>'Melhor Envio','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>'Credenciais Melhor Envio não configuradas'
            ]];
        }
        $baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate'
            : 'https://www.melhorenvio.com.br/api/v2/me/shipment/calculate';
        $p = $packages[0] ?? [];
        try {
            $http = Http::withHeaders([
                'Accept'=>'application/json',
                'Content-Type'=>'application/json',
                'User-Agent'=>'Feira das Fabricas (frete)'
            ]);
            if (!empty($token)) {
                $http = $http->withToken($token);
            } else {
                $http = $http->withBasicAuth($clientId,$clientSecret);
            }
            $response = $http->post($baseUrl,[
                'from' => [ 'postal_code' => preg_replace('/[^0-9]/','', $origin['cep'] ?? '') ],
                'to'   => [ 'postal_code' => preg_replace('/[^0-9]/','', $destination['cep'] ?? '') ],
                'products' => [[
                    'id' => '1',
                    'width' => $p['width'] ?? 20,
                    'height' => $p['height'] ?? 20,
                    'length' => $p['length'] ?? 20,
                    'weight' => $p['weight'] ?? 1,
                    'insurance_value' => $p['value'] ?? 0,
                    'quantity' => 1
                ]],
                'services' => '1,2,3,4,17'
            ]);
            if (!$response->successful()) {
                return [ $this->errorQuote(null,'Erro HTTP: '.$response->status()) ];
            }
            $data = $response->json();
            $quotes = [];
            if (isset($data['data'])) {
                foreach ($data['data'] as $service) {
                    $quotes[] = [
                        'provider'=>'melhor_envio',
                        'service_code'=>$service['id'] ?? null,
                        'service_name'=>$service['name'] ?? 'Melhor Envio',
                        'price'=>(float)($service['price'] ?? 0),
                        'delivery_time'=>(int)($service['delivery_time'] ?? 0),
                        'delivery_time_text'=>($service['delivery_time'] ?? 0).' dias úteis',
                        'error'=>null
                    ];
                }
            }
            return $quotes ?: [ $this->errorQuote(null,'Nenhum serviço retornado') ];
        } catch (\Throwable $e) {
            Log::error('Melhor Envio quote error: '.$e->getMessage());
            return [ $this->errorQuote(null,'Exception: '.$e->getMessage()) ];
        }
    }

    public function track(string $trackingCode): array
    { return ['success'=>false,'error'=>'Tracking Melhor Envio não implementado','events'=>[],'raw'=>null]; }
    public function create(array $shipmentData): array
    { return ['success'=>false,'error'=>'Criação Melhor Envio não implementada']; }

    private function errorQuote($serviceCode,string $msg): array
    { return ['provider'=>'melhor_envio','service_code'=>$serviceCode,'service_name'=>'Melhor Envio','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>$msg]; }
}
