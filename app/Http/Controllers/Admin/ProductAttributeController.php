<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index($productId)
    {
        return view('admin.products.attributes', compact('productId'));
    }

    public function attach(Request $request, $productId)
    {
        // attach attribute value to product
        return back()->with('success','Atributo associado ao produto');
    }

    public function detach(Request $request, $productId)
    {
        // detach
        return back()->with('success','Atributo removido do produto');
    }
}
