<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        $galleries = $query->paginate(20);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data = $validated;
        $data['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('galleries/covers', 'public');
        }

        $gallery = Gallery::create($data);

        return redirect()->route('admin.galleries.edit', $gallery)
            ->with('success', 'Galeria criada com sucesso! Agora adicione imagens.');
    }

    public function edit(Gallery $gallery)
    {
        $gallery->load('images');
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'remove_cover' => ['nullable', Rule::in(['1'])],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data = $validated;
        $data['is_published'] = $request->boolean('is_published', $gallery->is_published);

        if ($request->has('remove_cover') && $request->remove_cover === '1') {
            if ($gallery->cover_image) {
                Storage::disk('public')->delete($gallery->cover_image);
            }
            $data['cover_image'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($gallery->cover_image) {
                Storage::disk('public')->delete($gallery->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('galleries/covers', 'public');
        }

        $gallery->update($data);

        return redirect()->route('admin.galleries.edit', $gallery)
            ->with('success', 'Galeria atualizada com sucesso!');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->cover_image) {
            Storage::disk('public')->delete($gallery->cover_image);
        }

        // Remover imagens associadas (arquivos)
        foreach ($gallery->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Galeria removida com sucesso!');
    }

    public function togglePublish(Gallery $gallery)
    {
        $gallery->update(['is_published' => !$gallery->is_published]);
        return redirect()->back()->with('success', 'Status de publicação atualizado.');
    }

    public function uploadImages(Request $request, Gallery $gallery)
    {
        $request->validate([
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        $maxOrder = (int) ($gallery->images()->max('sort_order') ?? 0);

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('galleries/' . $gallery->id, 'public');
            $gallery->images()->create([
                'image_path' => $path,
                'sort_order' => ++$maxOrder,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Imagens enviadas com sucesso!');
    }

    public function destroyImage(Gallery $gallery, GalleryImage $image)
    {
        abort_unless($image->gallery_id === $gallery->id, 404);

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return redirect()->back()->with('success', 'Imagem removida com sucesso!');
    }

    public function reorderImages(Request $request, Gallery $gallery)
    {
        $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($request->orders as $imageId => $order) {
            $img = $gallery->images()->where('id', $imageId)->first();
            if ($img) {
                $img->update(['sort_order' => (int) $order]);
            }
        }

        return redirect()->back()->with('success', 'Ordem das imagens atualizada!');
    }

    public function uploadImageFromUrl(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'image_url' => ['required', 'url']
        ]);

        try {
            $response = Http::timeout(15)->get($data['image_url']);
            if (!$response->ok()) {
                return redirect()->back()->with('error', 'Não foi possível baixar a imagem do link informado.');
            }

            $contentType = $response->header('Content-Type', '');
            $allowed = ['image/jpeg','image/png','image/gif','image/webp','image/jpg'];
            if (!collect($allowed)->contains(fn($t) => str_contains($contentType, $t))) {
                return redirect()->back()->with('error', 'Tipo de arquivo não suportado.');
            }

            $body = $response->body();
            // Limite de 10MB
            if (strlen($body) > 10 * 1024 * 1024) {
                return redirect()->back()->with('error', 'A imagem por link excede 10MB.');
            }

            // Extensão pela content-type
            $ext = match (true) {
                str_contains($contentType, 'image/jpeg') => 'jpg',
                str_contains($contentType, 'image/png') => 'png',
                str_contains($contentType, 'image/gif') => 'gif',
                str_contains($contentType, 'image/webp') => 'webp',
                default => 'jpg',
            };

            $filename = 'galleries/'.$gallery->id.'/url_'.uniqid().'.'.$ext;
            Storage::disk('public')->put($filename, $body);

            $maxOrder = (int) ($gallery->images()->max('sort_order') ?? 0);
            $gallery->images()->create([
                'image_path' => $filename,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Imagem adicionada por link com sucesso!');
        } catch (\Throwable $e) {
            \Log::error('Upload por URL falhou', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Falha ao processar a imagem por link.');
        }
    }
}
