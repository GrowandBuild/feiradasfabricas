<?php

namespace App\Http\Controllers;

use App\Models\Album;

class AlbumPublicController extends Controller
{
    public function index()
    {
        $albums = Album::where('is_published', true)->latest()->paginate(12);
        return view('albums.index', compact('albums'));
    }

    public function show(string $slug)
    {
        $album = Album::where('slug', $slug)->where('is_published', true)->with('images')->firstOrFail();
        return view('albums.show', compact('album'));
    }
}
