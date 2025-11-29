<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoria criada com sucesso!');
    }

    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Remover imagem anterior
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        // Verificar se a categoria tem produtos
        if ($category->products()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Não é possível excluir uma categoria que possui produtos associados.');
        }

        // Remover imagem
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Return a lightweight list of categories for selects (JSON)
     */
    public function list()
    {
        $cats = Category::select('id', 'name')->orderBy('name')->get();
        return response()->json(['success' => true, 'categories' => $cats]);
    }

    /**
     * Quick inline update used by frontend when admin is authenticated.
     * Accepts partial payload: name, description, icon_class
     */
    public function quickUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'nullable|string|max:255',
            'show_avatar' => 'nullable|boolean',
            'show_cover' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',
            'show_description' => 'nullable|boolean',
            'show_button' => 'nullable|boolean',
            'button_position' => 'nullable|string|in:top,center,bottom',
        ]);

        try {
            $data = [];
            if ($request->filled('name')) {
                $data['name'] = $request->name;
                $data['slug'] = Str::slug($request->name);
            }
            if ($request->has('description')) {
                $data['description'] = $request->description;
            }
            if ($request->has('icon_class')) {
                // Only attempt to set icon_class if the column exists in DB
                if (Schema::hasColumn('categories', 'icon_class')) {
                    $data['icon_class'] = $request->icon_class;
                }
            }

            // Visibility flags: set only if the columns exist. Use exists() so false/0 values are honored.
            if (Schema::hasColumn('categories', 'show_avatar') && $request->exists('show_avatar')) {
                $data['show_avatar'] = (bool) $request->input('show_avatar');
            }
            if (Schema::hasColumn('categories', 'show_cover') && $request->exists('show_cover')) {
                $data['show_cover'] = (bool) $request->input('show_cover');
            }
            if (Schema::hasColumn('categories', 'show_title') && $request->exists('show_title')) {
                $data['show_title'] = (bool) $request->input('show_title');
            }
            if (Schema::hasColumn('categories', 'show_description') && $request->exists('show_description')) {
                $data['show_description'] = (bool) $request->input('show_description');
            }
            if (Schema::hasColumn('categories', 'show_button') && $request->exists('show_button')) {
                $data['show_button'] = (bool) $request->input('show_button');
            }
            if (Schema::hasColumn('categories', 'button_position') && $request->exists('button_position')) {
                $pos = $request->input('button_position');
                if (in_array($pos, ['top','center','bottom'])) {
                    $data['button_position'] = $pos;
                }
            }

            if (!empty($data)) {
                $category->update($data);
                // refresh to ensure we return the latest values
                $category->refresh();
            }

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'icon_class' => $category->icon_class ?? null,
                    'slug' => $category->slug,
                    'show_avatar' => $category->show_avatar ?? null,
                    'show_cover' => $category->show_cover ?? null,
                    'show_title' => $category->show_title ?? null,
                    'show_description' => $category->show_description ?? null,
                    'show_button' => $category->show_button ?? null,
                    'button_position' => $category->button_position ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro quickUpdate categoria: ' . $e->getMessage(), ['id' => $category->id, 'payload' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar categoria.'], 500);
        }
    }

    /**
     * Update category image via quick frontend request (admin only)
     */
    public function updateImage(Request $request, Category $category)
    {
        // Accept either a file upload or an external URL
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
            'image_url' => 'nullable|url|max:2000',
        ]);

        try {
            // If a file was uploaded, store it in public disk
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                \Log::info('Category updateImage called with file upload', ['category_id' => $category->id, 'original_name' => $request->file('image')->getClientOriginalName()]);
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                $path = $request->file('image')->store('categories', 'public');
                // ensure we persist the storage path (not the URL)
                $category->update(['image' => $path]);
                $imageUrl = Storage::disk('public')->url($path);
                \Log::info('Category image stored', ['category_id' => $category->id, 'path' => $path, 'url' => $imageUrl]);
            } elseif ($request->filled('image_url')) {
                // Accept external URL and store it directly in the image field
                $imageUrl = $request->input('image_url');
                $category->update(['image' => $imageUrl]);
                \Log::info('Category updateImage called with external URL', ['category_id' => $category->id, 'image_url' => $imageUrl]);
            } else {
                return response()->json(['success' => false, 'message' => 'Nenhuma imagem enviada.'], 422);
            }

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'image_url' => $imageUrl,
                    'stored_path' => $path ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro updateImage categoria: ' . $e->getMessage(), ['id' => $category->id]);
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar imagem da categoria.'], 500);
        }
    }

    /**
     * Remove category image (quick inline)
     */
    public function removeImage(Request $request, Category $category)
    {
        try {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->update(['image' => null]);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'image_url' => asset('images/no-image.svg'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro removeImage categoria: ' . $e->getMessage(), ['id' => $category->id]);
            return response()->json(['success' => false, 'message' => 'Erro ao remover imagem da categoria.'], 500);
        }
    }

    /**
     * Update cover (background) image URL for the category card
     */
    public function updateCover(Request $request, Category $category)
    {
        $request->validate([
            'cover_url' => 'required|url|max:2000',
        ]);

        try {
            // Only set cover if the column exists
            if (!Schema::hasColumn('categories', 'cover')) {
                return response()->json(['success' => false, 'message' => 'Coluna "cover" não existe. Rode a migration para adicionar a coluna cover na tabela categories.'], 400);
            }

            $coverUrl = $request->input('cover_url');
            $category->update(['cover' => $coverUrl]);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'cover_url' => $coverUrl,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro updateCover categoria: ' . $e->getMessage(), ['id' => $category->id]);
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar cover da categoria.'], 500);
        }
    }

    /**
     * Remove cover (background) image
     */
    public function removeCover(Request $request, Category $category)
    {
        try {
            if (!Schema::hasColumn('categories', 'cover')) {
                return response()->json(['success' => false, 'message' => 'Coluna "cover" não existe. Rode a migration para adicionar a coluna cover na tabela categories.'], 400);
            }

            $category->update(['cover' => null]);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'cover_url' => null,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro removeCover categoria: ' . $e->getMessage(), ['id' => $category->id]);
            return response()->json(['success' => false, 'message' => 'Erro ao remover cover da categoria.'], 500);
        }
    }
}
