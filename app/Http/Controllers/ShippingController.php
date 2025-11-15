<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Calcula cotações de frete para um produto via Melhor Envio.
     * Requer: product_id, cep (destino), quantity opcional (default 1)
     */
    public function quote(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'cep' => 'required|string',
                'quantity' => 'nullable|integer|min:1',
            ]);

            // Verificar se o provedor está habilitado e credenciado
            if (!setting('melhor_envio_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cálculo de frete indisponível no momento.'
                ], 400);
            }

            $clientId = setting('melhor_envio_client_id');
            $clientSecret = setting('melhor_envio_client_secret');
            $token = setting('melhor_envio_token');
            $refreshToken = setting('melhor_envio_refresh_token');
            $sandbox = setting('melhor_envio_sandbox', true);
            $env = $sandbox ? \MelhorEnvio\Enums\Environment::SANDBOX : \MelhorEnvio\Enums\Environment::PRODUCTION;

            if (empty($clientId) || empty($clientSecret) || empty($token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Integração com Melhor Envio não configurada.'
                ], 400);
            }

            $product = Product::findOrFail($validated['product_id']);
            $qty = max(1, (int)($validated['quantity'] ?? 1));

            // Normalizar CEPs (apenas dígitos)
            $cepDestino = preg_replace('/\D+/', '', (string) $validated['cep']);
            $cepOrigem = preg_replace('/\D+/', '', (string) (setting('melhor_envio_cep_origem') ?: '01010010'));

            if (strlen($cepDestino) !== 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP inválido. Informe 8 dígitos.'
                ], 422);
            }
            if (strlen($cepOrigem) !== 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP de origem inválido nas configurações.'
                ], 500);
            }

            // Preparar dimensões/peso com fallback seguro
            $length = (float) ($product->length ?: 16);
            $width  = (float) ($product->width ?: 11);
            $height = (float) ($product->height ?: 4);
            $weight = (float) ($product->weight ?: 0.3); // kg
            // Respeitar mínimos de transporte
            $length = max($length, 11); // cm
            $width  = max($width, 11);  // cm
            $height = max($height, 2);  // cm
            $weight = max($weight, 0.1); // kg

            // Valor declarado baseado no preço unitário
            $insuranceValue = (float) ($product->price ?: 0);

            // Instanciar calculadora
            $shipment = new \MelhorEnvio\Shipment($token, $env);
            $calculator = $shipment->calculator();
            $calculator->postalCode($cepOrigem, $cepDestino);

            // Adicionar produtos (quantidade definida)
            $calculator->addProducts(
                new \MelhorEnvio\Resources\Shipment\Product(
                    (string) $product->id,
                    (float) $length,
                    (float) $width,
                    (float) $height,
                    (float) $weight,
                    (float) $insuranceValue,
                    (int) $qty
                )
            );

            // Serviços opcionais: restringir aos selecionados no admin (se houver)
            $services = setting('melhor_envio_service_ids');
            $serviceIds = [];
            if (is_string($services) && trim($services) !== '') {
                $serviceIds = array_values(array_filter(array_map('intval', array_map('trim', explode(',', $services)))));
            } elseif (is_array($services) && !empty($services)) {
                $serviceIds = array_values(array_filter(array_map('intval', $services)));
            }
            if (!empty($serviceIds)) {
                $calculator->addServices(...$serviceIds);
            }

            try {
                $quotes = $calculator->calculate();
            } catch (\MelhorEnvio\Exceptions\CalculatorException $e) {
                // Tentar refresh do token e recalcular
                Log::warning('Falha no cálculo (tentando refresh token): ' . $e->getMessage());
                if (!empty($refreshToken)) {
                    try {
                        $provider = new \MelhorEnvio\Auth\OAuth2($clientId, $clientSecret);
                        $provider->setEnvironment($sandbox ? 'sandbox' : 'production');
                        $new = $provider->refreshToken($refreshToken);
                        if (!empty($new['access_token'])) {
                            Setting::set('melhor_envio_token', $new['access_token'], 'string', 'delivery');
                            if (!empty($new['refresh_token'])) {
                                Setting::set('melhor_envio_refresh_token', $new['refresh_token'], 'string', 'delivery');
                            }
                            if (!empty($new['expires_in'])) {
                                Setting::set('melhor_envio_token_expires_at', now()->addSeconds((int)$new['expires_in'])->toIso8601String(), 'string', 'delivery');
                            }
                            // Reinstanciar com novo token
                            $shipment = new \MelhorEnvio\Shipment($new['access_token'], $env);
                            $calculator = $shipment->calculator();
                            $calculator->postalCode($cepOrigem, $cepDestino);
                            $calculator->addProducts(
                                new \MelhorEnvio\Resources\Shipment\Product((string)$product->id, (float)$length, (float)$width, (float)$height, (float)$weight, (float)$insuranceValue, (int)$qty)
                            );
                            if (!empty($serviceIds)) { $calculator->addServices(...$serviceIds); }
                            $quotes = $calculator->calculate();
                        } else {
                            throw new \RuntimeException('Não foi possível atualizar o token do Melhor Envio.');
                        }
                    } catch (\Throwable $ex) {
                        Log::error('Erro ao atualizar token do Melhor Envio: ' . $ex->getMessage());
                        return response()->json([
                            'success' => false,
                            'message' => 'Erro de autenticação com o Melhor Envio. Tente novamente mais tarde.'
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sessão expirada do Melhor Envio. Reautorize no painel.'
                    ], 401);
                }
            }

            // Normalizar retorno (defensivo, caso estrutura varie)
            $normalized = [];
            if (is_array($quotes)) {
                foreach ($quotes as $q) {
                    // Converter objetos para arrays se necessário
                    $item = is_object($q) ? (array) $q : (array) $q;
                    $serviceName = $item['service'] ?? ($item['name'] ?? ($item['service_name'] ?? 'Serviço'));
                    $company = $item['company'] ?? ($item['carrier'] ?? ($item['provider'] ?? ''));
                    $price = null;
                    if (isset($item['price'])) { $price = $item['price']; }
                    elseif (isset($item['final_price'])) { $price = $item['final_price']; }
                    elseif (isset($item['cost'])) { $price = $item['cost']; }

                    $days = null;
                    if (isset($item['delivery_time']['days'])) { $days = $item['delivery_time']['days']; }
                    elseif (isset($item['delivery']['days'])) { $days = $item['delivery']['days']; }
                    elseif (isset($item['delivery_range']['min'])) { $days = $item['delivery_range']['min']; }

                    $serviceId = $item['id'] ?? ($item['service_id'] ?? null);

                    $normalized[] = [
                        'service_id' => $serviceId,
                        'service' => $serviceName,
                        'company' => $company,
                        'price' => is_numeric($price) ? (float) $price : null,
                        'delivery_days' => is_numeric($days) ? (int) $days : null,
                        'raw' => $item,
                    ];
                }
            }

            // Ordenar por preço ascendente quando possível
            usort($normalized, function ($a, $b) {
                return ($a['price'] ?? INF) <=> ($b['price'] ?? INF);
            });

            return response()->json([
                'success' => true,
                'quotes' => $normalized,
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => $ve->validator->errors()->first(),
                'errors' => $ve->validator->errors()->toArray(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erro ao calcular frete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível calcular o frete. Tente novamente.'
            ], 500);
        }
    }
}
