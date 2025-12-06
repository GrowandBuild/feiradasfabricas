<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductVariationController extends Controller
{
    public function index(Product $product)
    {
        $variations = $product->variations()->get();
        return view('admin.products.variations.index', compact('product','variations'));
    }

    public function store(Request $request, Product $product)
    {
        // create single variation for product
        return back()->with('success','Variação adicionada');
    }

    public function update(Request $request, Product $product, ProductVariation $variation)
    {
        // update
        return back()->with('success','Variação atualizada');
    }

    public function destroy(Product $product, ProductVariation $variation)
    {
        $variation->delete();
        return back()->with('success','Variação excluída');
    }
}
