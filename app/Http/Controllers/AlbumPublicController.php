<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumPublicController extends Controller
{
    public function index()
    {
        $query = Album::latest();
        // show unpublished albums to admins so they can manage/see drafts
        if (!auth()->guard('admin')->check()) {
            $query->where('is_published', true);
        }

        $albums = $query->paginate(12);
        return view('albums.index', compact('albums'));
    }

    public function show(string $slug)
    {
        $query = Album::where('slug', $slug)->with('images');
        // allow admins to preview unpublished albums
        if (!auth()->guard('admin')->check()) {
            $query->where('is_published', true);
        }

        $album = $query->firstOrFail();
        return view('albums.show', compact('album'));
    }

    /**
     * Store images uploaded from the public album page by an admin.
     */
    public function storeImage(Request $request, string $slug)
    {
        $album = Album::where('slug', $slug)->firstOrFail();

        $request->validate([
            'images.*' => 'required|image|max:8192',
        ]);

        if (!$request->hasFile('images')) {
            return back()->with('error', 'Nenhuma imagem enviada.');
        }

        $maxPos = (int) ($album->images()->max('position') ?? 0);
        $created = [];
        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('albums/images', 'public');
            $img = $album->images()->create([
                'path' => $path,
                'position' => $maxPos + $i + 1,
            ]);
            $created[] = [
                'id' => $img->id,
                'url' => Storage::url($path),
                'alt' => $img->alt ?? $album->title,
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'images' => $created], 201);
        }

        return back()->with('success', 'Imagens adicionadas ao álbum.');
    }

    /**
     * Quick create an album from public page (admin only).
     */
    public function storeAlbum(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:albums,slug',
            'description' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'cover' => 'nullable|image|max:4096',
        ]);

        $slug = $data['slug'] ?? Str::slug($data['title']);
        $original = $slug;
        $i = 2;
        while (Album::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i;
            $i++;
        }

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('albums/covers', 'public');
        }

        $album = Album::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_published' => (bool)($data['is_published'] ?? false),
            'cover_path' => $coverPath,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'cover_url' => $album->cover_url,
            ]], 201);
        }

        return redirect()->route('albums.show', $album->slug)->with('success', 'Álbum criado.');
    }

    /**
     * Delete an image from album (admin only) via public page.
     */
    public function destroyImage(Request $request, string $slug, int $imageId)
    {
        $album = Album::where('slug', $slug)->firstOrFail();
        $image = AlbumImage::findOrFail($imageId);
        abort_unless($image->album_id === $album->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true], 200);
        }

        return back()->with('success', 'Imagem removida.');
    }
}
