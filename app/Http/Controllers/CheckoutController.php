<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\PaymentService;

class CheckoutController extends Controller
{
    /**
     * Página inicial do checkout
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $subtotal = $this->calculateSubtotal();
        $shipping = $this->calculateShipping();
        $total = $subtotal + $shipping;
        $shippingSelection = session('shipping_selection');
        
        // Se for requisição AJAX, retornar apenas JSON com os valores
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'shipping_selection' => $shippingSelection,
                'formatted' => [
                    'subtotal' => 'R$ ' . number_format($subtotal, 2, ',', '.'),
                    'shipping' => 'R$ ' . number_format($shipping, 2, ',', '.'),
                    'total' => 'R$ ' . number_format($total, 2, ',', '.')
                ]
            ]);
        }

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total', 'shippingSelection'));
    }

    /**
     * Processar checkout (NOVO FLUXO SEGURO)
     */
    public function store(Request $request)
    {
        // Verificar se há frete regional selecionado
        $shippingSelection = session('shipping_selection');
        $hasRegionalShipping = !empty($shippingSelection) && !empty($shippingSelection['shipping_type']) && $shippingSelection['shipping_type'] === 'regional';
        
        // Regras de validação baseadas no tipo de frete
        $addressRules = $hasRegionalShipping ? 'required' : 'nullable';
        
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_cpf' => 'nullable|string|max:14',
            // Campos de endereço detalhados
            'shipping_street' => $addressRules . '|string|max:255',
            'shipping_number' => $addressRules . '|string|max:20',
            'shipping_complement' => 'nullable|string|max:255',
            'shipping_neighborhood' => $addressRules . '|string|max:255',
            'shipping_city' => $addressRules . '|string|max:100',
            'shipping_state' => $addressRules . '|string|max:2',
            'shipping_zip' => $addressRules . '|string|max:10',
            // Campo antigo mantido para compatibilidade
            'shipping_address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:credit_card,pix,boleto'
        ]);
        
        // Montar endereço completo dos campos detalhados
        $shippingAddress = '';
        if ($request->shipping_street) {
            $parts = array_filter([
                $request->shipping_street,
                $request->shipping_number ? 'Nº ' . $request->shipping_number : null,
                $request->shipping_complement,
                $request->shipping_neighborhood ? 'Bairro: ' . $request->shipping_neighborhood : null,
                $request->shipping_city,
                $request->shipping_state,
                $request->shipping_zip
            ]);
            $shippingAddress = implode(', ', $parts);
        } else {
            $shippingAddress = $request->shipping_address ?? '';
        }

        try {
            DB::beginTransaction();

            // Obter itens do carrinho
            $cartItems = $this->getCartItems();
            
            if ($cartItems->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Seu carrinho está vazio.')
                    ->withInput();
            }

            // Validar produtos antes de processar checkout
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $variation = $cartItem->variation;
                
                // Verificar se produto ainda existe e está ativo
                if (!$product || !$product->is_active) {
                    $productName = $product ? ($product->name ?? 'desconhecido') : 'desconhecido';
                    return redirect()->back()
                        ->with('error', "Produto {$productName} não está mais disponível.")
                        ->withInput();
                }
                
                // Se tiver variação, validar variação; senão, validar produto
                if ($variation) {
                    // Validar variação
                    if (!$variation->in_stock || $variation->stock_quantity < $cartItem->quantity) {
                        return redirect()->back()
                            ->with('error', "Variação do produto {$product->name} não está mais disponível em estoque suficiente.")
                            ->withInput();
                    }
                    
                    // Validar preço da variação
                    $priceDifference = abs($variation->price - $cartItem->price);
                    $priceChangePercent = $cartItem->price > 0 ? ($priceDifference / $cartItem->price) * 100 : 0;
                    
                    if ($priceChangePercent > 10) {
                        $cartItem->update(['price' => $variation->price]);
                    }
                } else {
                    // Validar estoque do produto
                    if (!$product->in_stock || $product->stock_quantity < $cartItem->quantity) {
                        return redirect()->back()
                            ->with('error', "Produto {$product->name} não está mais disponível em estoque suficiente.")
                            ->withInput();
                    }
                    
                    // Validar se preço mudou significativamente (mais de 10% de diferença)
                    $priceDifference = abs($product->price - $cartItem->price);
                    $priceChangePercent = $cartItem->price > 0 ? ($priceDifference / $cartItem->price) * 100 : 0;
                    
                    if ($priceChangePercent > 10) {
                        // Atualizar preço no carrinho se mudou muito
                        $cartItem->update(['price' => $product->price]);
                    }
                }
            }

            // Calcular totais
            $subtotal = $this->calculateSubtotal();
            $shipping = $this->calculateShipping();
            $total = $subtotal + $shipping;

            // NÃO criar pedido ainda - apenas preparar dados para pagamento
            // O pedido será criado APENAS após pagamento aprovado

            // Processar pagamento com Mercado Pago
            $paymentService = new PaymentService();
            
            // Preparar dados do pagamento
            $paymentData = [
                'email' => $request->customer_email,
                'first_name' => explode(' ', $request->customer_name)[0] ?? $request->customer_name,
                'last_name' => count(explode(' ', $request->customer_name)) > 1 ? implode(' ', array_slice(explode(' ', $request->customer_name), 1)) : '',
                'cpf' => $request->customer_cpf,
                'payment_method' => $request->payment_method,
                'address' => [
                    'street' => $shippingAddress,
                    'city' => $request->shipping_city ?? '',
                    'state' => $request->shipping_state ?? '',
                    'zip' => $request->shipping_zip ?? ''
                ]
            ];

            // Preparar itens do carrinho para metadados
            $cartItemsArray = $cartItems->map(function($item) {
                $displayProduct = $item->variation ?? $item->product;
                return [
                    'product_id' => $item->product_id,
                    'variation_id' => $item->variation_id,
                    'product' => [
                        'name' => $displayProduct->formatted_name ?? $item->product->name ?? 'Produto',
                        'sku' => $displayProduct->sku ?? $item->product->sku ?? '',
                        'description' => $item->product->description ?? ''
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total
                ];
            })->toArray();

            // Metadados temporários (sem order_id)
            $metadata = [
                'customer_email' => $request->customer_email,
                'temp_order' => true,
                'payment_method' => $request->payment_method,
                'cart_items' => $cartItemsArray
            ];

            // Processar pagamento baseado no método
            if ($request->payment_method === 'credit_card') {
                // Para cartão, usar checkout personalizado
                $paymentResult = $paymentService->processMercadoPagoPayment(
                    $total,
                    'BRL',
                    $paymentData,
                    $metadata
                );
            } else {
                // Para PIX e Boleto, usar preferência
                $paymentResult = $paymentService->createMercadoPagoPreference(
                    $total,
                    'BRL',
                    $paymentData,
                    $metadata
                );
            }

            if ($paymentResult['success']) {
                // Armazenar dados temporariamente na sessão (SEM criar pedido)
                $shippingSelection = session('shipping_selection');
                session([
                    'temp_order_data' => [
                        'customer_name' => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone ?? null,
                        'customer_cpf' => $request->customer_cpf ?? null,
                        'shipping_address' => $shippingAddress,
                        'shipping_street' => $request->shipping_street,
                        'shipping_number' => $request->shipping_number,
                        'shipping_complement' => $request->shipping_complement,
                        'shipping_neighborhood' => $request->shipping_neighborhood,
                        'shipping_city' => $request->shipping_city,
                        'shipping_state' => $request->shipping_state,
                        'shipping_zip' => $request->shipping_zip,
                        'payment_method' => $request->payment_method,
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shipping,
                        'shipping_selection' => $shippingSelection,
                        'total_amount' => $total,
                        'cart_items' => $cartItemsArray
                    ],
                    'checkout_data' => $paymentResult
                ]);

                DB::commit();

                // Redirecionar baseado no método de pagamento
                if ($request->payment_method === 'credit_card') {
                    // Para cartão, usar checkout personalizado
                    return redirect()->route('checkout.payment.temp')
                        ->with('success', 'Complete o pagamento para finalizar seu pedido.');
                } elseif ($request->payment_method === 'pix') {
                    // Para PIX, usar página específica
                    return redirect()->route('checkout.payment.pix')
                        ->with('success', 'Complete o pagamento para finalizar seu pedido.');
                } elseif ($request->payment_method === 'boleto') {
                    // Para Boleto, usar página específica
                    return redirect()->route('checkout.payment.boleto')
                        ->with('success', 'Complete o pagamento para finalizar seu pedido.');
                } else {
                    // Fallback para PIX
                    return redirect()->route('checkout.payment.pix')
                        ->with('success', 'Complete o pagamento para finalizar seu pedido.');
                }
            } else {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Erro ao processar pagamento: ' . $paymentResult['error'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erro ao processar pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Página de pagamento temporária (sem pedido criado)
     */
    public function paymentTemp()
    {
        $tempOrderData = session('temp_order_data');
        $checkoutData = session('checkout_data');

        if (!$tempOrderData || !$checkoutData) {
            return redirect()->route('checkout.index')
                ->with('error', 'Sessão expirada. Por favor, refaça o checkout.');
        }

        return view('checkout.payment-temp', compact('tempOrderData', 'checkoutData'));
    }

    /**
     * Página de pagamento PIX
     */
    public function paymentPix()
    {
        $tempOrderData = session('temp_order_data');
        $checkoutData = session('checkout_data');

        if (!$tempOrderData || !$checkoutData) {
            return redirect()->route('checkout.index')
                ->with('error', 'Sessão expirada. Por favor, refaça o checkout.');
        }

        return view('checkout.payment-pix', compact('tempOrderData', 'checkoutData'));
    }

    /**
     * Página de pagamento Boleto
     */
    public function paymentBoleto()
    {
        $tempOrderData = session('temp_order_data');
        $checkoutData = session('checkout_data');

        if (!$tempOrderData || !$checkoutData) {
            return redirect()->route('checkout.index')
                ->with('error', 'Sessão expirada. Por favor, refaça o checkout.');
        }

        return view('checkout.payment-boleto', compact('tempOrderData', 'checkoutData'));
    }

    /**
     * Verificar status de pagamento PIX/Boleto
     */
    public function checkPaymentStatusTemp(Request $request)
    {
        try {
            $tempOrderData = session('temp_order_data');
            $checkoutData = session('checkout_data');

            if (!$tempOrderData || !$checkoutData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sessão expirada'
                ], 400);
            }

            // Verificar se há um payment_id para consultar
            if (isset($checkoutData['payment_id'])) {
                $paymentService = new PaymentService();
                $statusResult = $paymentService->checkPaymentStatus('mercadopago', $checkoutData['payment_id']);
                
                if ($statusResult['success']) {
                    $status = $statusResult['status'];
                    
                    if ($status === 'approved' || $status === 'paid') {
                        // Pagamento aprovado - criar pedido
                        $this->createOrderFromTempData($tempOrderData, $checkoutData, $status);
                        
                        return response()->json([
                            'success' => true,
                            'status' => $status,
                            'message' => 'Pagamento confirmado!',
                            'redirect_url' => route('checkout.success', 'temp')
                        ]);
                    } else {
                        return response()->json([
                            'success' => true,
                            'status' => $status,
                            'message' => 'Aguardando pagamento...'
                        ]);
                    }
                }
            }

            // Se não conseguir verificar, retornar como pendente
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Aguardando pagamento...'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao verificar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar pedido a partir dos dados temporários
     */
    private function createOrderFromTempData($tempOrderData, $checkoutData, $status)
    {
        try {
            DB::beginTransaction();

            // Validar estoque antes de criar pedido
            foreach ($tempOrderData['cart_items'] as $item) {
                if (!empty($item['variation_id'])) {
                    // Validar variação
                    $variation = \App\Models\ProductVariation::find($item['variation_id']);
                    if (!$variation || !$variation->in_stock || $variation->stock_quantity < $item['quantity']) {
                        throw new \Exception("Variação do produto {$item['product']['name']} não está mais disponível em estoque suficiente.");
                    }
                } else {
                    // Validar produto
                    $product = Product::find($item['product_id']);
                    if (!$product || !$product->in_stock || $product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Produto {$item['product']['name']} não está mais disponível em estoque suficiente.");
                    }
                }
            }

            $shipSel = $tempOrderData['shipping_selection'] ?? session('shipping_selection');
            
            // Separar nome completo em first_name e last_name
            $nameParts = explode(' ', $tempOrderData['customer_name'], 2);
            $firstName = $nameParts[0] ?? $tempOrderData['customer_name'];
            $lastName = $nameParts[1] ?? '';
            
            // Preparar CEP
            $zipCode = isset($shipSel['cep']) ? (substr($shipSel['cep'],0,5).'-'.substr($shipSel['cep'],5)) : ($tempOrderData['shipping_zip'] ?? '');
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => Auth::guard('customer')->id(), // Se logado
                'subtotal' => $tempOrderData['subtotal'],
                'tax_amount' => 0,
                'shipping_amount' => $tempOrderData['shipping_cost'] ?? 0,
                'discount_amount' => 0,
                'total_amount' => $tempOrderData['total_amount'],
                'status' => 'paid',
                'payment_status' => $status,
                'shipping_status' => 'pending',
                'payment_method' => $tempOrderData['payment_method'],
                'payment_details' => json_encode([
                    'payment_id' => $checkoutData['payment_id'] ?? null,
                    'status' => $status,
                    'provider' => 'mercadopago',
                    'customer_email' => $tempOrderData['customer_email'],
                    'customer_phone' => $tempOrderData['customer_phone'] ?? null,
                    'customer_cpf' => $tempOrderData['customer_cpf'] ?? null,
                ]),
                // Campos obrigatórios de shipping
                'shipping_first_name' => $firstName,
                'shipping_last_name' => $lastName,
                'shipping_address' => $tempOrderData['shipping_address'] ?? '',
                'shipping_street' => $tempOrderData['shipping_street'] ?? '',
                'shipping_number' => $tempOrderData['shipping_number'] ?? '',
                'shipping_complement' => $tempOrderData['shipping_complement'] ?? '',
                'shipping_neighborhood' => $tempOrderData['shipping_neighborhood'] ?? '',
                'shipping_city' => $tempOrderData['shipping_city'] ?? '',
                'shipping_state' => $tempOrderData['shipping_state'] ?? '',
                'shipping_zip_code' => $zipCode,
                'shipping_phone' => $tempOrderData['customer_phone'] ?? null,
                'shipping_company' => $shipSel['company'] ?? null,
                // Campos obrigatórios de billing (mesmo que shipping)
                'billing_first_name' => $firstName,
                'billing_last_name' => $lastName,
                'billing_address' => $tempOrderData['shipping_address'] ?? '',
                'billing_street' => $tempOrderData['shipping_street'] ?? '',
                'billing_number' => $tempOrderData['shipping_number'] ?? '',
                'billing_complement' => $tempOrderData['shipping_complement'] ?? '',
                'billing_neighborhood' => $tempOrderData['shipping_neighborhood'] ?? '',
                'billing_city' => $tempOrderData['shipping_city'] ?? '',
                'billing_state' => $tempOrderData['shipping_state'] ?? '',
                'billing_zip_code' => $zipCode,
                // Persistir seleção de frete
                'shipping_service' => $shipSel['service'] ?? null,
                'shipping_service_id' => $shipSel['service_id'] ?? null,
                'shipping_delivery_days' => $shipSel['delivery_days'] ?? null,
            ]);

            // Criar itens do pedido
            foreach ($tempOrderData['cart_items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'product_name' => $item['product']['name'],
                    'product_sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                // Atualizar estoque - se tiver variação, atualizar variação; senão, produto
                if (!empty($item['variation_id'])) {
                    $variation = \App\Models\ProductVariation::find($item['variation_id']);
                    if ($variation) {
                        $variation->decrement('stock_quantity', $item['quantity']);
                        $variation->update(['in_stock' => $variation->stock_quantity > 0]);
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock_quantity', $item['quantity']);
                        $product->update(['in_stock' => $product->stock_quantity > 0]);
                    }
                }
            }

            // Limpar carrinho e sessão
            $this->clearCart();
            session()->forget(['temp_order_data', 'checkout_data']);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Processar pagamento e criar pedido apenas se aprovado
     */
    public function processPaymentAndCreateOrder(Request $request)
    {
        try {
            $tempOrderData = session('temp_order_data');
            $checkoutData = session('checkout_data');

            if (!$tempOrderData || !$checkoutData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sessão expirada'
                ], 400);
            }

            $request->validate([
                'token' => 'required|string',
                'payment_method_id' => 'required|string'
            ]);

            DB::beginTransaction();

            // Processar pagamento PRIMEIRO
            $paymentService = new PaymentService();
            $paymentResult = $paymentService->processMercadoPagoWithToken(
                $request->token,
                $tempOrderData['total_amount'],
                'BRL',
                [
                    'email' => $tempOrderData['customer_email'],
                    'first_name' => explode(' ', $tempOrderData['customer_name'])[0] ?? $tempOrderData['customer_name'],
                    'last_name' => count(explode(' ', $tempOrderData['customer_name'])) > 1 ? implode(' ', array_slice(explode(' ', $tempOrderData['customer_name']), 1)) : '',
                    'cpf' => $tempOrderData['customer_cpf'],
                ],
                ['temp_order' => true]
            );

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $paymentResult['error']
                ], 400);
            }

            // Validar estoque antes de processar pagamento
            foreach ($tempOrderData['cart_items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || !$product->in_stock || $product->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => "Produto {$item['product']['name']} não está mais disponível em estoque suficiente."
                    ], 400);
                }
            }

            // SÓ criar o pedido se o pagamento for aprovado
            if ($paymentResult['status'] === 'approved' || $paymentResult['status'] === 'paid') {
                $shipSel = $tempOrderData['shipping_selection'] ?? session('shipping_selection');
                
                // Separar nome completo em first_name e last_name
                $nameParts = explode(' ', $tempOrderData['customer_name'], 2);
                $firstName = $nameParts[0] ?? $tempOrderData['customer_name'];
                $lastName = $nameParts[1] ?? '';
                
                // Preparar CEP
                $zipCode = isset($shipSel['cep']) ? (substr($shipSel['cep'],0,5).'-'.substr($shipSel['cep'],5)) : ($tempOrderData['shipping_zip'] ?? '');
                
                $order = Order::create([
                    'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'customer_id' => Auth::guard('customer')->id(), // Se logado
                    'subtotal' => $tempOrderData['subtotal'],
                    'tax_amount' => 0,
                    'shipping_amount' => $tempOrderData['shipping_cost'] ?? 0,
                    'discount_amount' => 0,
                    'total_amount' => $tempOrderData['total_amount'],
                    'status' => 'paid', // Pedido pago
                    'payment_status' => $paymentResult['status'],
                    'shipping_status' => 'pending',
                    'payment_method' => $tempOrderData['payment_method'],
                    'payment_details' => json_encode([
                        'payment_id' => $paymentResult['payment_id'],
                        'status' => $paymentResult['status'],
                        'provider' => 'mercadopago',
                        'customer_email' => $tempOrderData['customer_email'],
                        'customer_phone' => $tempOrderData['customer_phone'] ?? null,
                        'customer_cpf' => $tempOrderData['customer_cpf'] ?? null,
                    ]),
                    // Campos obrigatórios de shipping
                    'shipping_first_name' => $firstName,
                    'shipping_last_name' => $lastName,
                    'shipping_address' => $tempOrderData['shipping_address'] ?? '',
                    'shipping_street' => $tempOrderData['shipping_street'] ?? '',
                    'shipping_number' => $tempOrderData['shipping_number'] ?? '',
                    'shipping_complement' => $tempOrderData['shipping_complement'] ?? '',
                    'shipping_neighborhood' => $tempOrderData['shipping_neighborhood'] ?? '',
                    'shipping_city' => $tempOrderData['shipping_city'] ?? '',
                    'shipping_state' => $tempOrderData['shipping_state'] ?? '',
                    'shipping_zip_code' => $zipCode,
                    'shipping_phone' => $tempOrderData['customer_phone'] ?? null,
                    'shipping_company' => $shipSel['company'] ?? null,
                    // Campos obrigatórios de billing (mesmo que shipping)
                    'billing_first_name' => $firstName,
                    'billing_last_name' => $lastName,
                    'billing_address' => $tempOrderData['shipping_address'] ?? '',
                    'billing_neighborhood' => '',
                    'billing_city' => $tempOrderData['shipping_city'] ?? '',
                    'billing_state' => $tempOrderData['shipping_state'] ?? '',
                    'billing_zip_code' => $zipCode,
                    // Persistir seleção de frete
                    'shipping_service' => $shipSel['service'] ?? null,
                    'shipping_service_id' => $shipSel['service_id'] ?? null,
                    'shipping_delivery_days' => $shipSel['delivery_days'] ?? null,
                ]);

                // Criar itens do pedido
                foreach ($tempOrderData['cart_items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'] ?? null,
                        'product_name' => $item['product']['name'],
                        'product_sku' => $item['product']['sku'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['total'],
                    ]);

                    // Atualizar estoque - se tiver variação, atualizar variação; senão, produto
                    if (!empty($item['variation_id'])) {
                        $variation = \App\Models\ProductVariation::find($item['variation_id']);
                        if ($variation) {
                            $variation->decrement('stock_quantity', $item['quantity']);
                            $variation->update(['in_stock' => $variation->stock_quantity > 0]);
                        }
                    } else {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $product->decrement('stock_quantity', $item['quantity']);
                            $product->update(['in_stock' => $product->stock_quantity > 0]);
                        }
                    }
                }

                // Limpar carrinho e sessão
                $this->clearCart();
                session()->forget(['temp_order_data', 'checkout_data']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('checkout.success', $order->order_number)
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Pagamento não foi aprovado'
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Página de sucesso do pedido
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('orderItems.product')
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    /**
     * Obter itens do carrinho
     * Usa mesma lógica do CartController para garantir consistência
     */
    private function getCartItems()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Query estrita: só retornar itens que pertencem EXATAMENTE a esta sessão ou cliente
        $query = CartItem::with(['product', 'variation.attributeValues.attribute']);
        
        if ($customerId) {
            // Se logado: só itens do cliente logado E sem session_id
            $query->where('customer_id', $customerId)
                  ->where(function($q) {
                      $q->whereNull('session_id')
                        ->orWhere('session_id', '');
                  });
        } else {
            // Se não logado: só itens da sessão atual E sem customer_id
            $query->where('session_id', $sessionId)
                  ->where(function($q) {
                      $q->whereNull('customer_id')
                        ->orWhere('customer_id', 0);
                  });
        }
        
        return $query->get();
    }

    /**
     * Calcular subtotal
     */
    private function calculateSubtotal()
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum('total');
    }

    /**
     * Calcular frete
     */
    private function calculateShipping()
    {
        $selection = session('shipping_selection');
        if (is_array($selection) && isset($selection['price'])) {
            return (float) $selection['price'];
        }
        return 0.0;
    }

    /**
     * Limpar carrinho
     * Usa mesma lógica do CartController
     */
    private function clearCart()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        CartItem::where(function ($query) use ($sessionId, $customerId) {
            if ($customerId) {
                $query->where('customer_id', $customerId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();
        
        // Limpar seleção de frete
        Session::forget('shipping_selection');
    }

    /**
     * Obter ID da sessão do carrinho
     * Usa mesma lógica do CartController para garantir consistência
     */
    private function getSessionId()
    {
        $sessionKey = 'cart_session_id';
        
        if (!Session::has($sessionKey)) {
            // Usar o ID da sessão do Laravel como base (já é único por navegador/sessão)
            // Adicionar um hash adicional para garantir unicidade absoluta
            $laravelSessionId = session()->getId();
            $uniqueId = 'cart_' . $laravelSessionId . '_' . md5($laravelSessionId . time() . uniqid('', true));
            
            // Armazenar na sessão do Laravel (que é isolada por navegador)
            Session::put($sessionKey, $uniqueId);
            
            // Garantir que a sessão seja persistida
            Session::save();
        }
        
        return Session::get($sessionKey);
    }
}
