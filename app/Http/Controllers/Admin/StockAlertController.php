<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Notification;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockAlertController extends Controller
{
    public function index()
    {
        $alerts = [
            'low_stock' => $this->getLowStockProducts(),
            'out_of_stock' => $this->getOutOfStockProducts(),
            'fast_moving' => $this->getFastMovingProducts(),
            'slow_moving' => $this->getSlowMovingProducts(),
            'high_value' => $this->getHighValueLowStockProducts(),
        ];

        return view('admin.products.stock-alerts', compact('alerts'));
    }

    public function createAlert(Request $request)
    {
        $request->validate([
            'type' => 'required|in:low_stock,out_of_stock,custom',
            'product_id' => 'required|exists:products,id',
            'threshold' => 'nullable|integer|min:0',
            'message' => 'nullable|string|max:255',
        ]);

        $product = Product::find($request->product_id);
        
        Notification::create([
            'title' => $this->getAlertTitle($request->type),
            'message' => $request->message ?: $this->getDefaultMessage($request->type, $product),
            'type' => $request->type,
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'threshold' => $request->threshold,
            ],
            'target_type' => 'admin',
            'target_id' => auth('admin')->id(),
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Alerta criado com sucesso!');
    }

    public function bulkCreateAlerts(Request $request)
    {
        $request->validate([
            'alert_type' => 'required|in:low_stock,out_of_stock,reorder',
            'brand' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'threshold' => 'required|integer|min:0',
        ]);

        $query = Product::query();

        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->get();
        $created = 0;

        foreach ($products as $product) {
            $shouldCreateAlert = match($request->alert_type) {
                'low_stock' => $product->stock_quantity <= $request->threshold,
                'out_of_stock' => $product->stock_quantity == 0,
                'reorder' => $product->stock_quantity <= $product->min_stock,
            };

            if ($shouldCreateAlert) {
                Notification::create([
                    'title' => $this->getAlertTitle($request->alert_type),
                    'message' => $this->getDefaultMessage($request->alert_type, $product),
                    'type' => $request->alert_type,
                    'data' => [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'current_stock' => $product->stock_quantity,
                        'threshold' => $request->threshold,
                    ],
                    'target_type' => 'admin',
                    'target_id' => auth('admin')->id(),
                    'is_read' => false,
                ]);
                $created++;
            }
        }

        return redirect()->back()
                        ->with('success', "{$created} alertas criados automaticamente!");
    }

    public function getStockReport()
    {
        $report = [
            'summary' => [
                'total_products' => Product::count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                'low_stock' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
                'total_value' => Product::sum(\DB::raw('stock_quantity * cost_price')),
            ],
            'brands' => Product::selectRaw('brand, 
                COUNT(*) as total_products,
                SUM(stock_quantity) as total_stock,
                AVG(stock_quantity) as avg_stock')
                ->whereNotNull('brand')
                ->groupBy('brand')
                ->orderBy('total_stock', 'desc')
                ->get(),
            'categories' => Product::selectRaw('categories.name as category_name,
                COUNT(products.id) as total_products,
                SUM(products.stock_quantity) as total_stock')
                ->join('category_product', 'products.id', '=', 'category_product.product_id')
                ->join('categories', 'categories.id', '=', 'category_product.category_id')
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_stock', 'desc')
                ->get(),
        ];

        return view('admin.products.stock-report', compact('report'));
    }

    private function getLowStockProducts()
    {
        return Product::whereColumn('stock_quantity', '<=', 'min_stock')
                     ->where('stock_quantity', '>', 0)
                     ->with('categories')
                     ->orderBy('stock_quantity', 'asc')
                     ->limit(20)
                     ->get();
    }

    private function getOutOfStockProducts()
    {
        return Product::where('stock_quantity', 0)
                     ->with('categories')
                     ->orderBy('updated_at', 'desc')
                     ->limit(20)
                     ->get();
    }

    private function getFastMovingProducts()
    {
        // Produtos com muitas saídas recentes
        return Product::selectRaw('products.*, 
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END), 0) as recent_sales')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([Carbon::now()->subDays(30)])
            ->groupBy('products.id')
            ->having('recent_sales', '>', 0)
            ->orderBy('recent_sales', 'desc')
            ->limit(10)
            ->get();
    }

    private function getSlowMovingProducts()
    {
        // Produtos sem saídas recentes
        return Product::whereNotExists(function ($query) {
            $query->select(\DB::raw(1))
                  ->from('inventory_logs')
                  ->whereColumn('inventory_logs.product_id', 'products.id')
                  ->where('inventory_logs.type', 'out')
                  ->where('inventory_logs.created_at', '>=', Carbon::now()->subDays(60));
        })
        ->where('stock_quantity', '>', 0)
        ->orderBy('stock_quantity', 'desc')
        ->limit(10)
        ->get();
    }

    private function getHighValueLowStockProducts()
    {
        return Product::whereColumn('stock_quantity', '<=', 'min_stock')
                     ->where('stock_quantity', '>', 0)
                     ->orderBy(\DB::raw('cost_price * stock_quantity'), 'desc')
                     ->limit(10)
                     ->get();
    }

    private function getAlertTitle($type)
    {
        return match($type) {
            'low_stock' => 'Estoque Baixo',
            'out_of_stock' => 'Produto Esgotado',
            'reorder' => 'Reposição Necessária',
            'fast_moving' => 'Produto em Alta Demanda',
            'slow_moving' => 'Produto com Baixa Rotatividade',
            default => 'Alerta de Estoque'
        };
    }

    private function getDefaultMessage($type, $product)
    {
        return match($type) {
            'low_stock' => "O produto {$product->name} está com estoque baixo ({$product->stock_quantity} unidades).",
            'out_of_stock' => "O produto {$product->name} está esgotado.",
            'reorder' => "O produto {$product->name} precisa ser reposto ({$product->stock_quantity} unidades restantes).",
            'fast_moving' => "O produto {$product->name} está com alta demanda e pode precisar de reposição.",
            'slow_moving' => "O produto {$product->name} está com baixa rotatividade e pode precisar de promoção.",
            default => "Alerta para o produto {$product->name}."
        };
    }
}
