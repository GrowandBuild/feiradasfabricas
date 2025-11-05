<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class BulkStockController extends Controller
{
    public function bulkStockAdjustment(Request $request)
    {
        $request->validate([
            'action' => 'required|in:add,subtract,set',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer',
            'notes' => 'nullable|string|max:255',
        ]);

        $updated = 0;
        $errors = [];

        foreach ($request->products as $productData) {
            try {
                $product = Product::find($productData['id']);
                $oldStock = $product->stock_quantity;
                $quantity = (int) $productData['quantity'];

                $newStock = match($request->action) {
                    'add' => $oldStock + $quantity,
                    'subtract' => max(0, $oldStock - $quantity),
                    'set' => $quantity,
                };

                // Criar log de estoque
                InventoryLog::create([
                    'product_id' => $product->id,
                    'admin_id' => auth('admin')->id(),
                    'type' => match($request->action) {
                        'add' => 'in',
                        'subtract' => 'out',
                        'set' => 'adjustment',
                    },
                    'quantity_before' => $oldStock,
                    'quantity_change' => $newStock - $oldStock,
                    'quantity_after' => $newStock,
                    'notes' => $request->notes ?: 'Ajuste em lote',
                    'reference' => 'Ajuste em lote',
                ]);

                // Atualizar produto
                $product->update([
                    'stock_quantity' => $newStock,
                    'in_stock' => $newStock > 0,
                ]);

                $updated++;
            } catch (\Exception $e) {
                $errors[] = "Produto {$product->name}: " . $e->getMessage();
            }
        }

        $response = redirect()->back()->with('success', "Estoque de {$updated} produtos atualizado!");
        
        if (!empty($errors)) {
            $response->with('errors', $errors);
        }

        return $response;
    }

    public function lowStockReport()
    {
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock')
                                 ->with('categories')
                                 ->orderBy('stock_quantity', 'asc')
                                 ->get();

        return view('admin.products.low-stock-report', compact('lowStockProducts'));
    }

    public function bulkSetMinStock(Request $request)
    {
        $request->validate([
            'brand' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'min_stock' => 'required|integer|min:0',
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

        $updated = $query->update(['min_stock' => $request->min_stock]);

        return redirect()->back()
                        ->with('success', "Estoque mínimo de {$updated} produtos atualizado para {$request->min_stock}!");
    }
}
