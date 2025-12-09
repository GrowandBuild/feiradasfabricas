<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\VariationGenerator;
use Illuminate\Http\Request;

class ProductVariationGeneratorController extends Controller
{
    protected VariationGenerator $generator;

    public function __construct(VariationGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function create()
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.products.variations-generator', compact('products'));
    }

    /**
     * Generate variations for a product.
     * Expects JSON body: { "combinations": [ {"ram":"8GB","storage":"128GB","color":"Preto","price":99.99,"stock_quantity":5}, ... ] }
     */
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'combinations' => 'required|array|min:1',
            'combinations.*' => 'array',
        ]);

        $results = $this->generator->generate($product, $data['combinations']);

        return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'results' => array_map(function($r){
                return [
                    'action' => $r['action'],
                    'id' => $r['variation']->id,
                    'sku' => $r['variation']->sku,
                ];
            }, $results),
        ]);
    }
}
