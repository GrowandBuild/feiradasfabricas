<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\EmailService;
use App\Services\FiscalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lista pedidos do cliente
     */
    public function index(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        
        $query = Order::with(['orderItems.product'])
            ->where('customer_id', $customerId);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Exibe detalhes de um pedido
     */
    public function show(Order $order)
    {
        // Verificar se o pedido pertence ao cliente
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Acesso negado.');
        }

        $order->load(['orderItems.product', 'customer']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Cria um novo pedido a partir do carrinho
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_number' => 'required|string|max:20',
            'shipping_neighborhood' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:2',
            'shipping_zip_code' => 'required|string|max:10',
            'shipping_phone' => 'required|string|max:20',
            'billing_first_name' => 'nullable|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
            'billing_number' => 'nullable|string|max:20',
            'billing_neighborhood' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:2',
            'billing_zip_code' => 'nullable|string|max:10',
            'payment_method' => 'required|string|in:credit_card,debit_card,pix,boleto,transfer',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customerId = Auth::guard('customer')->id();
        $cartItems = CartItem::with('product')
            ->where('customer_id', $customerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Carrinho vazio.'
            ], 400);
        }

        // Verificar estoque antes de criar o pedido
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Produto '{$item->product->name}' não disponível em estoque suficiente."
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Criar pedido
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customerId,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'subtotal' => $cartItems->sum('total'),
                'tax_amount' => 0, // Implementar cálculo de impostos se necessário
                'shipping_amount' => 0, // Implementar cálculo de frete
                'discount_amount' => 0, // Implementar sistema de cupons
                'total_amount' => $cartItems->sum('total'),
                'shipping_first_name' => $request->shipping_first_name,
                'shipping_last_name' => $request->shipping_last_name,
                'shipping_address' => $request->shipping_address,
                'shipping_number' => $request->shipping_number,
                'shipping_neighborhood' => $request->shipping_neighborhood,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip_code' => $request->shipping_zip_code,
                'shipping_phone' => $request->shipping_phone,
                'billing_first_name' => $request->billing_first_name ?: $request->shipping_first_name,
                'billing_last_name' => $request->billing_last_name ?: $request->shipping_last_name,
                'billing_address' => $request->billing_address ?: $request->shipping_address,
                'billing_number' => $request->billing_number ?: $request->shipping_number,
                'billing_neighborhood' => $request->billing_neighborhood ?: $request->shipping_neighborhood,
                'billing_city' => $request->billing_city ?: $request->shipping_city,
                'billing_state' => $request->billing_state ?: $request->shipping_state,
                'billing_zip_code' => $request->billing_zip_code ?: $request->shipping_zip_code,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Criar itens do pedido e atualizar estoque
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->total,
                ]);

                // Atualizar estoque
                $product = $cartItem->product;
                $product->decrement('stock_quantity', $cartItem->quantity);
                $product->update(['in_stock' => $product->stock_quantity > 0]);
            }

            // Limpar carrinho
            CartItem::where('customer_id', $customerId)->delete();

            DB::commit();

            // Enviar email de confirmação
            try {
                $emailService = new EmailService();
                $emailService->enviarConfirmacaoPedido($order);
            } catch (\Exception $e) {
                // Log do erro, mas não falha o pedido
                \Log::error('Erro ao enviar email de confirmação: ' . $e->getMessage());
            }

            // Emitir nota fiscal se habilitado
            if (setting('sefaz_enabled', false) && setting('sefaz_auto_emitir', false)) {
                try {
                    $fiscalService = new FiscalService();
                    $fiscalService->emitirNotaFiscal($order);
                } catch (\Exception $e) {
                    // Log do erro, mas não falha o pedido
                    \Log::error('Erro ao emitir nota fiscal: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('orders.show', $order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Cancela um pedido
     */
    public function cancel(Order $order)
    {
        // Verificar se o pedido pertence ao cliente
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode cancelar
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido não pode ser cancelado.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Atualizar status
            $order->update(['status' => 'cancelled']);

            // Devolver produtos ao estoque
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                $product->increment('stock_quantity', $item->quantity);
                $product->update(['in_stock' => $product->stock_quantity > 0]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar pedido. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Reordena um pedido (adiciona itens ao carrinho)
     */
    public function reorder(Order $order)
    {
        // Verificar se o pedido pertence ao cliente
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Acesso negado.');
        }

        $customerId = Auth::guard('customer')->id();
        $addedItems = 0;

        foreach ($order->orderItems as $orderItem) {
            // Verificar se o produto ainda está disponível
            if (!$orderItem->product->in_stock) {
                continue;
            }

            // Verificar se já existe no carrinho
            $existingCartItem = CartItem::where('customer_id', $customerId)
                ->where('product_id', $orderItem->product_id)
                ->first();

            if ($existingCartItem) {
                // Atualizar quantidade
                $newQuantity = min(
                    $existingCartItem->quantity + $orderItem->quantity,
                    $orderItem->product->stock_quantity
                );
                $existingCartItem->update(['quantity' => $newQuantity]);
            } else {
                // Criar novo item no carrinho
                CartItem::create([
                    'customer_id' => $customerId,
                    'product_id' => $orderItem->product_id,
                    'quantity' => min($orderItem->quantity, $orderItem->product->stock_quantity),
                    'price' => $orderItem->product->price,
                ]);
            }

            $addedItems++;
        }

        if ($addedItems > 0) {
            return response()->json([
                'success' => true,
                'message' => "{$addedItems} item(s) adicionado(s) ao carrinho!",
                'redirect_url' => route('cart.index')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum item disponível para reordenação.'
            ], 400);
        }
    }

    /**
     * Obtém estatísticas do cliente
     */
    public function stats()
    {
        $customerId = Auth::guard('customer')->id();
        
        $stats = [
            'total_orders' => Order::where('customer_id', $customerId)->count(),
            'pending_orders' => Order::where('customer_id', $customerId)
                ->whereIn('status', ['pending', 'processing'])->count(),
            'completed_orders' => Order::where('customer_id', $customerId)
                ->where('status', 'delivered')->count(),
            'total_spent' => Order::where('customer_id', $customerId)
                ->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}
