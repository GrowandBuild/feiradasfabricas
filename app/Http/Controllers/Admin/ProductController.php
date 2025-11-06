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
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('categories');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', $request->supplier);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'unavailable') {
                $query->where('is_unavailable', true);
            } elseif ($request->availability === 'available') {
                $query->where('is_unavailable', false);
            }
        }

        $products = $query->paginate(20);
        $categories = Category::all();
        
        // Não criar valores padrão aqui - deixar a view usar os defaults apenas para exibição

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'b2b_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
            'specifications' => 'nullable|array',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['in_stock'] = $request->stock_quantity > 0;
        $data['manage_stock'] = true;

        // Upload de imagens
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $product = Product::create($data);
        $product->categories()->attach($request->categories);

        // Log de estoque inicial
        InventoryLog::create([
            'product_id' => $product->id,
            'admin_id' => auth('admin')->id(),
            'type' => 'in',
            'quantity_before' => 0,
            'quantity_change' => $request->stock_quantity,
            'quantity_after' => $request->stock_quantity,
            'notes' => 'Estoque inicial',
            'reference' => 'Criação do produto',
        ]);

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
            'b2b_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
            'specifications' => 'nullable|array',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['in_stock'] = $request->stock_quantity > 0;

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

        return redirect()->back()
                        ->with('success', 'Estoque ajustado com sucesso!');
    }

    /**
     * Ações em massa para produtos
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_unavailable,mark_available',
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

        foreach ($existingIds as $productId) {
            $product = Product::find($productId);
            if ($product) {
                if ($action === 'mark_unavailable') {
                    $product->is_unavailable = true;
                } elseif ($action === 'mark_available') {
                    $product->is_unavailable = false;
                }
                $product->save();
                $count++;
            }
        }

        $message = $action === 'mark_unavailable' 
            ? "{$count} produto(s) marcado(s) como indisponível(is)!" 
            : "{$count} produto(s) marcado(s) como disponível(is)!";

        return redirect()->back()
                        ->with('success', $message);
    }

    /**
     * Atualizar preço de custo e recalcular B2B/B2C
     */
    public function updateCostPrice(Request $request, Product $product)
    {
        $request->validate([
            'cost_price' => 'required|numeric|min:0',
        ]);

        // Obter margens configuradas
        // B2C = 10% de margem (custo R$ 1,00 → venda R$ 1,10)
        // B2B = 20% de margem (custo R$ 1,00 → venda R$ 1,20)
        $b2cMargin = Setting::get('b2c_margin_percentage', 10);
        $b2bMargin = Setting::get('b2b_margin_percentage', 20);

        // Calcular novos preços baseados nas margens
        $costPrice = (float) $request->cost_price;
        $b2cPrice = round($costPrice * (1 + $b2cMargin / 100), 2);
        $b2bPrice = round($costPrice * (1 + $b2bMargin / 100), 2);

        // Atualizar produto
        $product->update([
            'cost_price' => $costPrice,
            'b2b_price' => $b2bPrice,
            'price' => $b2cPrice,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preços atualizados com sucesso!',
            'product' => [
                'cost_price' => number_format($costPrice, 2, ',', '.'),
                'b2b_price' => number_format($b2bPrice, 2, ',', '.'),
                'b2c_price' => number_format($b2cPrice, 2, ',', '.'),
            ]
        ]);
    }

    /**
     * Salvar configurações de margens de lucro
     */
    public function salvarMargens(Request $request)
    {
        try {
            $validated = $request->validate([
                'margem_b2c' => 'required|numeric|min:0|max:100',
                'margem_b2b' => 'required|numeric|min:0|max:100',
            ]);

            $margemB2C = (float) $validated['margem_b2c'];
            $margemB2B = (float) $validated['margem_b2b'];

            // Buscar registro B2C existente ou criar novo
            $settingB2C = Setting::where('key', 'b2c_margin_percentage')->first();
            if ($settingB2C) {
                // Se existe, FORÇAR atualização
                $settingB2C->value = (string) $margemB2C;
                $settingB2C->type = 'number';
                $settingB2C->group = 'pricing';
                $settingB2C->save();
            } else {
                // Se não existe, criar novo
                Setting::create([
                    'key' => 'b2c_margin_percentage',
                    'value' => (string) $margemB2C,
                    'type' => 'number',
                    'group' => 'pricing',
                ]);
            }

            // Buscar registro B2B existente ou criar novo
            $settingB2B = Setting::where('key', 'b2b_margin_percentage')->first();
            if ($settingB2B) {
                // Se existe, FORÇAR atualização
                $settingB2B->value = (string) $margemB2B;
                $settingB2B->type = 'number';
                $settingB2B->group = 'pricing';
                $settingB2B->save();
            } else {
                // Se não existe, criar novo
                Setting::create([
                    'key' => 'b2b_margin_percentage',
                    'value' => (string) $margemB2B,
                    'type' => 'number',
                    'group' => 'pricing',
                ]);
            }

            // Buscar novamente para confirmar que foram salvos
            $confirmB2C = Setting::where('key', 'b2c_margin_percentage')->first();
            $confirmB2B = Setting::where('key', 'b2b_margin_percentage')->first();

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Margens salvas com sucesso!',
                'margens' => [
                    'b2c' => (float) ($confirmB2C->value ?? $margemB2C),
                    'b2b' => (float) ($confirmB2B->value ?? $margemB2B),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar margens: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao salvar margens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar margens configuradas a todos os produtos
     */
    public function aplicarMargensTodos(Request $request)
    {
        try {
            $margemB2C = (float) Setting::get('b2c_margin_percentage', 10);
            $margemB2B = (float) Setting::get('b2b_margin_percentage', 20);

            $produtos = Product::whereNotNull('cost_price')
                ->where('cost_price', '>', 0)
                ->get();

            $atualizados = 0;
            foreach ($produtos as $produto) {
                $custo = (float) $produto->cost_price;
                
                if ($custo <= 0) {
                    continue;
                }
                
                $precoB2C = round($custo * (1 + $margemB2C / 100), 2);
                $precoB2B = round($custo * (1 + $margemB2B / 100), 2);

                $produto->update([
                    'price' => $precoB2C,
                    'b2b_price' => $precoB2B,
                ]);

                $atualizados++;
            }

            return response()->json([
                'sucesso' => true,
                'mensagem' => "Preços de {$atualizados} produto(s) recalculado(s) com sucesso!",
                'atualizados' => $atualizados,
                'total' => $produtos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao recalcular preços: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna as variações de um produto agrupadas por tipo
     */
    public function getVariations(Product $product)
    {
        $variations = $product->variations()->get();
        
        // Agrupar por cores
        $colors = $variations->whereNotNull('color')
            ->groupBy('color')
            ->map(function($group, $color) {
                $activeVariations = $group->where('is_active', true);
                return [
                    'name' => $color,
                    'count' => $group->count(),
                    'enabled' => $activeVariations->count() > 0
                ];
            })
            ->values();
        
        // Agrupar por RAM
        $rams = $variations->whereNotNull('ram')
            ->groupBy('ram')
            ->map(function($group, $ram) {
                $activeVariations = $group->where('is_active', true);
                return [
                    'name' => $ram,
                    'count' => $group->count(),
                    'enabled' => $activeVariations->count() > 0
                ];
            })
            ->values();
        
        // Agrupar por armazenamento
        $storages = $variations->whereNotNull('storage')
            ->groupBy('storage')
            ->map(function($group, $storage) {
                $activeVariations = $group->where('is_active', true);
                return [
                    'name' => $storage,
                    'count' => $group->count(),
                    'enabled' => $activeVariations->count() > 0
                ];
            })
            ->values();
        
        // Retornar todas as variações individuais para o estoque
        $variationsList = $variations->map(function($variation) {
            $parts = [];
            if ($variation->ram) $parts[] = $variation->ram;
            if ($variation->storage) $parts[] = $variation->storage;
            if ($variation->color) $parts[] = $variation->color;
            
            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'name' => implode(' / ', $parts) ?: $variation->sku,
                'ram' => $variation->ram,
                'storage' => $variation->storage,
                'color' => $variation->color,
                'stock_quantity' => $variation->stock_quantity,
                'in_stock' => $variation->in_stock,
                'is_active' => $variation->is_active
            ];
        });
        
        return response()->json([
            'success' => true,
            'productId' => $product->id,
            'colors' => $colors,
            'rams' => $rams,
            'storages' => $storages,
            'variations' => $variationsList
        ]);
    }

    /**
     * Habilita/desabilita variações por tipo e valor
     */
    public function toggleVariation(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:color,ram,storage',
            'value' => 'required|string',
            'enabled' => 'required|boolean'
        ]);
        
        $type = $request->type;
        $value = $request->value;
        $enabled = $request->enabled;
        
        // Atualizar todas as variações com esse tipo e valor
        $updated = $product->variations()
            ->where($type, $value)
            ->update(['is_active' => $enabled]);
        
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
            'type' => 'required|in:color,ram,storage',
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
        
        // Verificar se já existe variação com esse valor
        $existingVariations = $product->variations()->where($type, $value)->get();
        
        if ($existingVariations->count() > 0) {
            // Se já existe, apenas habilita todas
            $existingVariations->each(function($variation) {
                $variation->update(['is_active' => true]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Variações existentes foram habilitadas',
                'action' => 'enabled_existing'
            ]);
        }
        
        // Se não existe, criar novas variações combinando com as existentes
        $existingVariations = $product->variations()->get();
        
        $rams = $existingVariations->whereNotNull('ram')->pluck('ram')->unique()->values();
        $storages = $existingVariations->whereNotNull('storage')->pluck('storage')->unique()->values();
        $colors = $existingVariations->whereNotNull('color')->pluck('color')->unique()->values();
        
        // Adicionar o novo valor ao tipo correspondente
        if ($type === 'color') {
            $colors = $colors->push($value)->unique()->values();
        } elseif ($type === 'ram') {
            $rams = $rams->push($value)->unique()->values();
        } elseif ($type === 'storage') {
            $storages = $storages->push($value)->unique()->values();
        }
        
        // Se não há outras variações, criar uma variação básica
        if ($rams->isEmpty() && $storages->isEmpty() && $colors->isEmpty()) {
            $rams = collect(['']);
            $storages = collect(['']);
            $colors = collect(['']);
        }
        
        // Garantir que pelo menos um de cada tipo existe (ou vazio)
        if ($rams->isEmpty()) $rams = collect(['']);
        if ($storages->isEmpty()) $storages = collect(['']);
        if ($colors->isEmpty()) $colors = collect(['']);
        
        // Criar combinações
        $created = 0;
        foreach ($rams as $ram) {
            foreach ($storages as $storage) {
                foreach ($colors as $color) {
                    // Verificar se já existe essa combinação
                    $query = $product->variations();
                    if ($ram) $query->where('ram', $ram);
                    else $query->whereNull('ram');
                    if ($storage) $query->where('storage', $storage);
                    else $query->whereNull('storage');
                    if ($color) $query->where('color', $color);
                    else $query->whereNull('color');
                    
                    $existing = $query->first();
                    
                    if (!$existing) {
                        // Criar SKU
                        $skuParts = [$product->sku];
                        if ($ram) $skuParts[] = str_replace('GB', '', $ram);
                        if ($storage) $skuParts[] = str_replace('GB', '', $storage);
                        if ($color) $skuParts[] = strtoupper(substr($color, 0, 3));
                        $sku = implode('-', $skuParts);
                        
                        // Verificar se SKU já existe
                        while (ProductVariation::where('sku', $sku)->exists()) {
                            $sku .= '-' . rand(100, 999);
                        }
                        
                        ProductVariation::create([
                            'product_id' => $product->id,
                            'ram' => $ram ?: null,
                            'storage' => $storage ?: null,
                            'color' => $color ?: null,
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
     * Atualiza o estoque de múltiplas variações
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.variation_id' => 'required|exists:product_variations,id',
            'updates.*.stock_quantity' => 'required|integer|min:0',
            'updates.*.in_stock' => 'required|boolean'
        ]);
        
        $updated = 0;
        
        foreach ($request->updates as $update) {
            $variation = ProductVariation::find($update['variation_id']);
            
            // Verificar se a variação pertence ao produto
            if ($variation && $variation->product_id === $product->id) {
                $variation->update([
                    'stock_quantity' => $update['stock_quantity'],
                    'in_stock' => $update['in_stock']
                ]);
                $updated++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Estoque de {$updated} variação(ões) atualizado(s)",
            'updated' => $updated
        ]);
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
                'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
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
                }
            } elseif (!$removeFeatured && $request->has('existing_featured_image')) {
                // Manter imagem de destaque existente
                $existingFeatured = $this->extractImagePath($request->existing_featured_image);
                if ($existingFeatured) {
                    $imagePaths[] = $existingFeatured;
                }
            }
            
            // 2. Processar imagens adicionais existentes (que não foram marcadas para remover)
            if ($request->has('existing_additional_images') && is_array($request->existing_additional_images)) {
                foreach ($request->existing_additional_images as $image) {
                    if (empty($image)) {
                        continue;
                    }
                    
                    $extractedPath = $this->extractImagePath($image);
                    if ($extractedPath && !in_array($extractedPath, $imagePaths)) {
                        $imagePaths[] = $extractedPath;
                    }
                }
            }
            
            // 3. Adicionar novas imagens adicionais se houver upload
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('products', 'public');
                        $imagePaths[] = $path;
                    }
                }
            }
            
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
     * Extrai o caminho da imagem de uma URL ou caminho
     */
    private function extractImagePath($image)
    {
        if (empty($image)) {
            return null;
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
            }
            
            return !empty($path) ? $path : null;
        }
        
        // Já é um caminho relativo
        return $image;
    }
}
