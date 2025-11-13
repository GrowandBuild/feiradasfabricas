<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TotalExpressProvider implements ShippingProviderInterface
{
    public function getName(): string { return 'total_express'; }

    public function quote(array $origin, array $destination, array $packages): array
    {
        $apiKey = setting('total_express_api_key');
        $sandbox = setting('total_express_sandbox', true);
        if (empty($apiKey)) {
            return [[
                'provider'=>'total_express','service_code'=>null,'service_name'=>'Total Express','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>'API Key não configurada'
            ]];
        }
        $baseUrl = $sandbox ? 'https://api-sandbox.totalexpress.com.br' : 'https://api.totalexpress.com.br';
        $p = $packages[0] ?? [];
        try {
            $response = Http::withHeaders([
                'Authorization'=>'Bearer '.$apiKey,
                'Content-Type'=>'application/json'
            ])->post($baseUrl.'/v1/quotes',[
                'origin'=>[
                    'cep'=>preg_replace('/[^0-9]/','',$origin['cep'] ?? ''),
                    'city'=>$origin['city'] ?? '',
                    'state'=>$origin['state'] ?? ''
                ],
                'destination'=>[
                    'cep'=>preg_replace('/[^0-9]/','',$destination['cep'] ?? ''),
                    'city'=>$destination['city'] ?? '',
                    'state'=>$destination['state'] ?? ''
                ],
                'package'=>[
                    'weight'=>$p['weight'] ?? 1,
                    'length'=>$p['length'] ?? 20,
                    'height'=>$p['height'] ?? 20,
                    'width'=>$p['width'] ?? 20,
                    'value'=>$p['value'] ?? 0
                ]
            ]);
            if (!$response->successful()) {
                return [ $this->errorQuote(null,'Erro HTTP: '.$response->status()) ];
            }
            $data = $response->json();
            $quotes = [];
            if (isset($data['quotes'])) {
                foreach ($data['quotes'] as $quote) {
                    $quotes[] = [
                        'provider'=>'total_express',
                        'service_code'=>$quote['service_code'] ?? null,
                        'service_name'=>$quote['service_name'] ?? 'Total Express',
                        'price'=>(float)($quote['price'] ?? 0),
                        'delivery_time'=>(int)($quote['delivery_days'] ?? 0),
                        'delivery_time_text'=>($quote['delivery_days'] ?? 0).' dias úteis',
                        'error'=>null
                    ];
                }
            }
            return $quotes ?: [ $this->errorQuote(null,'Nenhum serviço retornado') ];
        } catch (\Throwable $e) {
            Log::error('Total Express quote error: '.$e->getMessage());
            return [ $this->errorQuote(null,'Exception: '.$e->getMessage()) ];
        }
    }

    public function track(string $trackingCode): array
    { return ['success'=>false,'error'=>'Tracking Total Express não implementado','events'=>[],'raw'=>null]; }
    public function create(array $shipmentData): array
    { return ['success'=>false,'error'=>'Criação Total Express não implementada']; }

    private function errorQuote($serviceCode,string $msg): array
    { return ['provider'=>'total_express','service_code'=>$serviceCode,'service_name'=>'Total Express','price'=>0,'delivery_time'=>0,'delivery_time_text'=>'Indisponível','error'=>$msg]; }
}
