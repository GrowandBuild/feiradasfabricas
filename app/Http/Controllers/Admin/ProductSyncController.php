<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductSyncController extends Controller
{
    public function show($productId)
    {
        return view('admin.products.sync', compact('productId'));
    }

    public function push(Request $request, $productId)
    {
        // push product to configured marketplaces (async)
        return back()->with('success','Sincronização iniciada');
    }

    public function status(Request $request, $productId)
    {
        // return last sync status
        return response()->json(['status' => 'queued']);
    }
}
