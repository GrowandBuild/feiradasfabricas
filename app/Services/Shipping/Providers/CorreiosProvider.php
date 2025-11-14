<?php

namespace App\Services\Shipping\Providers;

use App\Services\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CorreiosProvider implements ShippingProviderInterface
{
    public function getName(): string
    {
        return 'correios';
    }

    public function quote(array $origin, array $destination, array $packages): array
    {
        // Aggregate packages into single dimensions (simplified first iteration)
        $minWeight = (float) config('shipping.defaults.min_weight', 0.3);
        $defL = (int) config('shipping.defaults.length', 20);
        $defH = (int) config('shipping.defaults.height', 20);
        $defW = (int) config('shipping.defaults.width', 20);

        $totalWeight = 0.0;
        $maxLength = $maxHeight = $maxWidth = 0;
        foreach ($packages as $p) {
            $totalWeight += (float)($p['weight'] ?? 0.0);
            $maxLength = max($maxLength, (int)($p['length'] ?? 0));
            $maxHeight = max($maxHeight, (int)($p['height'] ?? 0));
            $maxWidth  = max($maxWidth,  (int)($p['width'] ?? 0));
        }

    // Real credentials (contract) are optional; if you only have CPF (sem contrato), leave empty.
    // Try settings first; fallback to ENV variables for secure storage.
    $companyCode = setting('correios_codigo_empresa') ?: env('CORREIOS_CODIGO_EMPRESA');
    $password    = setting('correios_senha') ?: env('CORREIOS_SENHA');
        $originCepSetting = setting('correios_cep_origem');

        if (empty($originCepSetting)) {
            return [[
                'provider' => 'correios',
                'service_code' => null,
                'service_name' => 'Correios',
                'price' => 0,
                'delivery_time' => 0,
                'delivery_time_text' => 'Indisponível',
                'error' => 'CEP de origem não configurado'
            ]];
        }

        $services = ['04014','04510']; // SEDEX / PAC (baseline)
        $destCep = preg_replace('/[^0-9]/','', $destination['cep'] ?? '');
        if (strlen($destCep) !== 8) {
            return [[
                'provider' => 'correios',
                'service_code' => null,
                'service_name' => 'Correios',
                'price' => 0,
                'delivery_time' => 0,
                'delivery_time_text' => 'Indisponível',
                'error' => 'CEP de destino inválido'
            ]];
        }
        $quotes = [];
        foreach ($services as $serviceCode) {
            try {
                // Use HTTPS and explicit XML return to improve reliability
                $response = Http::timeout((int)config('shipping.timeout', 10))->get('https://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx', [
                    // Credenciais são opcionais no CalcPrecoPrazo; se não houver, envia vazio
                    'nCdEmpresa' => $companyCode ?? '',
                    'sDsSenha' => $password ?? '',
                    'sCepOrigem' => preg_replace('/[^0-9]/','', $origin['cep'] ?? $originCepSetting),
                    'sCepDestino' => $destCep,
                    'nVlPeso' => max($totalWeight, $minWeight),
                    'nCdFormato' => '1',
                    'nVlComprimento' => $maxLength ?: $defL,
                    'nVlAltura' => $maxHeight ?: $defH,
                    'nVlLargura' => $maxWidth ?: $defW,
                    'nVlDiametro' => 0,
                    'sCdMaoPropria' => 'n',
                    'nVlValorDeclarado' => 0,
                    'sCdAvisoRecebimento' => 'n',
                    'nCdServico' => $serviceCode,
                    'nVlDiametro' => 0,
                    'StrRetorno' => 'xml',
                ]);

                if (!$response->successful()) {
                    Log::warning('Correios HTTP failure', ['status'=>$response->status(),'service'=>$serviceCode,'body'=>trim($response->body())]);
                    $quotes[] = $this->errorQuote($serviceCode, 'Falha HTTP Correios');
                    continue;
                }
                $xml = @simplexml_load_string($response->body());
                if (!$xml || !isset($xml->cServico)) {
                    Log::warning('Correios invalid XML', ['service'=>$serviceCode,'body'=>trim($response->body())]);
                    $quotes[] = $this->errorQuote($serviceCode, 'XML inválido Correios');
                    continue;
                }
                $service = $xml->cServico;
                if ((string)$service->Erro === '0') {
                    $price = (float) str_replace(',', '.', (string)$service->Valor);
                    $prazo = (int) $service->PrazoEntrega;
                    $quotes[] = [
                        'provider' => 'correios',
                        'service_code' => $serviceCode,
                        'service_name' => $this->serviceName($serviceCode),
                        'price' => $price,
                        'delivery_time' => $prazo,
                        'delivery_time_text' => $prazo.' dias úteis',
                        'error' => null,
                    ];
                } else {
                    $errMsg = (string)$service->MsgErro;
                    Log::warning('Correios service error', [
                        'service_code' => $serviceCode,
                        'error_code' => (string)$service->Erro,
                        'message' => $errMsg,
                    ]);
                    $quotes[] = $this->errorQuote($serviceCode, $errMsg ?: 'Erro desconhecido');
                }
            } catch (\Throwable $e) {
                Log::error('Correios quote error: '.$e->getMessage());
                $quotes[] = $this->errorQuote($serviceCode, 'Exception: '.$e->getMessage());
            }
        }
        return $quotes;
    }

    public function track(string $trackingCode): array
    {
        // Using public Correios proxy endpoint (subject to change)
        $code = strtoupper(trim($trackingCode));
        if (!preg_match('/^[A-Z]{2}[0-9]{9}[A-Z]{2}$/', $code)) {
            return ['success'=>false,'error'=>'Formato de código inválido','events'=>[],'raw'=>null];
        }
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get("https://proxyapp.correios.com.br/v1/sro-rastro/$code");
            if (!$response->successful()) {
                return ['success'=>false,'error'=>'Falha HTTP Correios','events'=>[],'raw'=>null];
            }
            $json = $response->json();
            $events = [];
            $rawEvents = $json['objeto'][0]['eventos'] ?? [];
            foreach ($rawEvents as $ev) {
                $events[] = [
                    'date' => $ev['dtHrCriado'] ?? null,
                    'status' => $ev['descricao'] ?? null,
                    'location' => $ev['unidade']['nomMun'] ?? null,
                    'uf' => $ev['unidade']['uf'] ?? null,
                    'type' => $ev['tipo'] ?? null,
                ];
            }
            return [
                'success' => true,
                'tracking_code' => $code,
                'events' => $events,
                'raw' => $json,
            ];
        } catch (\Throwable $e) {
            return ['success'=>false,'error'=>'Exception: '.$e->getMessage(),'events'=>[],'raw'=>null];
        }
    }

    public function create(array $shipmentData): array
    {
        return [
            'success' => false,
            'error' => 'Criação de envio Correios não implementada'
        ];
    }

    private function errorQuote(string $serviceCode, string $message): array
    {
        return [
            'provider' => 'correios',
            'service_code' => $serviceCode,
            'service_name' => $this->serviceName($serviceCode),
            'price' => 0.0,
            'delivery_time' => 0,
            'delivery_time_text' => 'Indisponível',
            'error' => $message,
        ];
    }

    private function serviceName(string $code): string
    {
        return [
            '04014' => 'SEDEX',
            '04510' => 'PAC',
            '04782' => 'SEDEX 12',
            '04790' => 'SEDEX 10',
            '04804' => 'SEDEX Hoje',
        ][$code] ?? 'Serviço '.$code;
    }
}
