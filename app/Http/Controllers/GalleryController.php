<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::published()
            ->withCount(['images' => function ($q) { $q->active(); }])
            ->orderByDesc('id')
            ->paginate(12);

        return view('gallery.index', compact('galleries'));
    }

    public function show(string $slug)
    {
        $gallery = Gallery::published()
            ->where('slug', $slug)
            ->with(['images' => function ($q) { $q->active()->orderBy('sort_order'); }])
            ->firstOrFail();

        return view('gallery.show', compact('gallery'));
    }
}
