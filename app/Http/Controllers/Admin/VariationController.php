<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class VariationController extends Controller
{
    private function gone()
    {
        return response()->json(['message' => 'Variations subsystem removed'], 410);
    }

    public function index(...$args) { return $this->gone(); }
    public function create(...$args) { return $this->gone(); }
    public function store(...$args) { return $this->gone(); }
    public function edit(...$args) { return $this->gone(); }
    public function update(...$args) { return $this->gone(); }
    public function destroy(...$args) { return $this->gone(); }
    public function setGenerator(...$args) { return $this->gone(); }
}
