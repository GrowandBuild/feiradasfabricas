<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class InstructionsController extends Controller
{
    public function index()
    {
        return view('admin.instructions.index');
    }
}
