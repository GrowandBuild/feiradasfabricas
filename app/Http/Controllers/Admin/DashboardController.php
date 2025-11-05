<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
        ];

        // Vendas por mês (últimos 12 meses)
        $monthlySales = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('status', '!=', 'cancelled')
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        // Preparar dados para o gráfico
        $salesData = $monthlySales->pluck('total');
        $salesLabels = $monthlySales->map(function($item) {
            return \Carbon\Carbon::create($item->year, $item->month, 1)->format('M/Y');
        })->toArray();

        // Top produtos vendidos
        $topProducts = Product::withCount(['orderItems as sales_count' => function ($query) {
            $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                  ->where('orders.status', '!=', 'cancelled');
        }])
        ->orderBy('sales_count', 'desc')
        ->limit(10)
        ->get();

        // Pedidos recentes
        $recentOrders = Order::with(['customer', 'orderItems.product'])
                           ->latest()
                           ->limit(10)
                           ->get();

        // Clientes por tipo
        $customersByType = [
            'b2c' => Customer::where('type', 'b2c')->count(),
            'b2b' => Customer::where('type', 'b2b')->count(),
        ];

        // Status dos pedidos
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
                             ->groupBy('status')
                             ->get()
                             ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'stats',
            'monthlySales',
            'salesData',
            'salesLabels',
            'topProducts',
            'recentOrders',
            'customersByType',
            'ordersByStatus'
        ));
    }
}
