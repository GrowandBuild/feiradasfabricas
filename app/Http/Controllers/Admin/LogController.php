<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // basic logs listing view
        return view('admin.logs.index');
    }

    public function show($type)
    {
        return view('admin.logs.show', compact('type'));
    }

    public function export(Request $request)
    {
        // export filtered logs
        return response()->download(storage_path('logs/laravel.log'));
    }
}
