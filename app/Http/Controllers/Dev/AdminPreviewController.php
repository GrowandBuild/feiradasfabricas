<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Department;

class AdminPreviewController extends Controller
{
    /**
     * Dev-only product edit preview without auth.
     */
    public function productEdit($id)
    {
        $product = Product::find($id);
        if (!$product) {
            abort(404, 'Produto não encontrado (dev preview)');
        }

        $categories = Category::all();
        $departments = Department::all();
        $productCategories = $product->categories->pluck('id')->toArray();

        return view('admin.products.edit', compact('product', 'categories', 'productCategories', 'departments'));
    }
}
