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

            // Preparar dimensões/peso com fallback seguro (valores base do item unitário)
            $baseLength = (float) ($product->length ?: 16);
            $baseWidth  = (float) ($product->width ?: 11);
            $baseHeight = (float) ($product->height ?: 4);
            $baseWeight = (float) ($product->weight ?: 0.3); // kg
            // Respeitar mínimos de transporte
            $baseLength = max($baseLength, 11); // cm
            $baseWidth  = max($baseWidth, 11);  // cm
            $baseHeight = max($baseHeight, 2);  // cm
            $baseWeight = max($baseWeight, 0.1); // kg

            // Heurística de consolidação: assume empilhamento vertical de até 4 itens depois aumenta camada
            $stackPerLayer = 4;
            $layers = (int) ceil($qty / $stackPerLayer);
            $itemsLastLayer = $qty % $stackPerLayer ?: min($qty, $stackPerLayer);

            // Dimensões consolidadas (mantém comprimento/largura, aumenta altura proporcional às camadas)
            $length = $baseLength; // poderia expandir com muitos itens, mantemos para simplicidade
            $width  = $baseWidth;
            $height = $baseHeight * $layers; // empilhado
            // Peso total (real) somado
            $weight = $baseWeight * $qty;

            // Calcular peso volumétrico consolidado
            $volumetricWeight = ($length * $width * $height) / 6000; // fórmula aproximada (cm³ / 6000)
            // Usar o maior entre real e volumétrico (prática comum)
            $usedWeight = max($weight, $volumetricWeight);

            // Valor declarado dinâmico conforme modo configurado
            // Valor declarado: padrão = none (mínimo simbólico) para baratear o frete
            $declaredMode = setting('melhor_envio_declared_mode','none');
            $declaredCap = (float) setting('melhor_envio_declared_cap', 80);
            $costPrice = (float) ($product->cost_price ?: ($product->price * 0.4));
            $priceFull = (float) ($product->price ?: 0);
            switch ($declaredMode) {
                case 'cap':
                    $insuranceValue = min(max($costPrice, 0), $declaredCap);
                    break;
                case 'none': // modo opcional: desabilitar seguro (usa mínimo simbólico)
                    $insuranceValue = 1.0;
                    break;
                case 'full':
                    $insuranceValue = $priceFull;
                    break;
                case 'cost':
                default:
                    $insuranceValue = $costPrice;
                    break;
            }
            // Garantir mínimo simbólico para não zerar seguro (alguns serviços rejeitam 0)
            if ($insuranceValue < 1) { $insuranceValue = 1.0; }

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
                    (float) $usedWeight,
                    (float) $insuranceValue,
                    1 // já consolidado numa caixa única
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
                                new \MelhorEnvio\Resources\Shipment\Product((string)$product->id, (float)$length, (float)$width, (float)$height, (float)$usedWeight, (float)$insuranceValue, 1)
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

            // Normalizar retorno (defensivo, caso estrutura varie) + filtrar serviços econômicos se nenhum ID configurado
            $normalized = [];
            if (is_array($quotes)) {
                foreach ($quotes as $q) {
                    // Converter objetos para arrays se necessário
                    $item = is_object($q) ? (array) $q : (array) $q;
                    $serviceName = $item['service'] ?? ($item['name'] ?? ($item['service_name'] ?? 'Serviço'));
                    // Prefixar Jadlog para serviços que vêm como ".Com", ".Package" etc.
                    if (is_string($serviceName) && str_starts_with($serviceName, '.')) {
                        $serviceName = 'Jadlog ' . $serviceName; // mantém o ponto para consistência visual
                    }
                    $companyRaw = $item['company'] ?? ($item['carrier'] ?? ($item['provider'] ?? ''));
                    $company = is_array($companyRaw) ? ($companyRaw['name'] ?? '') : (is_object($companyRaw) ? ($companyRaw->name ?? '') : $companyRaw);
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

            // Remover opções sem preço (SDK pode retornar serviços sem cotação para o cenário atual)
            $normalized = array_values(array_filter($normalized, function($n){
                return isset($n['price']) && is_numeric($n['price']);
            }));

            // Se não há serviços explicitamente configurados, filtrar para opções consideradas econômicas
            $hasCustomServices = !empty($serviceIds);
            if (!$hasCustomServices) {
                $normalized = array_filter($normalized, function($n){
                    $name = strtolower($n['service'] ?? '');
                    // Palavras-chave de serviços mais baratos/convencionais
                    return str_contains($name,'standard')
                        || str_contains($name,'econom')
                        || str_contains($name,'mini')
                        || str_contains($name,'package')
                        || str_contains($name,'pac');
                }) ?: $normalized; // se filtro vazio, mantém tudo
            }
            // Ordenar por preço ascendente quando possível
            usort($normalized, function ($a, $b) {
                return ($a['price'] ?? INF) <=> ($b['price'] ?? INF);
            });

            // Limitar excesso de serviços caros se não customizado
            if (!$hasCustomServices) {
                $normalized = array_slice($normalized, 0, 6);
            }

            // Debug opcional (adicionar metadados se ?debug=1)
            $debugMeta = null;
            if ($request->boolean('debug') || $request->hasHeader('X-Debug-Frete')) {
                $volumetricWeight = (($length * $width * $height) / 6000); // padrão aproximado
                $debugMeta = [
                    'declared_mode' => $declaredMode,
                    'declared_value' => $insuranceValue,
                    'declared_cap' => $declaredCap,
                    'product_price' => $priceFull,
                    'product_cost_estimate' => $costPrice,
                    'dimensions_cm' => compact('length','width','height'),
                    'weight_real_kg_total' => round($weight,3),
                    'weight_volumetric_kg_total' => round($volumetricWeight,3),
                    'weight_used_kg' => round($usedWeight,3),
                    'consolidated' => true,
                    'stack_layers' => $layers,
                    'environment' => $sandbox ? 'sandbox':'production',
                    'service_ids_configured' => $serviceIds,
                    'quantity' => $qty,
                ];
            }

            $responsePayload = [
                'success' => true,
                'quotes' => $normalized,
            ];
            if ($debugMeta) { $responsePayload['debug'] = $debugMeta; }
            return response()->json($responsePayload);

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

    /**
     * Persiste na sessão a opção de frete selecionada pelo usuário.
     * Espera: service (string), price (numeric), cep (8 dígitos)
     * Opcional: service_id (int), company (string), delivery_days (int), product_id (int), quantity (int)
     */
    public function select(Request $request)
    {
        try {
            $validated = $request->validate([
                'service' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'cep' => 'required|string',
                'delivery_days' => 'nullable|integer|min:0|max:60',
                'service_id' => 'nullable|integer',
                'company' => 'nullable|string|max:255',
                'product_id' => 'nullable|integer',
                'quantity' => 'nullable|integer|min:1'
            ]);

            // Normalizar CEP
            $cepDestino = preg_replace('/\D+/', '', (string) $validated['cep']);
            if (strlen($cepDestino) !== 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP inválido. Informe 8 dígitos.'
                ], 422);
            }

            // Sanitizar nome do serviço (evitar prefixos inconsistentes no front)
            $service = (string) $validated['service'];
            if (str_starts_with($service, '.')) {
                $service = 'Jadlog ' . $service;
            }

            $selection = [
                'service_id' => $validated['service_id'] ?? null,
                'service' => $service,
                'company' => $validated['company'] ?? null,
                'price' => (float) $validated['price'],
                'delivery_days' => $validated['delivery_days'] ?? null,
                'cep' => $cepDestino,
                'product_id' => $validated['product_id'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'updated_at' => now()->toIso8601String(),
            ];

            session(['shipping_selection' => $selection]);

            return response()->json([
                'success' => true,
                'selection' => $selection,
                'message' => 'Opção de frete selecionada com sucesso.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => $ve->validator->errors()->first(),
                'errors' => $ve->validator->errors()->toArray(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erro ao salvar seleção de frete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível salvar a seleção de frete.'
            ], 500);
        }
    }

    /**
     * Retorna a opção de frete atualmente selecionada na sessão.
     */
    public function current()
    {
        $selection = session('shipping_selection');
        return response()->json([
            'success' => (bool) $selection,
            'selection' => $selection,
        ]);
    }

    /**
     * Limpa a seleção de frete da sessão.
     */
    public function clear()
    {
        session()->forget('shipping_selection');
        return response()->json([
            'success' => true,
            'message' => 'Seleção de frete removida.'
        ]);
    }

    /**
     * Calcula cotações de frete para o carrinho inteiro (todas as linhas).
     * Consolida dimensões e pesos de forma heurística.
     */
    public function quoteCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'cep' => 'required|string',
            ]);

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

            // Buscar itens do carrinho atual (sessão/cliente)
            $sessionId = session('cart_session_id');
            $customerId = auth('customer')->id();
            $query = \App\Models\CartItem::with('product');
            if ($customerId) {
                $query->forCustomer($customerId);
            } else {
                $query->forSession($sessionId);
            }
            $items = $query->get();
            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho está vazio.'
                ], 400);
            }

            // Agregar dimensões/peso/valores
            $maxLength = 0.0; $maxWidth = 0.0; $sumHeight = 0.0; $sumWeight = 0.0;
            $sumCost = 0.0; $sumPrice = 0.0; $qtyTotal = 0;
            foreach ($items as $line) {
                $p = $line->product;
                if (!$p) { continue; }
                $q = max(1, (int)$line->quantity);
                $qtyTotal += $q;
                $baseLength = (float) ($p->length ?: 16);
                $baseWidth  = (float) ($p->width ?: 11);
                $baseHeight = (float) ($p->height ?: 4);
                $baseWeight = (float) ($p->weight ?: 0.3);
                // mínimos
                $baseLength = max($baseLength, 11);
                $baseWidth  = max($baseWidth, 11);
                $baseHeight = max($baseHeight, 2);
                $baseWeight = max($baseWeight, 0.1);

                $maxLength = max($maxLength, $baseLength);
                $maxWidth  = max($maxWidth, $baseWidth);
                $sumHeight += ($baseHeight * $q); // empilhar em altura por simplicidade
                $sumWeight += ($baseWeight * $q);

                $cost = (float) ($p->cost_price ?: ($p->price * 0.4));
                $sumCost += ($cost * $q);
                $sumPrice += ((float)$p->price * $q);
            }

            // Consolidado
            $length = $maxLength ?: 16;
            $width  = $maxWidth  ?: 11;
            $height = max($sumHeight, 2);
            $weight = max($sumWeight, 0.1);

            $volumetricWeight = ($length * $width * $height) / 6000; // kg approx
            $usedWeight = max($weight, $volumetricWeight);

            // Valor declarado conforme modo
            // Valor declarado: padrão = none (mínimo simbólico)
            $declaredMode = setting('melhor_envio_declared_mode','none');
            $declaredCap = (float) setting('melhor_envio_declared_cap', 80);
            switch ($declaredMode) {
                case 'cap':
                    $insuranceValue = min(max($sumCost, 0), $declaredCap);
                    break;
                case 'none':
                    $insuranceValue = 1.0;
                    break;
                case 'full':
                    $insuranceValue = $sumPrice;
                    break;
                case 'cost':
                default:
                    $insuranceValue = $sumCost;
                    break;
            }
            if ($insuranceValue < 1) { $insuranceValue = 1.0; }

            // Instanciar calculadora
            $shipment = new \MelhorEnvio\Shipment($token, $env);
            $calculator = $shipment->calculator();
            $calculator->postalCode($cepOrigem, $cepDestino);
            $calculator->addProducts(
                new \MelhorEnvio\Resources\Shipment\Product(
                    'cart', (float)$length, (float)$width, (float)$height, (float)$usedWeight, (float)$insuranceValue, 1
                )
            );

            // Serviços opcionais
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
                Log::warning('Falha no cálculo carrinho (refresh token): ' . $e->getMessage());
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
                            $shipment = new \MelhorEnvio\Shipment($new['access_token'], $env);
                            $calculator = $shipment->calculator();
                            $calculator->postalCode($cepOrigem, $cepDestino);
                            $calculator->addProducts(new \MelhorEnvio\Resources\Shipment\Product('cart', (float)$length, (float)$width, (float)$height, (float)$usedWeight, (float)$insuranceValue, 1));
                            if (!empty($serviceIds)) { $calculator->addServices(...$serviceIds); }
                            $quotes = $calculator->calculate();
                        } else {
                            throw new \RuntimeException('Não foi possível atualizar o token do Melhor Envio.');
                        }
                    } catch (\Throwable $ex) {
                        Log::error('Erro ao atualizar token do Melhor Envio (carrinho): ' . $ex->getMessage());
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

            // Normalização
            $normalized = [];
            if (is_array($quotes)) {
                foreach ($quotes as $q) {
                    $item = is_object($q) ? (array) $q : (array) $q;
                    $serviceName = $item['service'] ?? ($item['name'] ?? ($item['service_name'] ?? 'Serviço'));
                    if (is_string($serviceName) && str_starts_with($serviceName, '.')) {
                        $serviceName = 'Jadlog ' . $serviceName;
                    }
                    $companyRaw = $item['company'] ?? ($item['carrier'] ?? ($item['provider'] ?? ''));
                    $company = is_array($companyRaw) ? ($companyRaw['name'] ?? '') : (is_object($companyRaw) ? ($companyRaw->name ?? '') : $companyRaw);
                    $price = $item['price'] ?? ($item['final_price'] ?? ($item['cost'] ?? null));
                    $days = $item['delivery_time']['days'] ?? ($item['delivery']['days'] ?? ($item['delivery_range']['min'] ?? null));
                    $serviceId = $item['id'] ?? ($item['service_id'] ?? null);

                    $normalized[] = [
                        'service_id' => $serviceId,
                        'service' => $serviceName,
                        'company' => $company,
                        'price' => is_numeric($price) ? (float)$price : null,
                        'delivery_days' => is_numeric($days) ? (int)$days : null,
                        'raw' => $item,
                    ];
                }
            }

            // Remover opções sem preço (podem ser serviços indisponíveis/sem cotação)
            $normalized = array_values(array_filter($normalized, function($n){
                return isset($n['price']) && is_numeric($n['price']);
            }));

            $hasCustomServices = !empty($serviceIds);
            if (!$hasCustomServices) {
                $normalized = array_filter($normalized, function($n){
                    $name = strtolower($n['service'] ?? '');
                    return str_contains($name,'standard')
                        || str_contains($name,'econom')
                        || str_contains($name,'mini')
                        || str_contains($name,'package')
                        || str_contains($name,'pac');
                }) ?: $normalized;
            }
            usort($normalized, function($a,$b){ return ($a['price'] ?? INF) <=> ($b['price'] ?? INF); });
            if (!$hasCustomServices) { $normalized = array_slice($normalized, 0, 6); }

            $debugMeta = null;
            if ($request->boolean('debug') || $request->hasHeader('X-Debug-Frete')) {
                $debugMeta = [
                    'declared_mode' => $declaredMode,
                    'declared_value' => $insuranceValue,
                    'declared_cap' => $declaredCap,
                    'dimensions_cm' => compact('length','width','height'),
                    'weight_real_kg_total' => round($weight,3),
                    'weight_volumetric_kg_total' => round($volumetricWeight,3),
                    'weight_used_kg' => round($usedWeight,3),
                    'environment' => $sandbox ? 'sandbox':'production',
                    'quantity_total' => $qtyTotal,
                    'cart_items' => $items->map(function($i){ return [ 'id'=>$i->id, 'product_id'=>$i->product_id, 'qty'=>$i->quantity ]; }),
                ];
            }

            $payload = ['success' => true, 'quotes' => $normalized];
            if ($debugMeta) { $payload['debug'] = $debugMeta; }
            return response()->json($payload);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => $ve->validator->errors()->first(),
                'errors' => $ve->validator->errors()->toArray(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erro ao calcular frete (carrinho): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível calcular o frete do carrinho.'
            ], 500);
        }
    }
}
