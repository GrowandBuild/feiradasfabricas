<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function dashboard()
    {
        return view('admin.sync.dashboard');
    }

    public function settings()
    {
        return view('admin.sync.settings');
    }

    public function runNow(Request $request)
    {
        // trigger sync job(s)
        return back()->with('success','Sync started');
    }
}
