<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Exibe a página de checkout
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }
        
        $subtotal = $this->calculateSubtotal($cartItems);
        
        return view('checkout.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Processa o pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_cpf' => 'nullable|string|max:14',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:50',
            'shipping_zip' => 'required|string|max:10',
            'payment_method' => 'required|string|in:credit_card,pix,boleto',
        ]);

        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $subtotal = $this->calculateSubtotal($cartItems);

        DB::beginTransaction();
        
        try {
            // Criar o pedido
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => Auth::guard('customer')->id(), // Pode ser null para usuários não logados
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                
                // Informações de entrega
                'shipping_first_name' => explode(' ', $request->customer_name)[0] ?? $request->customer_name,
                'shipping_last_name' => count(explode(' ', $request->customer_name)) > 1 ? implode(' ', array_slice(explode(' ', $request->customer_name), 1)) : '',
                'shipping_company' => null,
                'shipping_address' => $request->shipping_address,
                'shipping_number' => null,
                'shipping_complement' => null,
                'shipping_neighborhood' => '',
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip_code' => $request->shipping_zip,
                'shipping_phone' => $request->customer_phone,
                
                // Informações de cobrança (mesmo que entrega)
                'billing_first_name' => explode(' ', $request->customer_name)[0] ?? $request->customer_name,
                'billing_last_name' => count(explode(' ', $request->customer_name)) > 1 ? implode(' ', array_slice(explode(' ', $request->customer_name), 1)) : '',
                'billing_company' => null,
                'billing_address' => $request->shipping_address,
                'billing_number' => null,
                'billing_complement' => null,
                'billing_neighborhood' => '',
                'billing_city' => $request->shipping_city,
                'billing_state' => $request->shipping_state,
                'billing_zip_code' => $request->shipping_zip,
                
                // Método de pagamento
                'payment_method' => $request->payment_method,
                'payment_details' => null,
                
                // Observações
                'notes' => null,
                'admin_notes' => null,
            ]);

            // Criar os itens do pedido
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->total,
                ]);

                // Atualizar estoque
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Processar pagamento com Mercado Pago
            $paymentService = new PaymentService();
            
            // Mapear método de pagamento para Mercado Pago
            $mpPaymentMethod = $this->mapPaymentMethodToMercadoPago($request->payment_method);
            
            // Preparar dados do pagamento
            $paymentData = [
                'email' => $request->customer_email,
                'first_name' => explode(' ', $request->customer_name)[0] ?? $request->customer_name,
                'last_name' => count(explode(' ', $request->customer_name)) > 1 ? implode(' ', array_slice(explode(' ', $request->customer_name), 1)) : '',
                'cpf' => $request->customer_cpf,
                'payment_method_id' => $mpPaymentMethod,
                'address' => [
                    'street' => $request->shipping_address,
                    'city' => $request->shipping_city,
                    'state' => $request->shipping_state,
                    'postal_code' => $request->shipping_zip,
                ]
            ];

            // Metadados do pedido
            $metadata = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $request->customer_email,
            ];

            // Processar pagamento
            $paymentResult = $paymentService->processMercadoPagoPayment(
                $subtotal,
                'BRL',
                $paymentData,
                $metadata
            );

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
                        'cart_items' => $cartItems->toArray()
                    ],
                    'checkout_data' => $paymentResult
                ]);

                DB::commit();

                // Redirecionar para página de pagamento (sem pedido criado ainda)
                return redirect()->route('checkout.payment.temp')
                    ->with('success', 'Complete o pagamento para finalizar seu pedido.');
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
     * Obtém itens do carrinho
     */
    private function getCartItems()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        return CartItem::with('product')
            ->where(function ($query) use ($sessionId, $customerId) {
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();
    }

    /**
     * Calcula subtotal do carrinho
     */
    private function calculateSubtotal($cartItems)
    {
        return $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Gera número do pedido
     */
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Limpa o carrinho
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
    }

    /**
     * Obtém ID da sessão
     */
    private function getSessionId()
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', uniqid());
        }
        return Session::get('cart_session_id');
    }

    /**
     * Mapeia método de pagamento para Mercado Pago
     */
    private function mapPaymentMethodToMercadoPago($method)
    {
        $mapping = [
            'credit_card' => 'credit_card',
            'pix' => 'pix',
            'boleto' => 'bolbradesco'
        ];

        return $mapping[$method] ?? 'pix';
    }

    /**
     * Página de pagamento personalizada
     */
    public function payment($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('orderItems.product')
            ->firstOrFail();

        $paymentDetails = json_decode($order->payment_details, true);
        
        // Verificar se temos dados de checkout personalizado
        if (isset($paymentDetails['checkout_data'])) {
            return view('checkout.payment-custom', compact('order', 'paymentDetails'));
        }
        
        // Fallback para a página antiga (PIX)
        return view('checkout.payment', compact('order', 'paymentDetails'));
    }

    /**
     * Processar pagamento com token do Mercado Pago
     */
    public function processPayment(Request $request, $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)->firstOrFail();
            
            $request->validate([
                'token' => 'required|string',
                'payment_method_id' => 'required|string'
            ]);

            $paymentDetails = json_decode($order->payment_details, true);
            $checkoutData = $paymentDetails['checkout_data'] ?? null;
            
            if (!$checkoutData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Dados de checkout não encontrados'
                ], 400);
            }

            $paymentService = new PaymentService();
            $paymentResult = $paymentService->processMercadoPagoWithToken(
                $request->token,
                $checkoutData['amount'],
                $checkoutData['currency'],
                $checkoutData['payer'],
                ['order_id' => $order->id]
            );

            if ($paymentResult['success']) {
                // Atualizar pedido com resultado do pagamento
                $order->update([
                    'payment_status' => $paymentResult['status'],
                    'payment_details' => json_encode(array_merge($paymentDetails, [
                        'payment_id' => $paymentResult['payment_id'],
                        'status' => $paymentResult['status'],
                        'provider' => 'mercadopago'
                    ]))
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $paymentResult['status'],
                    'redirect_url' => route('checkout.success', $order->order_number)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $paymentResult['error']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status do pagamento
     */
    public function checkPaymentStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        $paymentDetails = json_decode($order->payment_details, true);
        
        if ($paymentDetails && isset($paymentDetails['payment_id'])) {
            $paymentService = new PaymentService();
            $statusResult = $paymentService->checkPaymentStatus('mercadopago', $paymentDetails['payment_id']);
            
            if ($statusResult['success']) {
                // Atualizar status do pedido
                $order->update([
                    'payment_status' => $statusResult['status'],
                    'payment_details' => json_encode(array_merge($paymentDetails, [
                        'status' => $statusResult['status']
                    ]))
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $statusResult['status']
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'status' => 'pending'
        ]);
    }
}
