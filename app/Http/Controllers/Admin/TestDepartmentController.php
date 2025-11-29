<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestDepartmentController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'message' => 'TestDepartmentController está funcionando!']);
    }
}
