<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('customer_type')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('type', $request->customer_type);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product', 'couponUsages.coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:255',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Atualizar status
        $order->update([
            'status' => $newStatus,
            'notes' => $request->notes ?: $order->notes,
        ]);

        // Se cancelado, devolver produtos ao estoque
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                $product->increment('stock_quantity', $item->quantity);
                $product->update(['in_stock' => $product->stock_quantity > 0]);
            }
        }

        return redirect()->back()
                        ->with('success', 'Status do pedido atualizado com sucesso!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,partial,refunded,failed',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->back()
                        ->with('success', 'Status do pagamento atualizado com sucesso!');
    }

    public function print(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);
        return view('admin.orders.print', compact('order'));
    }

    public function export(Request $request)
    {
        // Implementar exportação de pedidos (CSV, Excel, etc.)
        // Por enquanto, retornar uma resposta simples
        return response()->json(['message' => 'Exportação será implementada']);
    }
}
