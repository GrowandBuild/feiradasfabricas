<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCloneController extends Controller
{
    public function clone(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Cópia)';
        $newProduct->slug = Str::slug($product->name . ' copia ' . time());
        $newProduct->sku = $product->sku . '-COPY-' . rand(100, 999);
        $newProduct->stock_quantity = 0;
        $newProduct->is_featured = false;
        $newProduct->save();

        // Copiar categorias
        $newProduct->categories()->attach($product->categories->pluck('id'));

        return redirect()->route('admin.products.edit', $newProduct)
                        ->with('success', 'Produto clonado com sucesso!');
    }

    public function bulkClone(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'variations' => 'required|array',
            'variations.*' => 'string'
        ]);

        $cloned = 0;
        
        foreach ($request->product_ids as $productId) {
            $product = Product::find($productId);
            
            foreach ($request->variations as $variation) {
                $newProduct = $product->replicate();
                $newProduct->name = $product->name . ' ' . $variation;
                $newProduct->slug = Str::slug($product->name . ' ' . $variation . ' ' . time());
                $newProduct->sku = $product->sku . '-' . strtoupper(str_replace(' ', '', $variation));
                $newProduct->stock_quantity = 0;
                $newProduct->is_featured = false;
                $newProduct->save();

                // Copiar categorias
                $newProduct->categories()->attach($product->categories->pluck('id'));
                $cloned++;
            }
        }

        return redirect()->back()
                        ->with('success', "{$cloned} produtos clonados com sucesso!");
    }
}
