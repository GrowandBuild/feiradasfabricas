<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliveryService
{
    /**
     * Calcular frete via Correios
     */
    public function calculateCorreiosShipping($originCep, $destinationCep, $weight, $dimensions, $services = ['04014', '04510'])
    {
        try {
            $codigoEmpresa = setting('correios_codigo_empresa');
            $senha = setting('correios_senha');
            $cepOrigem = setting('correios_cep_origem');
            
            if (empty($codigoEmpresa) || empty($senha) || empty($cepOrigem)) {
                throw new \Exception('Credenciais dos Correios não configuradas');
            }

            $shippingOptions = [];

            foreach ($services as $serviceCode) {
                $response = Http::timeout(10)->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx', [
                    'nCdEmpresa' => $codigoEmpresa,
                    'sDsSenha' => $senha,
                    'sCepOrigem' => str_replace('-', '', $originCep ?: $cepOrigem),
                    'sCepDestino' => str_replace('-', '', $destinationCep),
                    'nVlPeso' => $weight,
                    'nCdFormato' => '1',
                    'nVlComprimento' => $dimensions['length'] ?? 20,
                    'nVlAltura' => $dimensions['height'] ?? 20,
                    'nVlLargura' => $dimensions['width'] ?? 20,
                    'nVlDiametro' => $dimensions['diameter'] ?? 0,
                    'sCdMaoPropria' => 'n',
                    'nVlValorDeclarado' => 0,
                    'sCdAvisoRecebimento' => 'n',
                    'nCdServico' => $serviceCode,
                    'nVlDiametro' => 0
                ]);

                if ($response->successful()) {
                    $xml = simplexml_load_string($response->body());
                    
                    if (isset($xml->cServico)) {
                        $service = $xml->cServico;
                        
                        if ((string) $service->Erro === '0') {
                            $shippingOptions[] = [
                                'provider' => 'correios',
                                'service_code' => $serviceCode,
                                'service_name' => $this->getCorreiosServiceName($serviceCode),
                                'price' => (float) str_replace(',', '.', (string) $service->Valor),
                                'delivery_time' => (int) $service->PrazoEntrega,
                                'delivery_time_text' => (string) $service->PrazoEntrega . ' dias úteis',
                                'error' => null
                            ];
                        } else {
                            $shippingOptions[] = [
                                'provider' => 'correios',
                                'service_code' => $serviceCode,
                                'service_name' => $this->getCorreiosServiceName($serviceCode),
                                'price' => 0,
                                'delivery_time' => 0,
                                'delivery_time_text' => 'Indisponível',
                                'error' => (string) $service->MsgErro
                            ];
                        }
                    }
                }
            }

            return [
                'success' => true,
                'shipping_options' => $shippingOptions
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Correios: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calcular frete via Total Express
     */
    public function calculateTotalExpressShipping($originData, $destinationData, $packageData)
    {
        try {
            $apiKey = setting('total_express_api_key');
            $sandbox = setting('total_express_sandbox', true);
            
            if (empty($apiKey)) {
                throw new \Exception('API Key do Total Express não configurada');
            }

            $baseUrl = $sandbox ? 'https://api-sandbox.totalexpress.com.br' : 'https://api.totalexpress.com.br';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v1/quotes', [
                'origin' => [
                    'cep' => str_replace('-', '', $originData['cep']),
                    'city' => $originData['city'],
                    'state' => $originData['state']
                ],
                'destination' => [
                    'cep' => str_replace('-', '', $destinationData['cep']),
                    'city' => $destinationData['city'],
                    'state' => $destinationData['state']
                ],
                'package' => [
                    'weight' => $packageData['weight'],
                    'length' => $packageData['length'],
                    'height' => $packageData['height'],
                    'width' => $packageData['width'],
                    'value' => $packageData['value'] ?? 0
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $shippingOptions = [];

                if (isset($data['quotes']) && is_array($data['quotes'])) {
                    foreach ($data['quotes'] as $quote) {
                        $shippingOptions[] = [
                            'provider' => 'total_express',
                            'service_code' => $quote['service_code'],
                            'service_name' => $quote['service_name'],
                            'price' => (float) $quote['price'],
                            'delivery_time' => (int) $quote['delivery_days'],
                            'delivery_time_text' => $quote['delivery_days'] . ' dias úteis',
                            'error' => null
                        ];
                    }
                }

                return [
                    'success' => true,
                    'shipping_options' => $shippingOptions
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro na API Total Express: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Total Express: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calcular frete via Jadlog
     */
    public function calculateJadlogShipping($originData, $destinationData, $packageData)
    {
        try {
            $cnpj = setting('jadlog_cnpj');
            $apiKey = setting('jadlog_api_key');
            $sandbox = setting('jadlog_sandbox', true);
            
            if (empty($cnpj) || empty($apiKey)) {
                throw new \Exception('Credenciais do Jadlog não configuradas');
            }

            $baseUrl = $sandbox ? 'https://api-sandbox.jadlog.com.br' : 'https://api.jadlog.com.br';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v1/quotes', [
                'cnpj' => $cnpj,
                'origin' => [
                    'cep' => str_replace('-', '', $originData['cep']),
                    'city' => $originData['city'],
                    'state' => $originData['state']
                ],
                'destination' => [
                    'cep' => str_replace('-', '', $destinationData['cep']),
                    'city' => $destinationData['city'],
                    'state' => $destinationData['state']
                ],
                'package' => [
                    'weight' => $packageData['weight'],
                    'length' => $packageData['length'],
                    'height' => $packageData['height'],
                    'width' => $packageData['width'],
                    'value' => $packageData['value'] ?? 0
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $shippingOptions = [];

                if (isset($data['services']) && is_array($data['services'])) {
                    foreach ($data['services'] as $service) {
                        $shippingOptions[] = [
                            'provider' => 'jadlog',
                            'service_code' => $service['code'],
                            'service_name' => $service['name'],
                            'price' => (float) $service['price'],
                            'delivery_time' => (int) $service['delivery_days'],
                            'delivery_time_text' => $service['delivery_days'] . ' dias úteis',
                            'error' => null
                        ];
                    }
                }

                return [
                    'success' => true,
                    'shipping_options' => $shippingOptions
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro na API Jadlog: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Jadlog: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calcular frete via Loggi
     */
    public function calculateLoggiShipping($originData, $destinationData, $packageData)
    {
        try {
            $apiKey = setting('loggi_api_key');
            $sandbox = setting('loggi_sandbox', true);
            
            if (empty($apiKey)) {
                throw new \Exception('API Key do Loggi não configurada');
            }

            $baseUrl = $sandbox ? 'https://api-sandbox.loggi.com' : 'https://api.loggi.com';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v1/quotes', [
                'origin' => [
                    'address' => $originData['address'],
                    'coordinates' => [
                        'lat' => $originData['lat'],
                        'lng' => $originData['lng']
                    ]
                ],
                'destination' => [
                    'address' => $destinationData['address'],
                    'coordinates' => [
                        'lat' => $destinationData['lat'],
                        'lng' => $destinationData['lng']
                    ]
                ],
                'package' => [
                    'weight' => $packageData['weight'],
                    'dimensions' => [
                        'length' => $packageData['length'],
                        'height' => $packageData['height'],
                        'width' => $packageData['width']
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $shippingOptions = [];

                if (isset($data['estimates']) && is_array($data['estimates'])) {
                    foreach ($data['estimates'] as $estimate) {
                        $shippingOptions[] = [
                            'provider' => 'loggi',
                            'service_code' => $estimate['type'],
                            'service_name' => $this->getLoggiServiceName($estimate['type']),
                            'price' => (float) $estimate['price'],
                            'delivery_time' => $estimate['delivery_time_minutes'] / 60, // Converter para horas
                            'delivery_time_text' => $estimate['delivery_time_minutes'] . ' minutos',
                            'error' => null
                        ];
                    }
                }

                return [
                    'success' => true,
                    'shipping_options' => $shippingOptions
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro na API Loggi: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Loggi: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Criar envio via provider específico
     */
    public function createShipment($provider, $shipmentData)
    {
        switch ($provider) {
            case 'correios':
                return $this->createCorreiosShipment($shipmentData);
            case 'total_express':
                return $this->createTotalExpressShipment($shipmentData);
            case 'jadlog':
                return $this->createJadlogShipment($shipmentData);
            case 'loggi':
                return $this->createLoggiShipment($shipmentData);
            default:
                return [
                    'success' => false,
                    'error' => 'Provider não reconhecido'
                ];
        }
    }

    /**
     * Rastrear envio
     */
    public function trackShipment($provider, $trackingCode)
    {
        switch ($provider) {
            case 'correios':
                return $this->trackCorreiosShipment($trackingCode);
            case 'total_express':
                return $this->trackTotalExpressShipment($trackingCode);
            case 'jadlog':
                return $this->trackJadlogShipment($trackingCode);
            case 'loggi':
                return $this->trackLoggiShipment($trackingCode);
            default:
                return [
                    'success' => false,
                    'error' => 'Provider não reconhecido'
                ];
        }
    }

    // Métodos privados para criação de envios
    private function createCorreiosShipment($shipmentData)
    {
        // Implementar criação de envio via Correios
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function createTotalExpressShipment($shipmentData)
    {
        // Implementar criação de envio via Total Express
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function createJadlogShipment($shipmentData)
    {
        // Implementar criação de envio via Jadlog
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function createLoggiShipment($shipmentData)
    {
        // Implementar criação de envio via Loggi
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    // Métodos privados para rastreamento
    private function trackCorreiosShipment($trackingCode)
    {
        // Implementar rastreamento via Correios
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function trackTotalExpressShipment($trackingCode)
    {
        // Implementar rastreamento via Total Express
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function trackJadlogShipment($trackingCode)
    {
        // Implementar rastreamento via Jadlog
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    private function trackLoggiShipment($trackingCode)
    {
        // Implementar rastreamento via Loggi
        return [
            'success' => false,
            'error' => 'Funcionalidade em desenvolvimento'
        ];
    }

    // Métodos auxiliares
    private function getCorreiosServiceName($serviceCode)
    {
        $services = [
            '04014' => 'SEDEX',
            '04510' => 'PAC',
            '04782' => 'SEDEX 12',
            '04790' => 'SEDEX 10',
            '04804' => 'SEDEX Hoje'
        ];

        return $services[$serviceCode] ?? 'Serviço ' . $serviceCode;
    }

    private function getLoggiServiceName($serviceType)
    {
        $services = [
            'express' => 'Expresso',
            'standard' => 'Padrão',
            'economy' => 'Econômico'
        ];

        return $services[$serviceType] ?? ucfirst($serviceType);
    }

    /**
     * Calcular frete combinado (múltiplos providers)
     */
    public function calculateCombinedShipping($originData, $destinationData, $packageData, $enabledProviders = null)
    {
        $allOptions = [];
        
        if ($enabledProviders === null) {
            $enabledProviders = $this->getEnabledProviders();
        }

        foreach ($enabledProviders as $provider) {
            try {
                $result = $this->calculateShippingByProvider($provider, $originData, $destinationData, $packageData);
                
                if ($result['success']) {
                    $allOptions = array_merge($allOptions, $result['shipping_options']);
                }
            } catch (\Exception $e) {
                Log::error("Erro ao calcular frete para {$provider}: " . $e->getMessage());
            }
        }

        // Ordenar por preço
        usort($allOptions, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        return [
            'success' => true,
            'shipping_options' => $allOptions
        ];
    }

    private function calculateShippingByProvider($provider, $originData, $destinationData, $packageData)
    {
        switch ($provider) {
            case 'correios':
                return $this->calculateCorreiosShipping(
                    $originData['cep'], 
                    $destinationData['cep'], 
                    $packageData['weight'], 
                    $packageData
                );
            case 'total_express':
                return $this->calculateTotalExpressShipping($originData, $destinationData, $packageData);
            case 'jadlog':
                return $this->calculateJadlogShipping($originData, $destinationData, $packageData);
            case 'loggi':
                return $this->calculateLoggiShipping($originData, $destinationData, $packageData);
            case 'melhor_envio':
                return $this->calculateMelhorEnvioShipping($originData, $destinationData, $packageData);
            default:
                return [
                    'success' => false,
                    'error' => 'Provider não reconhecido'
                ];
        }
    }

    private function getEnabledProviders()
    {
        $providers = [];
        
        if (setting('correios_enabled', false)) {
            $providers[] = 'correios';
        }
        
        if (setting('total_express_enabled', false)) {
            $providers[] = 'total_express';
        }
        
        if (setting('jadlog_enabled', false)) {
            $providers[] = 'jadlog';
        }
        
        if (setting('loggi_enabled', false)) {
            $providers[] = 'loggi';
        }
        
        if (setting('melhor_envio_enabled', false)) {
            $providers[] = 'melhor_envio';
        }

        return $providers;
    }

    /**
     * Calcular frete via Melhor Envio
     */
    private function calculateMelhorEnvioShipping($originData, $destinationData, $packageData)
    {
        try {
            $clientId = setting('melhor_envio_client_id');
            $clientSecret = setting('melhor_envio_client_secret');
            $token = setting('melhor_envio_token');
            $sandbox = setting('melhor_envio_sandbox', true);
            
            if (empty($clientId) || empty($clientSecret)) {
                return [
                    'success' => false,
                    'error' => 'Client ID e Client Secret do Melhor Envio não configurados'
                ];
            }

            $baseUrl = $sandbox 
                ? 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate'
                : 'https://www.melhorenvio.com.br/api/v2/me/shipment/calculate';

            // Usar Basic Auth se não tiver token, ou Bearer token se tiver
            $http = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Feira das Fábricas (contato@feiradasfabricas.com.br)'
            ]);

            // Se tiver token, usar Bearer. Se não, usar Basic Auth
            if (!empty($token)) {
                $http = $http->withToken($token);
            } else {
                $http = $http->withBasicAuth($clientId, $clientSecret);
            }

            $response = $http->post($baseUrl, [
                'from' => [
                    'postal_code' => preg_replace('/[^0-9]/', '', $originData['cep'])
                ],
                'to' => [
                    'postal_code' => preg_replace('/[^0-9]/', '', $destinationData['cep'])
                ],
                'products' => [
                    [
                        'id' => '1',
                        'width' => $packageData['width'] ?? 20,
                        'height' => $packageData['height'] ?? 20,
                        'length' => $packageData['length'] ?? 20,
                        'weight' => $packageData['weight'] ?? 1,
                        'insurance_value' => $packageData['value'] ?? 0,
                        'quantity' => 1
                    ]
                ],
                'services' => '1,2,3,4,17' // Correios PAC, SEDEX, etc.
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $options = [];
                
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $service) {
                        $options[] = [
                            'name' => $service['name'] ?? 'Melhor Envio',
                            'price' => (float) ($service['price'] ?? 0),
                            'delivery_time' => (int) ($service['delivery_time'] ?? 0),
                            'company' => $service['company']['name'] ?? 'Melhor Envio'
                        ];
                    }
                }
                
                return [
                    'success' => true,
                    'options' => $options
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro ao calcular frete via Melhor Envio'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete Melhor Envio: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

}
