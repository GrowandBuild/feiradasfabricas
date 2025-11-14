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
        $refreshToken = setting('melhor_envio_refresh_token');
        $expiresAtStr = setting('melhor_envio_token_expires_at');
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

        // Endpoints candidates (host and path) – some environments respond only on specific combos
        $hostCandidates = $sandbox
            ? ['https://sandbox.melhorenvio.com.br']
            : ['https://www.melhorenvio.com.br', 'https://melhorenvio.com.br', 'https://api.melhorenvio.com.br'];
        $pathCandidates = [
            '/api/v2/me/shipment/calculate',
            '/api/v2/shipment/calculate',
            '/api/v2/shipping/calculate',
        ];

        // Merge all packages (aggregator already sends one, but we safeguard multi)
        $merged = $this->mergePackages($packages);

        // Refresh antecipado se expirado
        if ($token && $refreshToken && $expiresAtStr) {
            $expiresAt = \Carbon\Carbon::parse($expiresAtStr, config('app.timezone'));
            if (now()->greaterThanOrEqualTo($expiresAt)) {
                $this->refreshTokenSilent($sandbox, $clientId, $clientSecret, $refreshToken);
                $token = setting('melhor_envio_token'); // recarrega
            }
        }

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

            $response = null; $lastUrl = null; $lastStatus = null; $lastBody = null;
            foreach ($hostCandidates as $host) {
                foreach ($pathCandidates as $path) {
                    try {
                        $calculateUrl = rtrim($host,'/').$path;
                        $lastUrl = $calculateUrl;
                        $response = $http->post($calculateUrl, $payload);
                        $lastStatus = $response->status();
                        $lastBody = substr($response->body(), 0, 240);
                        // Accept only JSON responses; if HTML or invalid, try next
                        if ($response->successful() && is_array($response->json())) {
                            break 2; // got a plausible JSON; proceed
                        }
                    } catch (\Throwable $e) {
                        // Network/DNS or transport error for this attempt; try next candidate
                        Log::warning('MelhorEnvio tentativa falhou', [
                            'url' => $lastUrl,
                            'error' => $e->getMessage(),
                        ]);
                        continue;
                    }
                }
            }

            // Se 401 com bearer e temos refresh_token, tenta uma única vez renovar e repetir
            if ($response->status() === 401 && $hasToken && $refreshToken) {
                Log::notice('MelhorEnvio 401 cotação - tentando refresh', [
                    'token_hash' => substr(md5((string)$token),0,8),
                    'sandbox' => $sandbox,
                ]);
                $this->refreshTokenSilent($sandbox, $clientId, $clientSecret, $refreshToken);
                $token = setting('melhor_envio_token');
                if ($token) {
                    $http = Http::timeout(config('shipping.timeout', 10))
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'User-Agent' => 'FeiraDasFabricas Shipping/1.0'
                        ])->withToken($token);
                    $response = $http->post($calculateUrl, $payload); // retry
                }
            }

            if (!$response || !$response->successful()) {
                $bodySnippet = $lastBody;
                Log::warning('MelhorEnvio HTTP failure', [
                    'status' => $lastStatus,
                    'body_snippet' => $bodySnippet,
                    'sandbox' => $sandbox,
                    'token_hash' => $hasToken ? substr(md5((string)$token),0,8) : null,
                    'origin_cep' => $originCep,
                    'dest_cep' => $destCep,
                    'merged' => $merged,
                    'services_param' => $servicesParam,
                    'url' => $lastUrl,
                ]);
                return [ $this->errorQuote(null, 'Erro HTTP '.($lastStatus ?? 'n/a')) ];
            }

            $data = $response ? $response->json() : null;
            if (!is_array($data)) {
                Log::error('MelhorEnvio resposta inválida', [
                    'raw' => $lastBody,
                    'sandbox' => $sandbox,
                    'token_hash' => $hasToken ? substr(md5((string)$token),0,8) : null,
                    'url' => $lastUrl,
                ]);
                return [ $this->errorQuote(null, 'Resposta inválida Melhor Envio') ];
            }

            $services = $data['data'] ?? null;
            if (!is_array($services) || empty($services)) {
                // Some error responses include an 'errors' key
                $errors = $data['errors'] ?? null;
                if ($errors) {
                    Log::notice('MelhorEnvio retornou erros', [
                        'errors' => $errors,
                        'sandbox' => $sandbox,
                        'token_hash' => $hasToken ? substr(md5((string)$token),0,8) : null,
                    ]);
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
            Log::error('MelhorEnvio exceção', [
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(),0,400),
                'sandbox' => $sandbox,
                'token_hash' => $hasToken ? substr(md5((string)$token),0,8) : null,
            ]);
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

    /** Silent refresh similar to controller logic; no exceptions bubble */
    private function refreshTokenSilent(bool $sandbox, ?string $clientId, ?string $clientSecret, string $refreshToken): void
    {
        try {
            $tokenUrl = $sandbox
                ? 'https://sandbox.melhorenvio.com.br/oauth/token'
                : 'https://www.melhorenvio.com.br/oauth/token';
            $payload = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ];
            if ($clientId && $clientSecret) {
                $payload['client_id'] = $clientId;
                $payload['client_secret'] = $clientSecret;
            }
            $resp = Http::asForm()->timeout(12)->post($tokenUrl, $payload);
            if ($resp->successful()) {
                $json = $resp->json();
                if (!empty($json['access_token'])) { setting('melhor_envio_token', $json['access_token']); \App\Models\Setting::set('melhor_envio_token', trim((string)$json['access_token'])); }
                if (!empty($json['refresh_token'])) { \App\Models\Setting::set('melhor_envio_refresh_token', trim((string)$json['refresh_token'])); }
                if (!empty($json['expires_in'])) {
                    \App\Models\Setting::set('melhor_envio_token_expires_at', now()->addSeconds(max(((int)$json['expires_in']) - 60,0))->toDateTimeString());
                }
                Log::info('MelhorEnvio token refresh (provider) OK');
            } else {
                Log::warning('MelhorEnvio token refresh falhou (provider)', ['status'=>$resp->status(),'body'=>substr($resp->body(),0,160)]);
            }
        } catch (\Throwable $e) {
            Log::error('MelhorEnvio token refresh exceção (provider)', ['error'=>$e->getMessage()]);
        }
    }
}
