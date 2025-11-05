<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvancedSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Product::query();

        // Filtros básicos
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filtros avançados
        if ($request->filled('brand')) {
            $query->whereIn('brand', (array) $request->brand);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', (array) $request->category_id);
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('stock_min')) {
            $query->where('stock_quantity', '>=', $request->stock_min);
        }

        if ($request->filled('stock_max')) {
            $query->where('stock_quantity', '<=', $request->stock_max);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'min_stock');
        }

        // Filtros de data
        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', $request->created_from);
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', $request->created_to . ' 23:59:59');
        }

        // Ordenação
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        
        $allowedSorts = ['name', 'price', 'stock_quantity', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Busca por especificações
        if ($request->filled('specifications')) {
            $specs = json_decode($request->specifications, true);
            if (is_array($specs)) {
                foreach ($specs as $key => $value) {
                    $query->whereJsonContains('specifications->' . $key, $value);
                }
            }
        }

        // Busca por faixa de margem
        if ($request->filled('margin_min') || $request->filled('margin_max')) {
            $query->selectRaw('*, ((price - cost_price) / price * 100) as margin_percentage');
            
            if ($request->filled('margin_min')) {
                $query->having('margin_percentage', '>=', $request->margin_min);
            }
            
            if ($request->filled('margin_max')) {
                $query->having('margin_percentage', '<=', $request->margin_max);
            }
        }

        $products = $query->with('categories')->paginate(20);
        
        // Dados para filtros
        $filters = [
            'brands' => Product::distinct()->pluck('brand')->filter()->sort()->values(),
            'categories' => Category::orderBy('name')->get(),
            'price_range' => [
                'min' => Product::min('price'),
                'max' => Product::max('price'),
            ],
            'stock_range' => [
                'min' => Product::min('stock_quantity'),
                'max' => Product::max('stock_quantity'),
            ],
        ];

        return view('admin.products.advanced-search', compact('products', 'filters'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,export,adjust_stock,adjust_price',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $productIds = $request->product_ids;
        $updated = 0;

        switch ($request->action) {
            case 'activate':
                $updated = Product::whereIn('id', $productIds)->update(['is_active' => true]);
                break;
                
            case 'deactivate':
                $updated = Product::whereIn('id', $productIds)->update(['is_active' => false]);
                break;
                
            case 'delete':
                Product::whereIn('id', $productIds)->delete();
                $updated = count($productIds);
                break;
                
            case 'adjust_stock':
                $request->validate([
                    'stock_action' => 'required|in:add,subtract,set',
                    'stock_value' => 'required|integer',
                ]);
                
                $products = Product::whereIn('id', $productIds)->get();
                foreach ($products as $product) {
                    $newStock = match($request->stock_action) {
                        'add' => $product->stock_quantity + $request->stock_value,
                        'subtract' => max(0, $product->stock_quantity - $request->stock_value),
                        'set' => $request->stock_value,
                    };
                    
                    $product->update([
                        'stock_quantity' => $newStock,
                        'in_stock' => $newStock > 0,
                    ]);
                    $updated++;
                }
                break;
                
            case 'adjust_price':
                $request->validate([
                    'price_action' => 'required|in:percentage,fixed',
                    'price_value' => 'required|numeric',
                ]);
                
                $products = Product::whereIn('id', $productIds)->get();
                foreach ($products as $product) {
                    $newPrice = match($request->price_action) {
                        'percentage' => $product->price * (1 + $request->price_value / 100),
                        'fixed' => $product->price + $request->price_value,
                    };
                    
                    $product->update([
                        'price' => round($newPrice, 2),
                        'b2b_price' => round($newPrice * 0.9, 2),
                    ]);
                    $updated++;
                }
                break;
        }

        return redirect()->back()->with('success', "Ação executada em {$updated} produtos!");
    }

    public function exportResults(Request $request)
    {
        // Replicar a mesma query de busca
        $query = Product::query();
        
        // Aplicar todos os filtros da busca atual
        $this->applySearchFilters($query, $request);
        
        $products = $query->with('categories')->get();
        
        $filename = 'produtos_busca_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Cabeçalho
        fputcsv($handle, [
            'ID', 'Nome', 'SKU', 'Marca', 'Modelo', 'Preço', 'Preço B2B', 
            'Estoque', 'Categoria', 'Ativo', 'Destaque', 'Data Criação'
        ]);
        
        // Dados
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->name,
                $product->sku,
                $product->brand,
                $product->model,
                $product->price,
                $product->b2b_price,
                $product->stock_quantity,
                $product->categories->pluck('name')->join(', '),
                $product->is_active ? 'Sim' : 'Não',
                $product->is_featured ? 'Sim' : 'Não',
                $product->created_at->format('d/m/Y H:i'),
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function getSearchSuggestions(Request $request)
    {
        $query = $request->q;
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [
            'products' => Product::where('name', 'like', "%{$query}%")
                               ->orWhere('sku', 'like', "%{$query}%")
                               ->limit(5)
                               ->get(['id', 'name', 'sku', 'brand']),
            'brands' => Product::where('brand', 'like', "%{$query}%")
                              ->distinct()
                              ->limit(5)
                              ->pluck('brand'),
            'models' => Product::where('model', 'like', "%{$query}%")
                              ->distinct()
                              ->limit(5)
                              ->pluck('model'),
        ];

        return response()->json($suggestions);
    }

    private function applySearchFilters($query, $request)
    {
        // Aplicar todos os filtros da função search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->whereIn('brand', (array) $request->brand);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', (array) $request->category_id);
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('stock_min')) {
            $query->where('stock_quantity', '>=', $request->stock_min);
        }

        if ($request->filled('stock_max')) {
            $query->where('stock_quantity', '<=', $request->stock_max);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'min_stock');
        }
    }
}
