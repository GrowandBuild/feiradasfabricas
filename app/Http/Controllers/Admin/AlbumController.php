<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::latest()->paginate(15);
        return view('admin.albums.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.albums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:albums,slug',
            'description' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'cover' => 'nullable|image|max:4096',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $slug = $data['slug'] ?? Str::slug($data['title']);
        $slug = $this->uniqueSlug($slug);

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

        if ($request->hasFile('images')) {
            $this->storeImages($album, $request->file('images'));
        }

        return redirect()->route('admin.albums.index')->with('success', 'Álbum criado com sucesso.');
    }

    public function edit(Album $album)
    {
        $album->load('images');
        return view('admin.albums.edit', compact('album'));
    }

    public function update(Request $request, Album $album)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:albums,slug,' . $album->id,
            'description' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'cover' => 'nullable|image|max:4096',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $slug = $data['slug'] ?? $album->slug;
        if ($slug !== $album->slug) {
            $slug = $this->uniqueSlug($slug, $album->id);
        }

        if ($request->hasFile('cover')) {
            if ($album->cover_path) Storage::disk('public')->delete($album->cover_path);
            $album->cover_path = $request->file('cover')->store('albums/covers', 'public');
        }

        $album->title = $data['title'];
        $album->slug = $slug;
        $album->description = $data['description'] ?? null;
        $album->is_published = (bool)($data['is_published'] ?? false);
        $album->save();

        if ($request->hasFile('images')) {
            $this->storeImages($album, $request->file('images'));
        }

        return redirect()->route('admin.albums.edit', $album)->with('success', 'Álbum atualizado.');
    }

    public function destroy(Album $album)
    {
        DB::transaction(function () use ($album) {
            foreach ($album->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
            if ($album->cover_path) Storage::disk('public')->delete($album->cover_path);
            $album->delete();
        });
        return redirect()->route('admin.albums.index')->with('success', 'Álbum excluído.');
    }

    public function destroyImage(Album $album, AlbumImage $image)
    {
        abort_unless($image->album_id === $album->id, 404);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        return back()->with('success', 'Imagem removida.');
    }

    private function storeImages(Album $album, array $files): void
    {
        $maxPos = (int) ($album->images()->max('position') ?? 0);
        foreach ($files as $i => $file) {
            $path = $file->store('albums/images', 'public');
            $album->images()->create([
                'path' => $path,
                'position' => $maxPos + $i + 1,
            ]);
        }
    }

    private function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($baseSlug);
        $original = $slug;
        $i = 2;
        while (Album::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
