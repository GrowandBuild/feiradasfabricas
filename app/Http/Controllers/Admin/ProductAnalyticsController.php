<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // dias
        $brand = $request->get('brand');
        
        $analytics = [
            'overview' => $this->getOverviewStats($period),
            'top_products' => $this->getTopProducts($period, $brand),
            'brand_performance' => $this->getBrandPerformance($period),
            'category_performance' => $this->getCategoryPerformance($period),
            'stock_analysis' => $this->getStockAnalysis(),
            'price_analysis' => $this->getPriceAnalysis(),
            'sales_trends' => $this->getSalesTrends($period),
            'profitability' => $this->getProfitabilityAnalysis($period),
        ];

        return view('admin.analytics.products', compact('analytics', 'period', 'brand'));
    }

    public function getProductDetails($productId)
    {
        $product = Product::with(['categories', 'inventoryLogs'])->findOrFail($productId);
        
        $analytics = [
            'basic_info' => $product,
            'sales_history' => $this->getProductSalesHistory($productId),
            'stock_movements' => $this->getProductStockMovements($productId),
            'profitability' => $this->getProductProfitability($productId),
            'performance_metrics' => $this->getProductPerformanceMetrics($productId),
        ];

        return view('admin.analytics.product-details', compact('analytics'));
    }

    public function exportAnalytics(Request $request)
    {
        $period = $request->get('period', '30');
        $format = $request->get('format', 'csv');
        
        $data = $this->getAnalyticsDataForExport($period);
        
        if ($format === 'csv') {
            return $this->exportToCSV($data, $period);
        } else {
            return $this->exportToJSON($data, $period);
        }
    }

    private function getOverviewStats($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
            'total_inventory_value' => Product::sum(DB::raw('stock_quantity * cost_price')),
            'avg_margin' => Product::selectRaw('AVG((price - cost_price) / price * 100) as avg_margin')->value('avg_margin'),
            'new_products' => Product::where('created_at', '>=', $startDate)->count(),
            'top_selling_brand' => $this->getTopSellingBrand($period),
        ];
    }

    private function getTopProducts($period, $brand = null)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $query = Product::selectRaw('products.*, 
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END), 0) as units_sold,
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change * products.price ELSE 0 END), 0) as revenue')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([$startDate, $startDate])
            ->groupBy('products.id');

        if ($brand) {
            $query->where('products.brand', $brand);
        }

        return $query->orderBy('units_sold', 'desc')
                    ->limit(10)
                    ->get();
    }

    private function getBrandPerformance($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return Product::selectRaw('brand,
            COUNT(*) as total_products,
            SUM(stock_quantity) as total_stock,
            AVG(price) as avg_price,
            SUM(stock_quantity * cost_price) as inventory_value,
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END), 0) as units_sold,
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change * products.price ELSE 0 END), 0) as revenue')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([$startDate, $startDate])
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderBy('revenue', 'desc')
            ->get();
    }

    private function getCategoryPerformance($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return DB::table('categories')
            ->selectRaw('categories.name as category_name,
                COUNT(products.id) as total_products,
                AVG(products.price) as avg_price,
                SUM(products.stock_quantity * products.cost_price) as inventory_value,
                COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END), 0) as units_sold')
            ->join('category_product', 'categories.id', '=', 'category_product.category_id')
            ->join('products', 'products.id', '=', 'category_product.product_id')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([$startDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('units_sold', 'desc')
            ->get();
    }

    private function getStockAnalysis()
    {
        return [
            'stock_levels' => [
                'high' => Product::where('stock_quantity', '>', DB::raw('min_stock * 3'))->count(),
                'normal' => Product::whereBetween('stock_quantity', [DB::raw('min_stock'), DB::raw('min_stock * 3')])->count(),
                'low' => Product::whereColumn('stock_quantity', '<=', 'min_stock')->where('stock_quantity', '>', 0)->count(),
                'out' => Product::where('stock_quantity', 0)->count(),
            ],
            'turnover_rate' => $this->calculateTurnoverRate(),
            'abc_analysis' => $this->getABCAnalysis(),
        ];
    }

    private function getPriceAnalysis()
    {
        return [
            'price_distribution' => [
                'under_500' => Product::where('price', '<', 500)->count(),
                '500_1000' => Product::whereBetween('price', [500, 1000])->count(),
                '1000_2000' => Product::whereBetween('price', [1000, 2000])->count(),
                '2000_5000' => Product::whereBetween('price', [2000, 5000])->count(),
                'over_5000' => Product::where('price', '>', 5000)->count(),
            ],
            'margin_analysis' => [
                'high_margin' => Product::selectRaw('*, ((price - cost_price) / price * 100) as margin')->having('margin', '>', 50)->count(),
                'medium_margin' => Product::selectRaw('*, ((price - cost_price) / price * 100) as margin')->havingBetween('margin', [20, 50])->count(),
                'low_margin' => Product::selectRaw('*, ((price - cost_price) / price * 100) as margin')->having('margin', '<', 20)->count(),
            ],
            'price_vs_competition' => $this->getPriceCompetitionAnalysis(),
        ];
    }

    private function getSalesTrends($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return DB::table('inventory_logs')
            ->selectRaw('DATE(created_at) as date,
                SUM(CASE WHEN type = "out" THEN quantity_change ELSE 0 END) as units_sold,
                SUM(CASE WHEN type = "out" THEN quantity_change * (
                    SELECT price FROM products WHERE products.id = inventory_logs.product_id
                ) ELSE 0 END) as revenue')
            ->where('created_at', '>=', $startDate)
            ->where('type', 'out')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProfitabilityAnalysis($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return Product::selectRaw('products.*,
            ((price - cost_price) / price * 100) as margin_percentage,
            (price - cost_price) as profit_per_unit,
            stock_quantity * (price - cost_price) as potential_profit,
            COALESCE(SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END), 0) as units_sold')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([$startDate])
            ->groupBy('products.id')
            ->orderBy('potential_profit', 'desc')
            ->limit(20)
            ->get();
    }

    private function getProductSalesHistory($productId)
    {
        return InventoryLog::where('product_id', $productId)
                          ->where('type', 'out')
                          ->selectRaw('DATE(created_at) as date, SUM(quantity_change) as units_sold')
                          ->groupBy('date')
                          ->orderBy('date', 'desc')
                          ->limit(30)
                          ->get();
    }

    private function getProductStockMovements($productId)
    {
        return InventoryLog::where('product_id', $productId)
                          ->with('admin')
                          ->orderBy('created_at', 'desc')
                          ->limit(20)
                          ->get();
    }

    private function getProductProfitability($productId)
    {
        $product = Product::find($productId);
        
        return [
            'margin_percentage' => ($product->price - $product->cost_price) / $product->price * 100,
            'profit_per_unit' => $product->price - $product->cost_price,
            'total_potential_profit' => $product->stock_quantity * ($product->price - $product->cost_price),
            'roi' => $product->cost_price > 0 ? (($product->price - $product->cost_price) / $product->cost_price) * 100 : 0,
        ];
    }

    private function getProductPerformanceMetrics($productId)
    {
        $product = Product::find($productId);
        
        return [
            'days_in_stock' => $product->created_at->diffInDays(now()),
            'turnover_rate' => $this->calculateProductTurnoverRate($productId),
            'demand_velocity' => $this->calculateDemandVelocity($productId),
            'stock_health_score' => $this->calculateStockHealthScore($product),
        ];
    }

    private function calculateTurnoverRate()
    {
        // Implementar cálculo de taxa de giro
        return 12; // Placeholder
    }

    private function getABCAnalysis()
    {
        $products = Product::selectRaw('*,
            stock_quantity * cost_price as inventory_value')
            ->orderBy('inventory_value', 'desc')
            ->get();

        $totalValue = $products->sum('inventory_value');
        $cumulativeValue = 0;

        return $products->map(function($product) use ($totalValue, &$cumulativeValue) {
            $cumulativeValue += $product->inventory_value;
            $percentage = ($cumulativeValue / $totalValue) * 100;
            
            $classification = 'C';
            if ($percentage <= 80) $classification = 'A';
            elseif ($percentage <= 95) $classification = 'B';
            
            return [
                'product' => $product,
                'percentage' => round($percentage, 2),
                'classification' => $classification,
            ];
        });
    }

    private function getPriceCompetitionAnalysis()
    {
        // Implementar análise de preços vs concorrência
        return [
            'avg_market_price' => 1500,
            'our_avg_price' => Product::avg('price'),
            'price_position' => 'competitive',
        ];
    }

    private function getTopSellingBrand($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return Product::selectRaw('brand,
            SUM(CASE WHEN inventory_logs.type = "out" AND inventory_logs.created_at >= ? THEN inventory_logs.quantity_change ELSE 0 END) as units_sold')
            ->leftJoin('inventory_logs', 'products.id', '=', 'inventory_logs.product_id')
            ->setBindings([$startDate])
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderBy('units_sold', 'desc')
            ->first();
    }

    private function calculateProductTurnoverRate($productId)
    {
        // Implementar cálculo específico do produto
        return 8; // Placeholder
    }

    private function calculateDemandVelocity($productId)
    {
        // Implementar cálculo de velocidade de demanda
        return 15; // Placeholder
    }

    private function calculateStockHealthScore($product)
    {
        $score = 100;
        
        // Penalizar estoque baixo
        if ($product->stock_quantity <= $product->min_stock) {
            $score -= 30;
        }
        
        // Penalizar produtos parados
        if ($product->stock_quantity > $product->min_stock * 5) {
            $score -= 20;
        }
        
        // Bonificar margem boa
        $margin = ($product->price - $product->cost_price) / $product->price * 100;
        if ($margin > 30) {
            $score += 10;
        }
        
        return max(0, min(100, $score));
    }

    private function getAnalyticsDataForExport($period)
    {
        return [
            'overview' => $this->getOverviewStats($period),
            'top_products' => $this->getTopProducts($period),
            'brand_performance' => $this->getBrandPerformance($period),
            'category_performance' => $this->getCategoryPerformance($period),
            'sales_trends' => $this->getSalesTrends($period),
        ];
    }

    private function exportToCSV($data, $period)
    {
        $filename = "analytics_produtos_{$period}d_" . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Cabeçalho
        fputcsv($handle, ['Métrica', 'Valor', 'Período (dias)']);
        
        // Dados
        foreach ($data['overview'] as $key => $value) {
            fputcsv($handle, [$key, $value, $period]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportToJSON($data, $period)
    {
        $filename = "analytics_produtos_{$period}d_" . date('Y-m-d_H-i-s') . '.json';
        
        $exportData = [
            'export_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'period_days' => $period,
                'type' => 'product_analytics'
            ],
            'data' => $data
        ];

        return response()->json($exportData)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
