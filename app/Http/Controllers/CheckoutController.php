<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Processar checkout (NOVO FLUXO SEGURO)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_cpf' => 'nullable|string|max:14',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:2',
            'shipping_zip' => 'required|string|max:10',
            'payment_method' => 'required|in:credit_card,pix,boleto'
        ]);

        try {
            DB::beginTransaction();

            // Obter itens do carrinho
            $cartItems = $this->getCartItems();
            
            if ($cartItems->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Seu carrinho está vazio.')
                    ->withInput();
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
                    'street' => $request->shipping_address,
                    'city' => $request->shipping_city,
                    'state' => $request->shipping_state,
                    'postal_code' => $request->shipping_zip,
                ]
            ];

            // Preparar itens do carrinho para metadados
            $cartItemsArray = $cartItems->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'product' => [
                        'name' => $item->product->name ?? 'Produto',
                        'sku' => $item->product->sku ?? '',
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
                session([
                    'temp_order_data' => [
                        'customer_name' => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone ?? null,
                        'customer_cpf' => $request->customer_cpf ?? null,
                        'shipping_address' => $request->shipping_address,
                        'shipping_city' => $request->shipping_city,
                        'shipping_state' => $request->shipping_state,
                        'shipping_zip' => $request->shipping_zip,
                        'payment_method' => $request->payment_method,
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shipping,
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

            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_name' => $tempOrderData['customer_name'],
                'customer_email' => $tempOrderData['customer_email'],
                'customer_phone' => $tempOrderData['customer_phone'],
                'customer_cpf' => $tempOrderData['customer_cpf'],
                'shipping_address' => $tempOrderData['shipping_address'],
                'shipping_city' => $tempOrderData['shipping_city'],
                'shipping_state' => $tempOrderData['shipping_state'],
                'shipping_zip' => $tempOrderData['shipping_zip'],
                'payment_method' => $tempOrderData['payment_method'],
                'subtotal' => $tempOrderData['subtotal'],
                'shipping_cost' => $tempOrderData['shipping_cost'],
                'total_amount' => $tempOrderData['total_amount'],
                'status' => 'paid',
                'payment_status' => $status,
                'payment_details' => json_encode([
                    'payment_id' => $checkoutData['payment_id'] ?? null,
                    'status' => $status,
                    'provider' => 'mercadopago'
                ])
            ]);

            // Criar itens do pedido
            foreach ($tempOrderData['cart_items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product']['name'],
                    'product_sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                // Atualizar estoque
                Product::find($item['product_id'])->decrement('stock_quantity', $item['quantity']);
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

            // SÓ criar o pedido se o pagamento for aprovado
            if ($paymentResult['status'] === 'approved' || $paymentResult['status'] === 'paid') {
                $order = Order::create([
                    'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'customer_name' => $tempOrderData['customer_name'],
                    'customer_email' => $tempOrderData['customer_email'],
                    'customer_phone' => $tempOrderData['customer_phone'],
                    'customer_cpf' => $tempOrderData['customer_cpf'],
                    'shipping_address' => $tempOrderData['shipping_address'],
                    'shipping_city' => $tempOrderData['shipping_city'],
                    'shipping_state' => $tempOrderData['shipping_state'],
                    'shipping_zip' => $tempOrderData['shipping_zip'],
                    'payment_method' => $tempOrderData['payment_method'],
                    'subtotal' => $tempOrderData['subtotal'],
                    'shipping_cost' => $tempOrderData['shipping_cost'],
                    'total_amount' => $tempOrderData['total_amount'],
                    'status' => 'paid', // Pedido pago
                    'payment_status' => $paymentResult['status'],
                    'payment_details' => json_encode([
                        'payment_id' => $paymentResult['payment_id'],
                        'status' => $paymentResult['status'],
                        'provider' => 'mercadopago'
                    ])
                ]);

                // Criar itens do pedido
                foreach ($tempOrderData['cart_items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product']['name'],
                        'product_sku' => $item['product']['sku'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['total'],
                    ]);

                    // Atualizar estoque
                    Product::find($item['product_id'])->decrement('stock_quantity', $item['quantity']);
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
     */
    private function getCartItems()
    {
        $sessionId = $this->getCartSessionId();
        return CartItem::where('session_id', $sessionId)
            ->with('product')
            ->get();
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
        // Implementar lógica de frete
        return 0;
    }

    /**
     * Limpar carrinho
     */
    private function clearCart()
    {
        $sessionId = $this->getCartSessionId();
        CartItem::where('session_id', $sessionId)->delete();
    }

    /**
     * Obter ID da sessão do carrinho
     */
    private function getCartSessionId()
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', uniqid());
        }
        return Session::get('cart_session_id');
    }
}
