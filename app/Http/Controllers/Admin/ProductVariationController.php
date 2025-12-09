<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * ProductVariationController removed — kept as graceful stub to return 410 for all endpoints.
 */
class ProductVariationController extends Controller
{
    private function gone()
    {
        return response()->json(['message' => 'Product variations subsystem removed'], 410);
    }

    public function index(...$args) { return $this->gone(); }
    public function store(...$args) { return $this->gone(); }
    public function update(...$args) { return $this->gone(); }
    public function destroy(...$args) { return $this->gone(); }
}
