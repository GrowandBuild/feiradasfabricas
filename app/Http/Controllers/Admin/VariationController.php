<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function index()
    {
        return view('admin.variations.index');
    }

    public function create()
    {
        return view('admin.variations.create');
    }

    public function store(Request $request)
    {
        // create variation definition (attributes like color, size)
        return back()->with('success','Variação criada');
    }

    public function edit($id)
    {
        return view('admin.variations.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return back()->with('success','Variação atualizada');
    }

    public function destroy($id)
    {
        return back()->with('success','Variação excluída');
    }

    public function setGenerator()
    {
        // basic product list for generator product selection
        $products = \App\Models\Product::orderBy('name')->limit(200)->get(['id','name','sku']);
        return view('admin.variations.set-generator', compact('products'));
    }
}
