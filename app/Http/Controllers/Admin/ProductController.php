<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::all();

        $suppliers = Product::query()
            ->whereNotNull('supplier')
            ->distinct()
            ->orderBy('supplier')
            ->pluck('supplier');

        return view('admin.products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku' => 'nullable|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Handle images (simple): accept uploaded files and store paths
        $uploadedPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                if ($path) $uploadedPaths[] = $path;
            }
        }

        if (!empty($uploadedPaths)) {
            $data['images'] = $uploadedPaths;
        }

        $product = DB::transaction(function() use ($data, $request) {
            $prod = Product::create($data);
            if ($request->has('categories')) {
                $prod->categories()->sync($request->categories);
            }
            InventoryLog::create([
                'product_id' => $prod->id,
                'admin_id' => auth('admin')->id(),
                'type' => 'initial',
                'quantity_before' => 0,
                'quantity_change' => $prod->stock_quantity ?? 0,
                'quantity_after' => $prod->stock_quantity ?? 0,
                'reference' => 'Criação do produto',
            ]);
            return $prod;
        });

        return redirect()->route('admin.products.edit', $product)
                        ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product)
    {
        $product->load('categories', 'inventoryLogs.admin', 'orderItems.order.customer');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $productCategories = $product->categories->pluck('id')->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'productCategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Simple images handling
        $imagePaths = [];
        if ($request->has('existing_images') && is_array($request->existing_images)) {
            $imagePaths = array_filter($request->existing_images);
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                if ($path) $imagePaths[] = $path;
            }
        }
        $data['images'] = $imagePaths;

        $oldStock = $product->stock_quantity;
        $newStock = $request->stock_quantity ?? $oldStock;
        if ($oldStock != $newStock) {
            InventoryLog::create([
                'product_id' => $product->id,
                'admin_id' => auth('admin')->id(),
                'type' => 'adjustment',
                'quantity_before' => $oldStock,
                'quantity_change' => $newStock - $oldStock,
                'quantity_after' => $newStock,
                'reference' => 'Edição do produto',
            ]);
        }

        $product->update($data);
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.products.edit', $product)
                        ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->count() > 0) {
            return redirect()->back()->with('error', 'Produto possui pedidos e não pode ser excluído');
        }

        if ($product->images) {
            foreach ($product->images as $image) {
                // keep files — do not delete storage files automatically
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produto excluído com sucesso!');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $oldStock = $product->stock_quantity;
        $quantity = (int) $request->quantity;
        $newStock = match ($request->type) {
            'in' => $oldStock + $quantity,
            'out' => max(0, $oldStock - $quantity),
            'adjustment' => $quantity,
        };

        InventoryLog::create([
            'product_id' => $product->id,
            'admin_id' => auth('admin')->id(),
            'type' => $request->type,
            'quantity_before' => $oldStock,
            'quantity_change' => $newStock - $oldStock,
            'quantity_after' => $newStock,
            'notes' => $request->notes,
            'reference' => 'Ajuste manual',
        ]);

        $product->update(['stock_quantity' => $newStock, 'in_stock' => $newStock > 0]);

        return response()->json(['success' => true, 'new_stock' => $newStock]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_unavailable,mark_available,delete',
            'product_ids' => 'required|string',
        ]);

        $productIds = json_decode($request->product_ids, true);
        if (!is_array($productIds) || empty($productIds)) {
            return redirect()->back()->with('error', 'Nenhum produto válido fornecido');
        }

        $existingIds = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $count = 0; $skipped = 0;

        foreach ($existingIds as $id) {
            $p = Product::find($id);
            if (!$p) continue;
            if ($request->action === 'mark_unavailable') { $p->update(['is_unavailable' => true]); $count++; }
            elseif ($request->action === 'mark_available') { $p->update(['is_unavailable' => false]); $count++; }
            elseif ($request->action === 'delete') {
                if ($p->orderItems()->count() > 0) { $skipped++; continue; }
                $p->delete(); $count++;
            }
        }

        $message = match($request->action) {
            'mark_unavailable' => "{$count} produto(s) marcado(s) como indisponível(is)!",
            'mark_available' => "{$count} produto(s) marcado(s) como disponível(is)!",
            'delete' => $skipped > 0 ? "{$count} produto(s) excluído(s). {$skipped} pulados." : "{$count} produto(s) excluído(s) com sucesso!",
        };

        return redirect()->back()->with('success', $message);
    }

    public function updateCostPrice(Request $request, Product $product)
    {
        $request->validate(['cost_price' => 'required|numeric|min:0']);
        $costPrice = (float) $request->cost_price;
        $profitMarginB2B = $product->profit_margin_b2b ?? 10.00;
        $profitMarginB2C = $product->profit_margin_b2c ?? 20.00;

        $b2bPrice = round($costPrice * (1 + $profitMarginB2B / 100), 2);
        $b2cPrice = round($costPrice * (1 + $profitMarginB2C / 100), 2);

        $product->update(['cost_price' => $costPrice, 'b2b_price' => $b2bPrice, 'price' => $b2cPrice]);

        return response()->json(['success' => true]);
    }

    public function updateProfitMargin(Request $request, Product $product)
    {
        $request->validate(['margin' => 'required|numeric|min:0|max:1000']);
        $type = $request->type; $margin = (float) $request->margin;
        if ($type === 'b2b') $product->update(['profit_margin_b2b' => $margin]);
        else $product->update(['profit_margin_b2c' => $margin]);
        return response()->json(['success' => true]);
    }

    public function applyGlobalMargins(Request $request)
    {
        $request->validate(['profit_margin_b2b' => 'required|numeric', 'profit_margin_b2c' => 'required|numeric']);
        $b2b = (float) $request->profit_margin_b2b; $b2c = (float) $request->profit_margin_b2c;
        $products = Product::whereNotNull('cost_price')->get();
        foreach ($products as $p) {
            $p->update(['profit_margin_b2b' => $b2b, 'profit_margin_b2c' => $b2c]);
        }
        return response()->json(['success' => true]);
    }

    public function getImages(Product $product)
    {
        $images = $product->all_images ?? [];
        $featuredImage = $product->first_image ?? null;
        $formatted = [];
        foreach ($images as $image) {
            if (empty($image)) continue;
            if (strpos($image, 'http') === 0) $formatted[] = $image;
            elseif (strpos($image, '/') === 0) $formatted[] = url(ltrim($image, '/'));
            else $formatted[] = asset('storage/' . $image);
        }
        return response()->json(['success' => true, 'images' => $formatted, 'featured_image' => $featuredImage ? (strpos($featuredImage, 'http') === 0 ? $featuredImage : asset('storage/' . $featuredImage)) : null]);
    }

    public function updateImages(Request $request, Product $product)
    {
        $request->validate(['existing_additional_images' => 'nullable|array']);
        $imagePaths = $product->images ?? [];
        if ($request->has('existing_additional_images') && is_array($request->existing_additional_images)) {
            $imagePaths = array_values(array_filter($request->existing_additional_images));
        }
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $img) {
                $p = $img->store('products', 'public'); if ($p) $imagePaths[] = $p;
            }
        }
        $product->update(['images' => $imagePaths]);
        return response()->json(['success' => true, 'images' => $product->all_images]);
    }

    public function removeImage(Request $request, Product $product)
    {
        $request->validate(['image_path' => 'required|string']);
        $imagePath = $this->extractImagePath((string)$request->image_path);
        if (!$imagePath) return response()->json(['message' => 'Imagem inválida'], 422);
        $current = $product->images ?? [];
        $updated = array_values(array_filter($current, function($i) use ($imagePath) { return $i !== $imagePath; }));
        $product->update(['images' => $updated]);
        return response()->json(['success' => true, 'images' => $product->all_images]);
    }

    public function updateDescription(Request $request, Product $product)
    {
        $request->validate(['description' => 'nullable|string|max:5000']);
        $product->update(['description' => $request->description]);
        return response()->json(['success' => true, 'description' => $product->description]);
    }

    public function updateName(Request $request, Product $product)
    {
        $request->validate(['name' => 'required|string|min:2|max:255']);
        $newName = trim($request->name);
        $product->update(['name' => $newName, 'slug' => Str::slug($newName)]);
        return response()->json(['success' => true]);
    }

    private function extractImagePath($image)
    {
        if (empty($image)) return null;
        if (strpos($image, 'http') !== 0 && strpos($image, '/') !== 0) return $image;
        if (strpos($image, 'http') === 0) {
            $parsed = parse_url($image);
            $path = $parsed['path'] ?? '';
            if (strpos($path, '/storage/') === 0) $path = substr($path, 9);
            elseif (strpos($path, 'storage/') === 0) $path = substr($path, 8);
            return !empty($path) ? ltrim($path, '/') : null;
        }
        if (strpos($image, '/') === 0) return substr($image, 1);
        return $image;
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\ProductVariation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Models\Attribute as AttributeModel;
use App\Models\AttributeValue as AttributeValueModel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Carregar apenas o essencial para a listagem: contar variações e evitar N+1
        $query = Product::query()->withCount('variations');

        // Se for requisição AJAX (Smart Search), buscar TODOS os produtos (incluindo inativos)
        $isSmartSearch = $request->ajax() || $request->wantsJson();
        
        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // brand filter removed

        if ($request->filled('category') && !$isSmartSearch) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->filled('status') && !$isSmartSearch) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('stock_status') && !$isSmartSearch) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        if ($request->filled('supplier') && !$isSmartSearch) {
            $query->where('supplier', $request->supplier);
        }

        if ($request->filled('availability') && !$isSmartSearch) {
            if ($request->availability === 'unavailable') {
                $query->where('is_unavailable', true);
            } elseif ($request->availability === 'available') {
                $query->where('is_unavailable', false);
            }
        }

        // Se for requisição AJAX (Smart Search), retornar JSON rapidamente e evitar carregar dados de view
        if ($isSmartSearch) {
            // Relevância: privilegiar correspondência forte do termo, mesmo se o produto estiver desabilitado
            if ($request->filled('search')) {
                $s = trim($request->search);
                $startsWith = $s . '%';
                $contains = '%' . $s . '%';

                // Score de match: exato > começa com > contém
                $query->orderByRaw(
                    "(CASE 
                        WHEN LOWER(name) = LOWER(?) THEN 6
                        WHEN LOWER(sku) = LOWER(?) THEN 6
                        WHEN LOWER(name) LIKE LOWER(?) THEN 5
                        WHEN LOWER(sku) LIKE LOWER(?) THEN 5
                        WHEN LOWER(name) LIKE LOWER(?) THEN 4
                        WHEN LOWER(sku) LIKE LOWER(?) THEN 4
                        ELSE 0 
                    END) DESC",
                    [$s, $s, $startsWith, $startsWith, $contains, $contains]
                );
            }

            $results = $query
                // Tiebreakers: status depois da relevância textual
                ->orderByDesc('is_active')
                ->orderBy('is_unavailable') // false (0) primeiro
                ->orderByDesc('in_stock')
                ->orderBy('name')
                ->limit(25)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'first_image' => $product->first_image,
                        'sku' => $product->sku,
                        'is_active' => $product->is_active,
                        'is_unavailable' => $product->is_unavailable,
                        'in_stock' => $product->in_stock,
                    ];
                });

            return response()->json([
                'products' => $results,
            ]);
        }

    $products = $query->paginate(20);
        $categories = Category::all();
        // brand lists removed (no DB queries for brands)
        $suppliers = Product::query()
            ->whereNotNull('supplier')
            ->distinct()
            ->orderBy('supplier')
            ->pluck('supplier');
        // Load departments for initial selection prompt (if needed in the view)
        $departments = Department::orderBy('name')
            ->get(['id','name','slug']);
        
        // Buscar todas as cores cadastradas no banco com seus hexadecimais
        $colorsFromDB = ProductVariation::whereNotNull('color')
            ->whereNotNull('color_hex')
            ->select('color', 'color_hex')
            ->distinct()
            ->get()
            ->pluck('color_hex', 'color')
            ->map(function($hex) {
                return strtolower($hex);
            })
            ->toArray();
        
        // Não criar valores padrão aqui - deixar a view usar os defaults apenas para exibição

        return view('admin.products.index', compact('products', 'categories', 'colorsFromDB', 'suppliers', 'departments'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $departments = Department::orderBy('name')->get(['id','name']);

        // If image_ids are provided (from album selection), load them to prefill the form
        $preselectedImages = [];
        if ($request->filled('image_ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->query('image_ids'))));
            if (!empty($ids)) {
                $images = \App\Models\AlbumImage::whereIn('id', $ids)->get();
                $preselectedImages = $images->map(function($img){
                    return [
                        'id' => $img->id,
                        'url' => $img->url,
                        'path' => $img->path,
                        'alt' => $img->alt,
                    ];
                })->toArray();
            }
        }

        return view('admin.products.create', compact('categories', 'departments', 'preselectedImages'));
    }

    // brandsList removed

    public function store(Request $request)
    {
        // If categories were sent as a JSON string (FormData submit with files), decode them
        $catsInput = $request->input('categories');
        if (is_string($catsInput) && $catsInput !== '') {
            $decodedCats = json_decode($catsInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedCats)) {
                $request->merge(['categories' => $decodedCats]);
            } else {
                // tolerate comma-separated list like "1,2,3"
                if (preg_match('/^\d+(,\d+)*$/', trim($catsInput))) {
                    $arr = array_map('intval', explode(',', $catsInput));
                    $request->merge(['categories' => $arr]);
                }
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            // SKU can be omitted from quick-create; generate server-side if missing
            'sku' => 'nullable|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sell_b2b' => 'nullable|boolean',
            'sell_b2c' => 'nullable|boolean',
            'b2b_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            // 'variations' may arrive as JSON string when sent via FormData; decode below
            'variations' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'product_type' => 'nullable|in:physical,service',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
            'existing_image_ids' => 'nullable|array',
            'existing_image_ids.*' => 'integer|exists:album_images,id',
            'specifications' => 'nullable|array',
            'brand_id' => 'nullable|exists:brands,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);
        $data = $request->all();
        // Ensure product_type present and default to 'physical'
        $data['product_type'] = $request->input('product_type', 'physical');
        $data['sell_b2b'] = $request->boolean('sell_b2b', true);
        $data['sell_b2c'] = $request->boolean('sell_b2c', true);
        $data['slug'] = Str::slug($request->name);
        // If product_type is 'service', do not manage stock by default
        if (($data['product_type'] ?? 'physical') === 'service') {
            $data['manage_stock'] = false;
        } else {
            // Manage stock only when explicitly requested or when stock_quantity provided
            $data['manage_stock'] = $request->boolean('manage_stock', $request->filled('stock_quantity'));
        }

        // If SKU was not provided by the frontend (quick-create minimal), generate a unique one server-side
        if (empty($data['sku'])) {
            $base = strtoupper(preg_replace('/[^A-Z0-9]/', '', Str::slug($request->name ?: 'PRD')));
            if (!$base) $base = 'PRD';
            $candidate = $base;
            $attempt = 0;
            while (\App\Models\Product::where('sku', $candidate)->exists()) {
                $attempt++;
                $candidate = $base . '-' . rand(100, 999) . ($attempt > 10 ? '-' . time() : '');
                if ($attempt > 50) break;
            }
            $data['sku'] = $candidate;
        }

        // Collect any existing album images passed as ids and merge with uploaded files
        $existingPaths = [];
        if ($request->filled('existing_image_ids')) {
            $ids = is_array($request->input('existing_image_ids')) ? $request->input('existing_image_ids') : explode(',', $request->input('existing_image_ids'));
            $ids = array_map('intval', array_filter($ids));
            if (!empty($ids)) {
                $existingPaths = \App\Models\AlbumImage::whereIn('id', $ids)->pluck('path')->toArray();
            }
        }

        // Upload de imagens (salva no storage imediatamente) e mesclar com imagens existentes
        $uploadedPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $uploadedPaths[] = $path;
            }
        }

        // Merge existing album image paths with newly uploaded images (existing first)
        $allImagePaths = array_values(array_filter(array_merge($existingPaths, $uploadedPaths)));
        if (!empty($allImagePaths)) {
            $data['images'] = $allImagePaths;
        }

        $variationsInput = $request->input('variations', []);
        // Accept variations as JSON string when submitted via FormData
        if (is_string($variationsInput) && $variationsInput !== '') {
            $decoded = json_decode($variationsInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $variationsInput = $decoded;
            } else {
                // if decoding fails, fallback to empty array to avoid validation errors
                $variationsInput = [];
            }
        }

        // Criar produto e variações dentro de uma transação
        $product = DB::transaction(function () use ($data, $request, $variationsInput) {
            // Criar o produto básico
            $prod = Product::create($data);

            // Relacionar categorias
            if ($request->has('categories')) {
                $prod->categories()->attach($request->categories);
            }

            // Se vierem variações, persistir cada uma
            $totalStock = 0;
            if (is_array($variationsInput) && !empty($variationsInput)) {
                foreach ($variationsInput as $v) {
                    $sku = isset($v['sku']) && trim($v['sku']) !== '' ? trim($v['sku']) : null;

                    // Gerar SKU se não informado
                    if (empty($sku)) {
                        $sku = $prod->sku . '-' . strtoupper(substr(sha1(uniqid((string) rand(), true)), 0, 6));
                    }

                    // Garantir unicidade simples para SKU
                    while ($sku && ProductVariation::where('sku', $sku)->exists()) {
                        $sku .= '-' . rand(100, 999);
                    }

                    $stockQty = isset($v['stock_quantity']) ? (int)$v['stock_quantity'] : 0;
                    $price = isset($v['price']) ? round((float)$v['price'], 2) : $prod->price;
                    $b2bPrice = isset($v['b2b_price']) ? round((float)$v['b2b_price'], 2) : $prod->b2b_price;
                    $costPrice = isset($v['cost_price']) ? round((float)$v['cost_price'], 2) : $prod->cost_price;

                    ProductVariation::create([
                        'product_id' => $prod->id,
                        'name' => $v['name'] ?? null,
                        'sku' => $sku,
                        'price' => $price,
                        'b2b_price' => $b2bPrice,
                        'cost_price' => $costPrice,
                        'stock_quantity' => $stockQty,
                        'in_stock' => $stockQty > 0,
                        'is_active' => true,
                        'sort_order' => 0,
                        // Accept explicit variation attribute fields if provided by the frontend
                        'ram' => isset($v['ram']) ? ($v['ram'] === '' ? null : $v['ram']) : null,
                        'storage' => isset($v['storage']) ? ($v['storage'] === '' ? null : $v['storage']) : null,
                        'color' => isset($v['color']) ? ($v['color'] === '' ? null : $v['color']) : null,
                        'color_hex' => isset($v['color_hex']) ? ($v['color_hex'] === '' ? null : $v['color_hex']) : null,
                    ]);

                    $totalStock += $stockQty;
                }

                // Atualizar estoque do produto pai com a soma das variações
                $prod->stock_quantity = $totalStock;
                $prod->in_stock = $totalStock > 0;
                $prod->save();
            } else {
                // Sem variações: usar estoque enviado no request — se não enviado, manter 0 e in_stock false
                $qty = $request->has('stock_quantity') ? (int)$request->stock_quantity : 0;
                $prod->stock_quantity = $qty;
                $prod->in_stock = $qty > 0;
                $prod->save();
                $totalStock = (int)$request->stock_quantity;
            }

            // Log de estoque inicial (somatório)
            InventoryLog::create([
                'product_id' => $prod->id,
                'admin_id' => auth('admin')->id(),
                'type' => 'in',
                'quantity_before' => 0,
                'quantity_change' => $totalStock,
                'quantity_after' => $totalStock,
                'notes' => 'Estoque inicial',
                'reference' => 'Criação do produto',
            ]);

            return $prod;
        });

        // If the client expects JSON (AJAX/fetch), respond with JSON payload
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produto criado com sucesso!',
                'product' => $product->fresh()->toArray(),
            ]);
        }

        return redirect()->route('admin.products.index')
                        ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product)
    {
        $product->load('categories', 'inventoryLogs.admin', 'orderItems.order.customer');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $departments = Department::orderBy('name')->get(['id','name']);
        $productCategories = $product->categories->pluck('id')->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'productCategories', 'departments'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sell_b2b' => 'nullable|boolean',
            'sell_b2c' => 'nullable|boolean',
            'b2b_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'categories' => 'required|array|min:1',
            'department_id' => 'nullable|exists:departments,id',
            'product_type' => 'nullable|in:physical,service',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
            'specifications' => 'nullable|array',
            'brand_id' => 'nullable|exists:brands,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['product_type'] = $request->input('product_type', $product->product_type ?? 'physical');
        $data['sell_b2b'] = $request->boolean('sell_b2b', true);
        $data['sell_b2c'] = $request->boolean('sell_b2c', true);
        $data['slug'] = Str::slug($request->name);
        // If product_type is service, do not manage stock
        if (($data['product_type'] ?? 'physical') === 'service') {
            $data['manage_stock'] = false;
        } else {
            $data['manage_stock'] = $request->boolean('manage_stock', $product->manage_stock ?? $request->filled('stock_quantity'));
        }
        // If stock_quantity was provided, determine in_stock; otherwise keep current product value
        $data['in_stock'] = $request->has('stock_quantity') ? ($request->stock_quantity > 0) : ($product->in_stock ?? true);

        // Gerenciar imagens - seguindo a mesma lógica simples dos selos de categorias
        $imagePaths = [];
        
        // 1. Primeiro, manter as imagens existentes que não foram removidas
        if ($request->has('existing_images') && is_array($request->existing_images)) {
            $imagePaths = array_filter($request->existing_images);
        }
        
        // 2. Adicionar novas imagens se houver upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('products', 'public');
                    $imagePaths[] = $path;
                }
            }
        }
        
        // 3. Se todas as imagens foram removidas explicitamente
        if ($request->has('all_images_removed') && $request->all_images_removed == '1') {
            $imagePaths = [];
        }
        
        $data['images'] = $imagePaths;

        // Manter apenas imagens válidas nas variações por cor
        if (is_array($product->variation_images) && !empty($product->variation_images)) {
            $validImages = array_flip($imagePaths);
            $filteredVariationImages = [];

            foreach ($product->variation_images as $color => $images) {
                if (!is_array($images)) {
                    continue;
                }

                $filtered = array_values(array_filter($images, function ($image) use ($validImages) {
                    return isset($validImages[$image]);
                }));

                if (!empty($filtered)) {
                    $filteredVariationImages[$color] = $filtered;
                }
            }

            $data['variation_images'] = $filteredVariationImages;
        }

        // Log de alteração de estoque se necessário
        $oldStock = $product->stock_quantity;
        $newStock = $request->stock_quantity;
        
        if ($oldStock != $newStock) {
            InventoryLog::create([
                'product_id' => $product->id,
                'admin_id' => auth('admin')->id(),
                'type' => 'adjustment',
                'quantity_before' => $oldStock,
                'quantity_change' => $newStock - $oldStock,
                'quantity_after' => $newStock,
                'notes' => 'Alteração via edição do produto',
                'reference' => 'Edição do produto',
            ]);
        }

        $product->update($data);
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.products.edit', $product)
                        ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        // Verificar se o produto tem pedidos
        if ($product->orderItems()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Não é possível excluir um produto que possui pedidos associados.');
        }

        // Remover imagens
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                        ->with('success', 'Produto excluído com sucesso!');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $oldStock = $product->stock_quantity;
        $quantity = $request->quantity;
        
        $newStock = match ($request->type) {
            'in' => $oldStock + $quantity,
            'out' => max(0, $oldStock - $quantity),
            'adjustment' => $quantity,
        };

        $quantityChange = $newStock - $oldStock;

        // Criar log de estoque
        InventoryLog::create([
            'product_id' => $product->id,
            'admin_id' => auth('admin')->id(),
            'type' => $request->type,
            'quantity_before' => $oldStock,
            'quantity_change' => $quantityChange,
            'quantity_after' => $newStock,
            'notes' => $request->notes,
            'reference' => 'Ajuste manual',
        ]);

        // Atualizar estoque do produto
        $product->update([
            'stock_quantity' => $newStock,
            'in_stock' => $newStock > 0,
        ]);

        // Se for requisição AJAX, retornar JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Estoque ajustado com sucesso!',
                'data' => [
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'change' => $quantityChange,
                ]
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Estoque ajustado com sucesso!');
    }

    /**
     * Ações em massa para produtos
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_unavailable,mark_available,delete',
            'product_ids' => 'required|string',
        ]);

        // Parse JSON array de IDs
        $productIdsJson = $request->product_ids;
        $productIds = json_decode($productIdsJson, true);
        
        if (!is_array($productIds) || empty($productIds)) {
            return redirect()->back()
                        ->with('error', 'Nenhum produto selecionado.');
        }

        // Validar que todos os IDs existem
        $existingIds = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $invalidIds = array_diff($productIds, $existingIds);
        
        if (!empty($invalidIds)) {
            return redirect()->back()
                        ->with('error', 'Alguns produtos selecionados não existem.');
        }

        $action = $request->action;
        $count = 0;
        $skippedCount = 0;

        foreach ($existingIds as $productId) {
            $product = Product::find($productId);
            if ($product) {
                if ($action === 'mark_unavailable') {
                    $product->is_unavailable = true;
                    $product->save();
                    $count++;
                } elseif ($action === 'mark_available') {
                    $product->is_unavailable = false;
                    $product->save();
                    $count++;
                } elseif ($action === 'delete') {
                    // Verificar se o produto tem pedidos associados
                    if ($product->orderItems()->count() > 0) {
                        $skippedCount++;
                        continue; // Pular este produto, não pode ser excluído
                    }
                    
                    // Excluir imagens do produto
                    if ($product->images) {
                        $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                        if (is_array($images)) {
                            foreach ($images as $image) {
                                if ($image && Storage::disk('public')->exists($image)) {
                                    Storage::disk('public')->delete($image);
                                }
                            }
                        }
                    }
                    
                    // Excluir variações do produto
                    $product->variations()->delete();
                    
                    // Excluir relacionamentos com categorias
                    $product->categories()->detach();
                    
                    // Excluir itens do carrinho relacionados
                    $product->cartItems()->delete();
                    
                    // Excluir o produto
                    $product->delete();
                    $count++;
                }
            }
        }

        $message = match($action) {
            'mark_unavailable' => "{$count} produto(s) marcado(s) como indisponível(is)!",
            'mark_available' => "{$count} produto(s) marcado(s) como disponível(is)!",
            'delete' => $skippedCount > 0 
                ? "{$count} produto(s) excluído(s) com sucesso! {$skippedCount} produto(s) não puderam ser excluídos por possuírem pedidos associados."
                : "{$count} produto(s) excluído(s) com sucesso!",
            default => "Ação realizada com sucesso!"
        };

        $messageType = ($action === 'delete' && $skippedCount > 0 && $count > 0) ? 'warning' : 'success';

        return redirect()->back()
                        ->with($messageType, $message);
    }

    /**
     * Atualizar preço de custo e recalcular B2B/B2C baseado nas margens de lucro
     */
    public function updateCostPrice(Request $request, Product $product)
    {
        try {
            $request->validate([
                'cost_price' => 'required|numeric|min:0',
            ]);

            $costPrice = (float) $request->cost_price;
            
            // Validar que o custo é válido
            if ($costPrice < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'O preço de custo não pode ser negativo.'
                ], 422);
            }

            // Obter margens de lucro (padrão: B2B 10%, B2C 20%)
            $profitMarginB2B = $product->profit_margin_b2b ?? 10.00;
            $profitMarginB2C = $product->profit_margin_b2c ?? 20.00;
            if ($profitMarginB2B < 0 || $profitMarginB2B > 1000) {
                $profitMarginB2B = 10.00;
            }
            if ($profitMarginB2C < 0 || $profitMarginB2C > 1000) {
                $profitMarginB2C = 20.00;
            }

            // Calcular preços com margem de lucro
            // Fórmula: Preço = Custo * (1 + Margem/100)
            $b2bPrice = round($costPrice * (1 + $profitMarginB2B / 100), 2);
            $b2cPrice = round($costPrice * (1 + $profitMarginB2C / 100), 2);

            // Validar que os preços calculados são válidos
            if ($b2bPrice <= 0 || $b2cPrice <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao calcular preços. Verifique as margens de lucro.'
                ], 422);
            }

            // Atualizar produto com custo e preços recalculados
            $product->update([
                'cost_price' => $costPrice,
                'b2b_price' => $b2bPrice,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preço de custo atualizado e preços recalculados com sucesso!',
                'product' => [
                    'cost_price' => number_format($costPrice, 2, ',', '.'),
                    'b2b_price' => number_format($b2bPrice, 2, ',', '.'),
                    'b2c_price' => number_format($b2cPrice, 2, ',', '.'),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', $e->errors()['cost_price'] ?? []),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar preço de custo: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar preço: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar margem de lucro e recalcular preços B2B/B2C
     */
    public function updateProfitMargin(Request $request, Product $product)
    {
        try {
            $request->validate([
                'type' => 'required|in:b2b,b2c',
                'margin' => 'required|numeric|min:0|max:1000',
            ]);

            $type = $request->type;
            $margin = (float) $request->margin;

            // Validar margem
            if ($margin < 0 || $margin > 1000) {
                return response()->json([
                    'success' => false,
                    'message' => 'A margem de lucro deve estar entre 0% e 1000%.'
                ], 422);
            }

            // Atualizar margem de lucro
            if ($type === 'b2b') {
                $product->update(['profit_margin_b2b' => $margin]);
            } else {
                $product->update(['profit_margin_b2c' => $margin]);
            }

            // Recalcular preços se houver custo definido
            if ($product->cost_price && $product->cost_price > 0) {
                $profitMarginB2B = $product->profit_margin_b2b ?? 10.00;
                $profitMarginB2C = $product->profit_margin_b2c ?? 20.00;

                $b2bPrice = round($product->cost_price * (1 + $profitMarginB2B / 100), 2);
                $b2cPrice = round($product->cost_price * (1 + $profitMarginB2C / 100), 2);

                $product->update([
                    'b2b_price' => $b2bPrice,
                    'price' => $b2cPrice,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Margem de lucro atualizada e preços recalculados com sucesso!',
                'product' => [
                    'profit_margin_b2b' => number_format($product->profit_margin_b2b ?? 10.00, 2, ',', '.'),
                    'profit_margin_b2c' => number_format($product->profit_margin_b2c ?? 20.00, 2, ',', '.'),
                    'b2b_price' => $product->b2b_price ? number_format($product->b2b_price, 2, ',', '.') : null,
                    'b2c_price' => $product->price ? number_format($product->price, 2, ',', '.') : null,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', array_merge(
                    $e->errors()['type'] ?? [],
                    $e->errors()['margin'] ?? []
                )),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar margem de lucro: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar margem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar margens de lucro globalmente a todos os produtos
     */
    public function applyGlobalMargins(Request $request)
    {
        try {
            $request->validate([
                'profit_margin_b2b' => 'required|numeric|min:0|max:1000',
                'profit_margin_b2c' => 'required|numeric|min:0|max:1000',
                'recalculate_prices' => 'nullable|boolean',
            ]);

            $profitMarginB2B = (float) $request->profit_margin_b2b;
            $profitMarginB2C = (float) $request->profit_margin_b2c;
            $recalculatePrices = $request->boolean('recalculate_prices', true);

            // Validar margens
            if ($profitMarginB2B < 0 || $profitMarginB2B > 1000) {
                return response()->json([
                    'success' => false,
                    'message' => 'A margem de lucro B2B deve estar entre 0% e 1000%.'
                ], 422);
            }

            if ($profitMarginB2C < 0 || $profitMarginB2C > 1000) {
                return response()->json([
                    'success' => false,
                    'message' => 'A margem de lucro B2C deve estar entre 0% e 1000%.'
                ], 422);
            }

            // Buscar todos os produtos que têm preço de custo definido
            $products = Product::whereNotNull('cost_price')
                              ->where('cost_price', '>', 0)
                              ->get();

            $updated = 0;
            $errors = [];

            foreach ($products as $product) {
                try {
                    // Atualizar margens de lucro
                    $product->update([
                        'profit_margin_b2b' => $profitMarginB2B,
                        'profit_margin_b2c' => $profitMarginB2C,
                    ]);

                    // Recalcular preços se solicitado
                    if ($recalculatePrices && $product->cost_price > 0) {
                        $b2bPrice = round($product->cost_price * (1 + $profitMarginB2B / 100), 2);
                        $b2cPrice = round($product->cost_price * (1 + $profitMarginB2C / 100), 2);

                        $product->update([
                            'b2b_price' => $b2bPrice,
                            'price' => $b2cPrice,
                        ]);
                    }

                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Produto ID {$product->id} ({$product->name}): " . $e->getMessage();
                    \Log::error("Erro ao atualizar produto {$product->id} no controlador global", [
                        'product_id' => $product->id,
                        'exception' => $e
                    ]);
                }
            }

            $message = "Margens de lucro aplicadas com sucesso a {$updated} produto(s).";
            if (!empty($errors)) {
                $message .= " Erros: " . count($errors) . " produto(s) não puderam ser atualizados.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated' => $updated,
                'total' => $products->count(),
                'errors' => $errors
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', array_merge(
                    $e->errors()['profit_margin_b2b'] ?? [],
                    $e->errors()['profit_margin_b2c'] ?? []
                )),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao aplicar margens globais: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao aplicar margens: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Retorna as variações de um produto agrupadas por tipo
     */
    public function getVariations(Product $product)
    {
        // Use central normalizer service for hex/key normalization
        $normalizer = app(\App\Services\VariationNormalizer::class);

        $variations = $product->variations()->get();

        // Build dynamic attribute groups from the variations' attributes JSON or legacy columns
        $attributeValues = []; // ['ram' => ['8GB' => ['count'=>x,'enabled'=>y], ...], ...]

        foreach ($variations as $v) {
            // Prefer attributes JSON
            $vals = is_array($v->attributes) ? $v->attributes : [];

            // Include legacy columns as fallback
            if (empty($vals)) {
                if ($v->ram) $vals['ram'] = $v->ram;
                if ($v->storage) $vals['storage'] = $v->storage;
                if ($v->color) $vals['color'] = $v->color;
                if ($v->color_hex) $vals['color_hex'] = $v->color_hex;
            }

            foreach ($vals as $key => $value) {
                if ($key === 'color_hex') continue; // hex is metadata, not a variation axis
                if ($value === null || $value === '') continue;

                // Normalize value so it can be used as an array key (avoid Illegal offset type)
                $valKey = null;
                if (is_array($value)) {
                    // common shapes: ['value' => 'X'], ['name' => 'X'], ['cor' => 'vermelho'], or plain array
                    if (array_key_exists('value', $value)) {
                        $valKey = (string) $value['value'];
                    } elseif (array_key_exists('name', $value)) {
                        $valKey = (string) $value['name'];
                    } elseif (count($value) === 1) {
                        // take the single scalar child if present
                        $first = reset($value);
                        if (!is_array($first) && !is_object($first)) {
                            $valKey = (string) $first;
                        }
                    }
                    if ($valKey === null) {
                        // fallback to trying to find any string-like child
                        foreach ($value as $maybe) {
                            if (!is_array($maybe) && !is_object($maybe)) {
                                $valKey = (string) $maybe;
                                break;
                            }
                        }
                    }
                    if ($valKey === null) {
                        $valKey = json_encode($value);
                    }
                } elseif (is_object($value)) {
                    if (property_exists($value, 'value')) {
                        $valKey = (string) $value->value;
                    } elseif (property_exists($value, 'name')) {
                        $valKey = (string) $value->name;
                    } else {
                        // try first public property
                        $props = get_object_vars($value);
                        if (count($props) === 1) {
                            $first = reset($props);
                            if (!is_array($first) && !is_object($first)) {
                                $valKey = (string) $first;
                            }
                        }
                        if ($valKey === null) {
                            foreach ($props as $maybe) {
                                if (!is_array($maybe) && !is_object($maybe)) {
                                    $valKey = (string) $maybe;
                                    break;
                                }
                            }
                        }
                        if ($valKey === null) {
                            $valKey = json_encode($value);
                        }
                    }
                } else {
                    $valKey = (string) $value;
                }

                $attributeValues[$key][$valKey][] = $v;
            }
        }

        $attributeGroups = [];
        foreach ($attributeValues as $key => $map) {
            $group = collect($map)->map(function ($items, $name) {
                $active = collect($items)->where('is_active', true)->count() > 0;
                return [
                    'name' => $name,
                    'count' => count($items),
                    'enabled' => $active,
                ];
            })->values();

            $attributeGroups[$key] = $group;
        }

        // Maintain compatibility: expose colors/rams/storages arrays if present
        $colors = $attributeGroups['color'] ?? collect([]);
        $rams = $attributeGroups['ram'] ?? collect([]);
        $storages = $attributeGroups['storage'] ?? collect([]);

        // Variations list - include attributes map for each variation
        $variationsList = $variations->map(function($variation) {
            $attrs = is_array($variation->attributes) ? $variation->attributes : [];
            // fallback to legacy
            if (empty($attrs)) {
                $attrs = [
                    'ram' => $variation->ram,
                    'storage' => $variation->storage,
                    'color' => $variation->color,
                    'color_hex' => $variation->color_hex,
                ];
            }

            $nameParts = [];
            if (!empty($attrs['ram'])) $nameParts[] = $attrs['ram'];
            if (!empty($attrs['storage'])) $nameParts[] = $attrs['storage'];
            if (!empty($attrs['color'])) $nameParts[] = $attrs['color'];

            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'name' => implode(' / ', $nameParts) ?: $variation->sku,
                'attributes' => $attrs,
                'stock_quantity' => $variation->stock_quantity,
                'in_stock' => $variation->in_stock,
                'is_active' => $variation->is_active,
                'price' => $variation->price,
                'b2b_price' => $variation->b2b_price,
                'cost_price' => $variation->cost_price,
            ];
        });

        // Build color hex map by reading attributes or legacy column. Support structured attribute values.
        $colorHexMap = [];
        foreach ($variations as $v) {
            $attrs = is_array($v->attributes) ? $v->attributes : [];
            $rawColor = $attrs['color'] ?? ($v->color ?? null);
            $rawHex = $attrs['color_hex'] ?? ($v->color_hex ?? null);

            $colorName = null;
            $hex = null;

            if (is_array($rawColor)) {
                if (array_key_exists('value', $rawColor)) $colorName = (string) $rawColor['value'];
                elseif (array_key_exists('name', $rawColor)) $colorName = (string) $rawColor['name'];
                elseif (count($rawColor) === 1) $colorName = (string) reset($rawColor);
                else $colorName = json_encode($rawColor);
            } elseif (is_object($rawColor)) {
                $vars = get_object_vars($rawColor);
                if (array_key_exists('value', $vars)) $colorName = (string) $vars['value'];
                elseif (array_key_exists('name', $vars)) $colorName = (string) $vars['name'];
                elseif (count($vars) === 1) $colorName = (string) reset($vars);
                else $colorName = json_encode($rawColor);
            } else {
                $colorName = $rawColor;
            }

            if ($rawHex) {
                $hex = $rawHex;
            } elseif (is_array($rawColor) && array_key_exists('hex', $rawColor)) {
                $hex = $rawColor['hex'];
            } elseif (is_object($rawColor) && property_exists($rawColor, 'hex')) {
                $hex = $rawColor->hex;
            }

            if ($colorName && $hex) {
                $colorHexMap[$colorName] = $hex;
            }
        }

        // Build tolerant normalized color map and attach hex to color group entries
        $normalizedColorMap = $normalizer->buildNormalizedColorMap($colorHexMap);
        $attributeGroups = $normalizer->attachHexToColorGroup($attributeGroups, $normalizedColorMap);

        return response()->json([
            'success' => true,
            'productId' => $product->id,
            'attribute_groups' => $attributeGroups,
            'colors' => $colors,
            'rams' => $rams,
            'storages' => $storages,
            'variations' => $variationsList,
            'product_images' => $product->images ?? [],
            'product_images_urls' => $product->all_images,
            'color_images' => $product->variation_images ?? [],
            'color_images_urls' => $product->variation_images_urls,
            'color_hex_map' => $normalizedColorMap ?? $colorHexMap,
            'margins' => [
                'b2c' => $product->profit_margin_b2c ?? 20.0,
                'b2b' => $product->profit_margin_b2b ?? 10.0,
            ],
            'defaults' => [
                'price' => $product->price,
                'b2b_price' => $product->b2b_price,
                'cost_price' => $product->cost_price,
            ]
        ]);
    }

    /**
     * Atualiza as imagens associadas a uma cor específica
     */
    public function updateColorImages(Request $request, Product $product)
    {
        $request->validate([
            'color' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string',
        ]);

        $color = trim($request->color);
        $selectedImages = array_values(array_filter($request->images ?? []));

        if ($color === '') {
            return response()->json([
                'success' => false,
                'message' => 'Cor inválida.'
            ], 422);
        }

        $availableImages = $product->images ?? [];

        // Garantir que as imagens selecionadas existem no produto
        $availableLookup = array_flip($availableImages);

        foreach ($selectedImages as $image) {
            if (!isset($availableLookup[$image])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alguma das imagens selecionadas não pertence ao produto.'
                ], 422);
            }
        }

        $variationImages = $product->variation_images ?? [];

        if (empty($selectedImages)) {
            unset($variationImages[$color]);
        } else {
            $variationImages[$color] = $selectedImages;
        }

        $product->update([
            'variation_images' => $variationImages
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Imagens da cor atualizadas com sucesso!',
            'color_images' => $variationImages,
            'color_images_urls' => $product->fresh()->variation_images_urls,
        ]);
    }

    /**
     * Habilita/desabilita variações por tipo e valor
     */
    public function toggleVariation(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'value' => 'required|string',
            'enabled' => 'required|boolean'
        ]);

        $type = $request->type;
        $value = $request->value;
        $enabled = $request->enabled;

        // Update variations where attributes->type == value OR legacy column equals value
        $query = $product->variations();
        $updated = $query->where(function($q) use ($type, $value) {
            // JSON attribute
            $q->where("attributes->${type}", $value)
              ->orWhere($type, $value);
        })->update(['is_active' => $enabled]);

        return response()->json([
            'success' => true,
            'message' => $updated . ' variação(ões) atualizada(s)',
            'updated' => $updated
        ]);
    }

    /**
     * Adiciona uma nova variação ou habilita variações existentes
     */
    public function addVariation(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'value' => 'required|string|max:255'
        ]);

        $type = $request->type;
        $value = trim($request->value);

        if (empty($value)) {
            return response()->json([
                'success' => false,
                'message' => 'O valor não pode estar vazio'
            ], 400);
        }

        // Check existing by JSON attribute or legacy column
        $existingVariations = $product->variations()->where(function($q) use ($type, $value) {
            $q->where("attributes->${type}", $value)->orWhere($type, $value);
        })->get();

        if ($existingVariations->count() > 0) {
            $existingVariations->each(function($variation) {
                $variation->update(['is_active' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Variações existentes foram habilitadas',
                'action' => 'enabled_existing'
            ]);
        }

        // Build existing attribute value lists by scanning current variations
        $existingAll = $product->variations()->get();

        $rams = collect();
        $storages = collect();
        $colors = collect();

        foreach ($existingAll as $ev) {
            $attrs = is_array($ev->attributes) ? $ev->attributes : [];
            if (!empty($attrs['ram'])) $rams->push($attrs['ram']); elseif (!empty($ev->ram)) $rams->push($ev->ram);
            if (!empty($attrs['storage'])) $storages->push($attrs['storage']); elseif (!empty($ev->storage)) $storages->push($ev->storage);
            if (!empty($attrs['color'])) $colors->push($attrs['color']); elseif (!empty($ev->color)) $colors->push($ev->color);
        }

        $rams = $rams->unique()->values();
        $storages = $storages->unique()->values();
        $colors = $colors->unique()->values();

        // Add the new value to the proper list
        if ($type === 'color') {
            $colors = $colors->push($value)->unique()->values();
        } elseif ($type === 'ram') {
            $rams = $rams->push($value)->unique()->values();
        } elseif ($type === 'storage') {
            $storages = $storages->push($value)->unique()->values();
        } else {
            // For arbitrary attribute types, create variations pairing with existing core attributes
            // We'll treat unknown type as another axis and pair with existing known axes
            // Ensure lists are not empty
            if ($rams->isEmpty()) $rams = collect(['']);
            if ($storages->isEmpty()) $storages = collect(['']);
            if ($colors->isEmpty()) $colors = collect(['']);
            // We'll append the new value into a special collection keyed by $type later when creating combos
        }

        if ($rams->isEmpty()) $rams = collect(['']);
        if ($storages->isEmpty()) $storages = collect(['']);
        if ($colors->isEmpty()) $colors = collect(['']);

        $created = 0;

        foreach ($rams as $ram) {
            foreach ($storages as $storage) {
                foreach ($colors as $color) {
                    // Determine attribute map for this combination
                    $attrs = [];
                    if ($ram) $attrs['ram'] = $ram;
                    if ($storage) $attrs['storage'] = $storage;
                    if ($color) $attrs['color'] = $color;

                    // If the new type is not one of the core ones, set it
                    if (!in_array($type, ['ram','storage','color'])) {
                        $attrs[$type] = $value;
                    } else {
                        // ensure the new value is present in the correct axis
                        if ($type === 'ram') $attrs['ram'] = $value;
                        if ($type === 'storage') $attrs['storage'] = $value;
                        if ($type === 'color') $attrs['color'] = $value;
                    }

                    // Check existence by matching attributes JSON or legacy columns
                    $query = $product->variations();
                    foreach ($attrs as $k => $v) {
                        if ($v === '' || $v === null) {
                            $query->whereNull($k)->orWhereNull("attributes->${k}");
                        } else {
                            $query->where(function($q) use ($k, $v) {
                                $q->where("attributes->${k}", $v)->orWhere($k, $v);
                            });
                        }
                    }

                    $existing = $query->first();
                    if (!$existing) {
                        // Build SKU from available parts
                        $skuParts = [$product->sku];
                        if (!empty($attrs['ram'])) $skuParts[] = str_replace('GB', '', $attrs['ram']);
                        if (!empty($attrs['storage'])) $skuParts[] = str_replace('GB', '', $attrs['storage']);
                        if (!empty($attrs['color'])) $skuParts[] = strtoupper(substr($attrs['color'], 0, 3));
                        if (!empty($attrs[$type]) && !in_array($type, ['ram','storage','color'])) $skuParts[] = strtoupper(substr($attrs[$type], 0, 3));
                        $sku = implode('-', array_filter($skuParts));

                        while (ProductVariation::where('sku', $sku)->exists()) {
                            $sku .= '-' . rand(100, 999);
                        }

                        ProductVariation::create([
                            'product_id' => $product->id,
                            'attributes' => $attrs ?: null,
                            'ram' => $attrs['ram'] ?? null,
                            'storage' => $attrs['storage'] ?? null,
                            'color' => $attrs['color'] ?? null,
                            'sku' => $sku,
                            'price' => $product->price,
                            'b2b_price' => $product->b2b_price,
                            'cost_price' => $product->cost_price,
                            'stock_quantity' => 0,
                            'in_stock' => false,
                            'is_active' => true,
                            'sort_order' => 0
                        ]);
                        $created++;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $created . ' nova(s) variação(ões) criada(s)',
            'created' => $created
        ]);
    }

    /**
     * Bulk add full variation combinations.
     * Expects payload: { combos: [ { ram: '12GB', storage: '256GB', color: 'Azul' }, ... ] }
     */
    public function bulkAddVariations(Request $request, Product $product)
    {
        $request->validate([
            'combos' => 'required|array',
        ]);

        $combos = $request->input('combos', []);
        // Temporary debug trace: log the incoming combos shape to diagnose array bindings
        try {
            \Log::debug('bulkAddVariations payload', ['product_id' => $product->id, 'combos_sample' => array_slice($combos, 0, 10)]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }
        $created = 0;

        foreach ($combos as $c) {
            // Support legacy payloads where each combo may be wrapped as { attributes: { ... }, ... }
            if (is_array($c) && isset($c['attributes']) && is_array($c['attributes'])) {
                $c = $c['attributes'];
            }
            // Accept arbitrary keys in combo; normalize into attributes
            $attrs = [];
            foreach ($c as $k => $v) {
                $v = is_string($v) ? trim($v) : $v;
                if ($v === '' || $v === null) continue;
                $attrs[$k] = $v;
            }

            if (empty($attrs)) continue;

            // Normalize attribute values: convert arrays/objects to JSON strings to avoid SQL binding errors
            foreach ($attrs as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    try {
                        $attrs[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
                    } catch (\Throwable $e) {
                        $attrs[$k] = (string) $v;
                    }
                }
                // Trim string values
                if (is_string($attrs[$k])) {
                    $attrs[$k] = trim($attrs[$k]);
                }
            }

            // Deterministic normalized string and hash for quick existence checks
            try {
                // reuse model helper if available
                $normalized = \App\Models\ProductVariation::normalizeAttributesForHash($attrs);
                $hash = md5($normalized);
            } catch (\Throwable $e) {
                $hash = null;
            }

            // Use transaction per combo to reduce race window
            DB::transaction(function() use (&$created, $product, $attrs, $hash, $normalized) {
                // check by attributes_hash first (fast, indexable once migration applied)
                $existsQuery = ProductVariation::where('product_id', $product->id);
                if ($hash) {
                    $existsQuery = $existsQuery->where('attributes_hash', $hash);
                } else {
                    // fallback: match on legacy columns or attributes JSON equality
                    foreach ($attrs as $k => $v) {
                        $existsQuery = $existsQuery->where(function($q) use ($k, $v) {
                            $q->where("attributes->${k}", $v)->orWhere($k, $v);
                        });
                    }
                }

                if ($existsQuery->lockForUpdate()->exists()) {
                    return; // already exists, skip
                }

                // Build readable SKU: use known axes if present
                $skuParts = [$product->sku];
                if (!empty($attrs['ram'])) $skuParts[] = str_replace('GB', '', $attrs['ram']);
                if (!empty($attrs['storage'])) $skuParts[] = str_replace('GB', '', $attrs['storage']);
                if (!empty($attrs['color'])) $skuParts[] = strtoupper(substr($attrs['color'], 0, 3));
                if (count($skuParts) === 1 && !empty($attrs)) {
                    $skuParts[] = strtoupper(substr(md5($normalized ?? json_encode($attrs)), 0, 6));
                }
                $sku = implode('-', array_filter($skuParts));

                while (ProductVariation::where('sku', $sku)->exists()) {
                    $sku .= '-' . rand(100, 999);
                }

                ProductVariation::create([
                    'product_id' => $product->id,
                    'attributes' => $attrs ?: null,
                    'attributes_hash' => $hash,
                    'ram' => $attrs['ram'] ?? null,
                    'storage' => $attrs['storage'] ?? null,
                    'color' => $attrs['color'] ?? null,
                    'sku' => $sku,
                    'price' => $product->price,
                    'b2b_price' => $product->b2b_price,
                    'cost_price' => $product->cost_price,
                    'stock_quantity' => 0,
                    'in_stock' => false,
                    'is_active' => true,
                    'sort_order' => 0
                ]);
                $created++;
            });
        }

        return response()->json([
            'success' => true,
            'message' => $created . ' nova(s) variação(ões) criada(s)',
            'created' => $created
        ]);
    }

    /**
     * Atualiza o estoque de múltiplas variações
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.variation_id' => 'required|exists:product_variations,id',
            'updates.*.stock_quantity' => 'required|integer|min:0',
            'updates.*.in_stock' => 'required|boolean',
            'updates.*.cost_price' => 'nullable|numeric|min:0',
            'updates.*.price' => 'nullable|numeric|min:0',
            'updates.*.b2b_price' => 'nullable|numeric|min:0',
        ]);
        
        $updated = 0;
        
        foreach ($request->updates as $update) {
            $variation = ProductVariation::find($update['variation_id']);
            
            // Verificar se a variação pertence ao produto
            if ($variation && $variation->product_id === $product->id) {
                $dataToUpdate = [
                    'stock_quantity' => $update['stock_quantity'],
                    'in_stock' => $update['in_stock']
                ];

                $costPrice = array_key_exists('cost_price', $update) ? $update['cost_price'] : null;
                $price = array_key_exists('price', $update) ? $update['price'] : null;
                $b2bPrice = array_key_exists('b2b_price', $update) ? $update['b2b_price'] : null;

                $profitMarginB2C = $product->profit_margin_b2c ?? 20.0;
                $profitMarginB2B = $product->profit_margin_b2b ?? 10.0;

                if (!is_null($costPrice)) {
                    $costPrice = round($costPrice, 2);
                    $dataToUpdate['cost_price'] = $costPrice;

                    if (is_null($price)) {
                        $price = round($costPrice * (1 + ($profitMarginB2C / 100)), 2);
                    }

                    if (is_null($b2bPrice)) {
                        $b2bPrice = round($costPrice * (1 + ($profitMarginB2B / 100)), 2);
                    }
                }

                if (!is_null($price)) {
                    $dataToUpdate['price'] = round($price, 2);
                }

                if (!is_null($b2bPrice)) {
                    $dataToUpdate['b2b_price'] = round($b2bPrice, 2);
                }

                $variation->update($dataToUpdate);
                $updated++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Estoque de {$updated} variação(ões) atualizado(s)",
            'updated' => $updated
        ]);
    }

    public function updateColorHex(Request $request, Product $product)
    {
        $request->validate([
            'color' => 'required|string',
            'hex' => 'nullable|string|regex:/^#?[0-9a-fA-F]{3,8}$/'
        ]);

        $color = trim($request->color);
        $hex = $request->hex ? '#' . ltrim($request->hex, '#') : null;

        $variations = $product->variations()->where('color', $color)->get();

        if ($variations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma variação encontrada para essa cor.'
            ], 404);
        }

        foreach ($variations as $variation) {
            // Update legacy column for compatibility
            $variation->color_hex = $hex;

            // Also update JSON attributes->color_hex
            $attrs = is_array($variation->attributes) ? $variation->attributes : [];
            $attrs['color_hex'] = $hex;
            $variation->attributes = $attrs;

            $variation->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cor atualizada com sucesso.',
            'hex' => $hex
        ]);
    }

    /**
     * Remove uma variação específica do produto
     */
    public function deleteVariation(Request $request, Product $product, $variationId)
    {
        // Converter o ID para inteiro para garantir que seja um número válido
        $variationId = (int) $variationId;
        
        if ($variationId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ID de variação inválido.'
            ], 422);
        }

        // Buscar a variação manualmente verificando se pertence ao produto
        $variation = ProductVariation::where('id', $variationId)
            ->where('product_id', $product->id)
            ->first();
        
        if (!$variation) {
            return response()->json([
                'success' => false,
                'message' => 'Variação não encontrada ou não pertence a este produto.'
            ], 404);
        }

        try {
            $variation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Variação removida com sucesso.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao remover variação', [
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível remover a variação. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Remove todas as variações desativadas de um produto
     */
    public function deleteInactiveVariations(Product $product)
    {
        try {
            $inactiveVariations = $product->variations()->where('is_active', false)->get();
            
            if ($inactiveVariations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma variação desativada encontrada.'
                ], 404);
            }

            $count = 0;
            foreach ($inactiveVariations as $variation) {
                $variation->delete();
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} variação(ões) desativada(s) removida(s) com sucesso."
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao remover variações desativadas', [
                'product_id' => $product->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível remover as variações desativadas. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Remove todas as variações que correspondem a um determinado valor de cor/RAM/armazenamento
     */
    public function deleteVariationValue(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'value' => 'required|string'
        ]);

        $type = $request->type;
        $value = trim($request->value);

        if ($value === '') {
            return response()->json([
                'success' => false,
                'message' => 'Valor inválido.'
            ], 422);
        }

        // Find variations by attributes JSON or legacy column
        $variations = $product->variations()->where(function($q) use ($type, $value) {
            $q->where("attributes->${type}", $value)->orWhere($type, $value);
        })->get();

        if ($variations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma variação encontrada com esse valor.'
            ], 404);
        }

        try {
            $deleted = 0;
            foreach ($variations as $variation) {
                $variation->delete();
                $deleted++;
            }

            return response()->json([
                'success' => true,
                'message' => $deleted . ' variação(ões) removida(s) com sucesso.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao remover variações por valor', [
                'product_id' => $product->id,
                'type' => $type,
                'value' => $value,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível remover as variações. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Retorna as imagens do produto para o modal
     */
    public function getImages(Product $product)
    {
        $images = $product->all_images ?? [];
        $featuredImage = $product->first_image ?? null;
        
        // Converter URLs relativas para absolutas se necessário
        $formattedImages = [];
        foreach ($images as $image) {
            if (empty($image)) {
                continue;
            }
            
            if (strpos($image, 'http') === 0 || strpos($image, 'https') === 0) {
                $formattedImages[] = $image;
            } elseif (strpos($image, '/') === 0) {
                $formattedImages[] = url(ltrim($image, '/'));
            } else {
                $formattedImages[] = asset('storage/' . $image);
            }
        }
        
        return response()->json([
            'success' => true,
            'images' => $formattedImages,
            'featured_image' => $featuredImage ? (strpos($featuredImage, 'http') === 0 ? $featuredImage : asset('storage/' . $featuredImage)) : null
        ]);
    }

    /**
     * Atualiza as imagens do produto - seguindo padrão do banner
     */
    public function updateImages(Request $request, Product $product)
    {
        try {
            $request->validate([
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
                'featured_image_url' => 'nullable|url|starts_with:http,https',
                'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
                'additional_image_urls' => 'nullable|array',
                'additional_image_urls.*' => 'nullable|url|starts_with:http,https',
                'remove_featured_image' => 'nullable|boolean',
                'existing_featured_image' => 'nullable|string',
                'existing_additional_images' => 'nullable|array',
            ]);

            $imagePaths = [];
            $currentImages = $product->images ?? [];
            
            // 1. Processar imagem de destaque
            $removeFeatured = $request->has('remove_featured_image') && $request->remove_featured_image == '1';
            
            if ($request->hasFile('featured_image')) {
                // Nova imagem de destaque foi enviada
                $featuredImage = $request->file('featured_image');
                if ($featuredImage->isValid()) {
                    $path = $featuredImage->store('products', 'public');
                    $imagePaths[] = $path; // Adicionar como primeira imagem
                    \Log::info('Nova imagem de destaque adicionada', ['path' => $path]);
                }
            } elseif ($request->filled('featured_image_url') && !$removeFeatured) {
                // Usar URL externa como imagem de destaque
                $url = trim($request->featured_image_url);
                $imagePaths[] = $url; // manter URL absoluta
                \Log::info('Imagem de destaque via URL adicionada', ['url' => $url]);
            } elseif (!$removeFeatured) {
                // Se não foi enviada nova imagem e não foi marcada para remover, manter a existente
                if ($request->has('existing_featured_image') && !empty($request->existing_featured_image)) {
                    // Manter imagem de destaque existente do request
                    $existingFeatured = $this->extractImagePath($request->existing_featured_image);
                    if ($existingFeatured) {
                        $imagePaths[] = $existingFeatured;
                        \Log::info('Imagem de destaque mantida (do request)', ['path' => $existingFeatured]);
                    }
                } elseif (!empty($currentImages)) {
                    // Se não veio no request mas existe no produto, manter a primeira imagem (destaque)
                    $firstImage = is_array($currentImages) ? $currentImages[0] : $currentImages;
                    if ($firstImage) {
                        $imagePaths[] = $firstImage;
                        \Log::info('Imagem de destaque mantida (primeira do produto)', ['path' => $firstImage]);
                    }
                }
            }
            
            // 2. Processar imagens adicionais existentes (que não foram marcadas para remover)
            // IMPORTANTE: Manter TODAS as imagens adicionais existentes que não foram removidas
            if ($request->has('existing_additional_images') && is_array($request->existing_additional_images)) {
                foreach ($request->existing_additional_images as $image) {
                    if (empty($image)) {
                        continue;
                    }
                    
                    $extractedPath = $this->extractImagePath($image);
                    if ($extractedPath) {
                        // Normalizar caminho para comparação (remover barras iniciais)
                        $normalizedPath = ltrim($extractedPath, '/');
                        
                        // Verificar se já existe no array (comparando caminhos normalizados)
                        $exists = false;
                        foreach ($imagePaths as $existingPath) {
                            $normalizedExisting = ltrim($existingPath, '/');
                            if (strcasecmp($normalizedPath, $normalizedExisting) === 0) {
                                $exists = true;
                                break;
                            }
                        }
                        
                        if (!$exists) {
                            $imagePaths[] = $extractedPath;
                            \Log::info('Imagem adicional mantida', ['path' => $extractedPath]);
                        } else {
                            \Log::info('Imagem adicional já existe, ignorando duplicata', ['path' => $extractedPath]);
                        }
                    }
                }
            }
            
            // 3. Adicionar novas imagens adicionais se houver upload
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('products', 'public');
                        $imagePaths[] = $path;
                        \Log::info('Nova imagem adicional adicionada', ['path' => $path]);
                    }
                }
            }
            // 3.1 Adicionar novas imagens adicionais via URLs
            if ($request->has('additional_image_urls') && is_array($request->additional_image_urls)) {
                foreach ($request->additional_image_urls as $url) {
                    $url = trim((string)$url);
                    if ($url === '') { continue; }
                    // Evitar duplicatas simples (comparando string exata)
                    if (!in_array($url, $imagePaths, true)) {
                        $imagePaths[] = $url;
                        \Log::info('Nova imagem adicional via URL adicionada', ['url' => $url]);
                    }
                }
            }
            
            \Log::info('Total de imagens após processamento', [
                'total' => count($imagePaths),
                'paths' => $imagePaths
            ]);
            
            // 4. Atualizar produto
            $product->update(['images' => $imagePaths]);
            
            return response()->json([
                'success' => true,
                'message' => 'Imagens atualizadas com sucesso!',
                'images' => $product->all_images
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar imagens do produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar imagens: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove uma imagem específica do produto
     */
    public function removeImage(Request $request, Product $product)
    {
        try {
            $request->validate([
                'image_path' => 'required|string'
            ]);

            $originalInput = (string)$request->image_path;
            $imagePath = $this->extractImagePath($originalInput);
            
            if (!$imagePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Caminho da imagem inválido'
                ], 400);
            }

            $currentImages = $product->images ?? [];
            
            // Remover a imagem do array
            $updatedImages = array_filter($currentImages, function($img) use ($imagePath, $originalInput) {
                // Remover se for exatamente igual ao armazenado, ou igual ao caminho normalizado
                if ($img === $originalInput) return false;
                if ($img === $imagePath) return false;
                return true;
            });
            
            // Reindexar o array
            $updatedImages = array_values($updatedImages);
            
            // Atualizar produto
            $product->update(['images' => $updatedImages]);
            
            // Opcional: deletar o arquivo físico do storage
            // Storage::disk('public')->delete($imagePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso!',
                'images' => $product->all_images
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao remover imagem do produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza preço de uma variação específica
     */
    public function updateVariationPrice(Request $request, $variationId)
    {
        try {
            $request->validate([
                'field' => 'required|in:cost_price,b2b_price,b2c_price',
                'value' => 'required|numeric|min:0'
            ]);
            
            $variation = ProductVariation::with('product')->findOrFail($variationId);
            $product = $variation->product;
            
            $field = $request->field;
            $value = $request->value;
            
            // Atualizar o campo solicitado
            $updateData = [$field => $value];
            
            // Se está atualizando o custo, recalcular B2B e B2C automaticamente
            if ($field === 'cost_price' && $value > 0) {
                $profitMarginB2B = $product->profit_margin_b2b ?? 10.00;
                $profitMarginB2C = $product->profit_margin_b2c ?? 20.00;
                
                // Calcular B2B e B2C baseado nas margens do produto pai
                $updateData['b2b_price'] = $value * (1 + ($profitMarginB2B / 100));
                $updateData['price'] = $value * (1 + ($profitMarginB2C / 100));
            }
            
            $variation->update($updateData);
            
            // Recarregar para pegar valores atualizados
            $variation->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Preço atualizado com sucesso!',
                'variation' => [
                    'id' => $variation->id,
                    'cost_price' => $variation->cost_price,
                    'b2b_price' => $variation->b2b_price,
                    'b2c_price' => $variation->price, // B2C é armazenado em 'price'
                    'profit_margin_b2b' => $product->profit_margin_b2b ?? 10.00,
                    'profit_margin_b2c' => $product->profit_margin_b2c ?? 20.00,
                ],
                'formatted' => [
                    'cost_price' => number_format($variation->cost_price, 2, ',', '.'),
                    'b2b_price' => number_format($variation->b2b_price, 2, ',', '.'),
                    'b2c_price' => number_format($variation->price, 2, ',', '.'),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar preço da variação: ' . $e->getMessage(), [
                'variation_id' => $variationId,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar preço: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza a descrição do produto
     */
    public function updateDescription(Request $request, Product $product)
    {
        try {
            $request->validate([
                'description' => 'nullable|string|max:5000'
            ]);
            
            $product->update([
                'description' => $request->description
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Descrição atualizada com sucesso!',
                'description' => $product->description
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar descrição: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar descrição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza rapidamente o nome (título) do produto
     */
    public function updateName(Request $request, Product $product)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2|max:255',
            ]);

            $newName = trim($request->name);

            // Atualiza nome e slug
            $product->update([
                'name' => $newName,
                'slug' => Str::slug($newName),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nome atualizado com sucesso!',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar nome do produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar nome: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai o caminho da imagem de uma URL ou caminho
     */
    private function extractImagePath($image)
    {
        if (empty($image)) {
            return null;
        }
        
        // Se já é um caminho relativo simples (sem http e sem / inicial), retornar direto
        if (strpos($image, 'http') !== 0 && strpos($image, '/') !== 0) {
            return $image;
        }
        
        // Se é URL absoluta, extrair o caminho
        if (strpos($image, 'http') === 0) {
            $parsed = parse_url($image);
            $path = $parsed['path'] ?? '';
            
            // Remover /storage/ se presente
            if (strpos($path, '/storage/') === 0) {
                $path = substr($path, 9); // Remove '/storage/'
            } elseif (strpos($path, 'storage/') === 0) {
                $path = substr($path, 8); // Remove 'storage/'
            } elseif (strpos($path, '/') === 0) {
                // Se começa com / mas não tem storage, remover a barra inicial
                $path = substr($path, 1);
            }
            
            return !empty($path) ? $path : null;
        }
        
        // Se começa com /, remover
        if (strpos($image, '/') === 0) {
            return substr($image, 1);
        }
        
        // Já é um caminho relativo
        return $image;
    }

    // bulkToggleByBrand removed

    /**
     * Retorna atributos agregados por departamento (ram, storage, color)
     * Espera query param `department` com slug ou id.
     */
    public function attributesList(Request $request)
    {
        $deptParam = $request->query('department');

        if (empty($deptParam)) {
            return response()->json(['attributes' => []]);
        }

        // Resolver departamento por id ou slug
        $department = null;
        if (is_numeric($deptParam)) {
            $department = Department::find((int)$deptParam);
        } else {
            $department = Department::where('slug', $deptParam)->first();
        }

        if (!$department) {
            return response()->json(['attributes' => []]);
        }

        // Agregar valores distintos das colunas de variação para os produtos do departamento
        $deptId = $department->id;

        $rams = DB::table('product_variations')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->where('products.department_id', $deptId)
            ->whereNotNull('product_variations.ram')
            ->distinct()
            ->pluck('product_variations.ram')
            ->filter()
            ->values()
            ->toArray();

        $storages = DB::table('product_variations')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->where('products.department_id', $deptId)
            ->whereNotNull('product_variations.storage')
            ->distinct()
            ->pluck('product_variations.storage')
            ->filter()
            ->values()
            ->toArray();

        $colors = DB::table('product_variations')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->where('products.department_id', $deptId)
            ->whereNotNull('product_variations.color')
            ->distinct()
            ->pluck('product_variations.color')
            ->filter()
            ->values()
            ->toArray();

        // Mapa color -> hex
        $colorHexMapRaw = DB::table('product_variations')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->where('products.department_id', $deptId)
            ->whereNotNull('product_variations.color')
            ->whereNotNull('product_variations.color_hex')
            ->select('product_variations.color', 'product_variations.color_hex')
            ->distinct()
            ->get();

        $colorHexMap = [];
        foreach ($colorHexMapRaw as $row) {
            $colorHexMap[$row->color] = strtolower($row->color_hex);
        }

        $attributes = [];
        // Use VariationNormalizer service for hex/key normalization
        $normalizer = app(\App\Services\VariationNormalizer::class);

        $normalizedColorMap = $normalizer->buildNormalizedColorMap($colorHexMap);

        if (!empty($colors)) {
            $attributes[] = [
                'key' => 'color',
                'name' => 'Cor',
                'values' => array_map(function ($c) use ($colorHexMap, $normalizer) {
                    $hex = $colorHexMap[$c] ?? null;
                    $hex = $normalizer->normalizeHex($hex);
                    return ['value' => $c, 'hex' => $hex];
                }, $colors)
            ];
        }

        if (!empty($rams)) {
            $attributes[] = [
                'key' => 'ram',
                'name' => 'RAM',
                'values' => array_map(function ($v) { return ['value' => $v]; }, $rams)
            ];
        }

        if (!empty($storages)) {
            $attributes[] = [
                'key' => 'storage',
                'name' => 'Armazenamento',
                'values' => array_map(function ($v) { return ['value' => $v]; }, $storages)
            ];
        }

        // Additionally, include central attributes defined in `attributes` table for this department
        $centralAttributes = AttributeModel::with('values')
            ->where(function($q) use ($deptId) {
                $q->where('department_id', $deptId)->orWhereNull('department_id');
            })->where('is_active', true)->get();

        foreach ($centralAttributes as $central) {
            $key = $central->key ?: Str::slug($central->name, '_');

            // Skip if we already have an attribute group with same key from variations
            $exists = collect($attributes)->firstWhere('key', $key);
            $values = [];

            // Include central attribute values regardless of is_active so frontend can offer activation
            foreach ($central->values as $val) {
                $values[] = ['value' => $val->value, 'hex' => $val->hex, 'is_active' => (bool)$val->is_active, 'value_id' => $val->id];
            }

            if (!empty($values) && !$exists) {
                $attributes[] = [
                    'key' => $key,
                    'name' => $central->name,
                    'attribute_id' => $central->id,
                    'values' => $values
                ];
            }
        }

        // Debug log to help trace why frontend may receive empty attributes
        try {
            \Log::debug('ProductController::attributesList response', [
                'department' => ['id' => $department->id, 'slug' => $department->slug, 'name' => $department->name],
                'attributes_count' => count($attributes),
                'attributes' => $attributes,
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors to avoid breaking endpoint
            \Log::warning('Failed to log attributesList debug info: ' . $e->getMessage());
        }

        return response()->json([
            'department' => ['id' => $department->id, 'slug' => $department->slug, 'name' => $department->name],
            'attributes' => $attributes,
            'color_hex_map' => $normalizedColorMap ?? []
        ]);
    }
}
