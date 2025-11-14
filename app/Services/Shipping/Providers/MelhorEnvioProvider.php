<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MelhorEnvioProvider implements ShippingProviderInterface
{
    /** Default fallback service IDs when none configured */
    private const DEFAULT_SERVICE_IDS = '1,2,3,4,17';

    public function getName(): string { return 'melhor_envio'; }

    /**
     * Quote shipping using Melhor Envio API.
     * Origin/Destination must contain 'cep'. Packages: list of associative arrays.
     */
    public function quote(array $origin, array $destination, array $packages): array
    {
    $clientId     = setting('melhor_envio_client_id') ?: env('MELHOR_ENVIO_CLIENT_ID');
    $clientSecret = setting('melhor_envio_client_secret') ?: env('MELHOR_ENVIO_CLIENT_SECRET');
    $token        = setting('melhor_envio_token') ?: env('MELHOR_ENVIO_TOKEN'); // optional personal token
    $sandbox      = setting('melhor_envio_sandbox', env('MELHOR_ENVIO_SANDBOX', true));
    $serviceIdsSetting = trim((string) (setting('melhor_envio_service_ids', env('MELHOR_ENVIO_SERVICE_IDS', ''))));
        $servicesParam = $serviceIdsSetting !== '' ? $serviceIdsSetting : self::DEFAULT_SERVICE_IDS;

        $hasToken = !empty($token);
        $hasBasic = !empty($clientId) && !empty($clientSecret);
        if (!$hasToken && !$hasBasic) {
            return [ $this->errorQuote(null, 'Configure um Token ou Client ID/Secret do Melhor Envio') ];
        }

        $originCep = preg_replace('/[^0-9]/','', $origin['cep'] ?? setting('correios_cep_origem') ?? '');
        $destCep   = preg_replace('/[^0-9]/','', $destination['cep'] ?? '');
        if (strlen($originCep) < 8 || strlen($destCep) < 8) {
            return [ $this->errorQuote(null, 'CEP origem/destino inválido') ];
        }

        // Melhor Envio sandbox and production endpoints differ only by host
        $calculateUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate'
            : 'https://www.melhorenvio.com.br/api/v2/me/shipment/calculate';

        // Merge all packages (aggregator already sends one, but we safeguard multi)
        $merged = $this->mergePackages($packages);

        try {
            $http = Http::timeout(config('shipping.timeout', 10))
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'FeiraDasFabricas Shipping/1.0'
                ]);

            // Prefer bearer token if provided; else basic auth
            $http = $hasToken
                ? $http->withToken($token)
                : $http->withBasicAuth($clientId, $clientSecret);

            $payload = [
                'from' => ['postal_code' => $originCep],
                'to'   => ['postal_code' => $destCep],
                'products' => [[
                    'id' => 'pkg-1',
                    'width' => $merged['width'] ?? 20,
                    'height' => $merged['height'] ?? 20,
                    'length' => $merged['length'] ?? 20,
                    'weight' => $merged['weight'] ?? 1,
                    'insurance_value' => $merged['value'] ?? 0,
                    'quantity' => 1,
                ]],
                'services' => $servicesParam,
            ];

            $response = $http->post($calculateUrl, $payload);

            if (!$response->successful()) {
                $bodySnippet = substr($response->body(), 0, 240);
                Log::warning('MelhorEnvio HTTP failure', [
                    'status' => $response->status(),
                    'body_snippet' => $bodySnippet,
                ]);
                return [ $this->errorQuote(null, 'Erro HTTP '.$response->status()) ];
            }

            $data = $response->json();
            if (!is_array($data)) {
                Log::error('MelhorEnvio resposta inválida', ['raw' => $response->body()]);
                return [ $this->errorQuote(null, 'Resposta inválida Melhor Envio') ];
            }

            $services = $data['data'] ?? null;
            if (!is_array($services) || empty($services)) {
                // Some error responses include an 'errors' key
                $errors = $data['errors'] ?? null;
                if ($errors) {
                    Log::notice('MelhorEnvio retornou erros', ['errors' => $errors]);
                    $msg = is_string($errors) ? $errors : ('Erros: '.json_encode($errors));
                    return [ $this->errorQuote(null, $msg) ];
                }
                return [ $this->errorQuote(null, 'Nenhum serviço retornado') ];
            }

            $quotes = [];
            foreach ($services as $service) {
                // Some fields may differ; guard types
                $price = isset($service['price']) ? (float)$service['price'] : 0.0;
                $deliveryDays = (int)($service['delivery_time'] ?? 0);
                $name = $service['name'] ?? 'Melhor Envio';
                $quotes[] = [
                    'provider' => 'melhor_envio',
                    'service_code' => $service['id'] ?? null,
                    'service_name' => $name,
                    'price' => $price,
                    'delivery_time' => $deliveryDays,
                    'delivery_time_text' => $deliveryDays > 0 ? ($deliveryDays.' dias úteis') : 'Indisponível',
                    'error' => null,
                ];
            }

            if (empty($quotes)) {
                return [ $this->errorQuote(null, 'Nenhum serviço válido') ];
            }
            return $quotes;
        } catch (\Throwable $e) {
            Log::error('MelhorEnvio exceção', ['message' => $e->getMessage(), 'trace' => substr($e->getTraceAsString(),0,400)]);
            return [ $this->errorQuote(null, 'Exception: '.$e->getMessage()) ];
        }
    }

    public function track(string $trackingCode): array
    {
        return [
            'success' => false,
            'error' => 'Tracking Melhor Envio não implementado',
            'events' => [],
            'raw' => null,
        ];
    }

    public function create(array $shipmentData): array
    { return ['success'=>false,'error'=>'Criação Melhor Envio não implementada']; }

    private function mergePackages(array $packages): array
    {
        if (empty($packages)) {
            return [];
        }
        // Simple merge summing value and max dimensions; weight summed
        $totalWeight = 0.0; $maxL=0; $maxH=0; $maxW=0; $totalValue=0.0;
        foreach ($packages as $p) {
            $totalWeight += (float)($p['weight'] ?? 0);
            $maxL = max($maxL, (int)($p['length'] ?? 0));
            $maxH = max($maxH, (int)($p['height'] ?? 0));
            $maxW = max($maxW, (int)($p['width'] ?? 0));
            $totalValue += (float)($p['value'] ?? 0);
        }
        return [
            'weight' => max($totalWeight, 0.1),
            'length' => $maxL ?: 20,
            'height' => $maxH ?: 20,
            'width'  => $maxW ?: 20,
            'value'  => $totalValue,
        ];
    }

    private function errorQuote($serviceCode, string $msg): array
    {
        return [
            'provider' => 'melhor_envio',
            'service_code' => $serviceCode,
            'service_name' => 'Melhor Envio',
            'price' => 0,
            'delivery_time' => 0,
            'delivery_time_text' => 'Indisponível',
            'error' => $msg,
        ];
    }
}
