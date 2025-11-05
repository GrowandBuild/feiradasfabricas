<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PriceRuleController extends Controller
{
    public function applyPriceRules(Request $request)
    {
        $request->validate([
            'rule_type' => 'required|in:percentage,fixed,markup',
            'value' => 'required|numeric',
            'brand' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
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

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->get();
        $updated = 0;

        foreach ($products as $product) {
            $oldPrice = $product->price;
            $oldB2BPrice = $product->b2b_price;
            $oldCostPrice = $product->cost_price;

            $newPrice = match($request->rule_type) {
                'percentage' => $oldPrice * (1 + $request->value / 100),
                'fixed' => $oldPrice + $request->value,
                'markup' => $oldCostPrice * (1 + $request->value / 100),
            };

            $newB2BPrice = $newPrice * 0.9; // 10% desconto B2B padrão
            $newCostPrice = $oldCostPrice; // Manter custo

            $product->update([
                'price' => round($newPrice, 2),
                'b2b_price' => round($newB2BPrice, 2),
                'cost_price' => round($newCostPrice, 2),
            ]);

            $updated++;
        }

        return redirect()->back()
                        ->with('success', "Preços de {$updated} produtos atualizados com a regra!");
    }

    public function bulkPriceUpdate(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.b2b_price' => 'nullable|numeric|min:0',
            'products.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        $updated = 0;

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            $product->update([
                'price' => (float) $productData['price'],
                'b2b_price' => (float) ($productData['b2b_price'] ?? $productData['price'] * 0.9),
                'cost_price' => (float) ($productData['cost_price'] ?? $product->cost_price),
            ]);

            $updated++;
        }

        return redirect()->back()
                        ->with('success', "Preços de {$updated} produtos atualizados!");
    }

    public function priceAnalysis()
    {
        $brandStats = Product::selectRaw('brand, 
            COUNT(*) as total_products,
            AVG(price) as avg_price,
            MIN(price) as min_price,
            MAX(price) as max_price,
            AVG(b2b_price) as avg_b2b_price')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->orderBy('avg_price', 'desc')
            ->get();

        $lowMarginProducts = Product::selectRaw('*, 
            ((price - cost_price) / price * 100) as margin_percentage')
            ->having('margin_percentage', '<', 20)
            ->orderBy('margin_percentage', 'asc')
            ->limit(20)
            ->get();

        return view('admin.products.price-analysis', compact('brandStats', 'lowMarginProducts'));
    }
}
