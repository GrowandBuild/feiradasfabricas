<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        return view('admin.seo.index');
    }

    public function edit(Request $request)
    {
        return view('admin.seo.edit');
    }

    public function update(Request $request)
    {
        // validate and persist metadata rules
        return back()->with('success','SEO settings updated');
    }
}
