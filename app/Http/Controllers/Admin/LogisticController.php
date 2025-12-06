<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogisticController extends Controller
{
    public function dimensions()
    {
        return view('admin.logistics.dimensions');
    }

    public function wmsSync()
    {
        return view('admin.logistics.wms-sync');
    }

    public function pushToWms(Request $request)
    {
        // enqueue job to push product/stock
        return back()->with('success','Push to WMS scheduled');
    }
}
