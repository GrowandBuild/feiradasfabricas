<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductAttributeController extends Controller
{
    private function gone()
    {
        return response()->json(['message' => 'Product attributes removed'], 410);
    }

    public function index(...$args) { return $this->gone(); }
    public function attach(...$args) { return $this->gone(); }
    public function detach(...$args) { return $this->gone(); }
}
