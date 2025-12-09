<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use App\Models\InventoryLog;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
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

    public function create()
    {
        $categories = Category::all();
        $departments = Department::all();
        return view('admin.products.create', compact('categories', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $uploaded = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                if ($path) $uploaded[] = $path;
            }
        }
        if (!empty($uploaded)) $data['images'] = $uploaded;

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

    public function edit(Product $product)
    {
        $categories = Category::all();
        $departments = Department::all();
        $productCategories = $product->categories->pluck('id')->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'productCategories', 'departments'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $images = [];
        if ($request->has('existing_images') && is_array($request->existing_images)) {
            $images = array_filter($request->existing_images);
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $p = $image->store('products', 'public'); if ($p) $images[] = $p;
            }
        }
        // Ensure images order respects primary_image if provided
        if (!empty($images)) {
            $primary = $request->input('primary_image');
            if ($primary) {
                // normalize and move primary to first position if exists
                $images = array_values(array_filter($images));
                $pos = array_search($primary, $images);
                if ($pos !== false) {
                    array_splice($images, $pos, 1);
                    array_unshift($images, $primary);
                }
            }
        }
        $data['images'] = $images;
        // store explicit primary field if provided (useful if DB has primary_image column)
        if ($request->has('primary_image')) {
            $data['primary_image'] = $request->input('primary_image');
        }

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

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Retorna variações do produto para JSON (AJAX)
     */
    public function getVariations(Product $product)
    {
        $variations = $product->variations()->withTrashed()->get();
        
        // Agrupar atributos para o frontend
        $attributeGroups = [];
        $variationsData = [];
        
        foreach ($variations as $variation) {
            $variationsData[] = [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'ram' => $variation->ram,
                'storage' => $variation->storage,
                'color' => $variation->color,
                'color_hex' => $variation->color_hex,
                'price' => $variation->price,
                'b2b_price' => $variation->b2b_price,
                'cost_price' => $variation->cost_price,
                'stock_quantity' => $variation->stock_quantity,
                'in_stock' => $variation->in_stock,
                'is_active' => $variation->is_active,
                'sort_order' => $variation->sort_order,
                'created_at' => $variation->created_at,
                'updated_at' => $variation->updated_at,
                'deleted_at' => $variation->deleted_at,
            ];
            
            // Agrupar valores por atributo
            foreach (['ram', 'storage', 'color'] as $attr) {
                if (!empty($variation->{$attr})) {
                    if (!isset($attributeGroups[$attr])) {
                        $attributeGroups[$attr] = [];
                    }
                    
                    $value = $variation->{$attr};
                    $key = $value;
                    
                    if (!isset($attributeGroups[$attr][$key])) {
                        $attributeGroups[$attr][$key] = [
                            'name' => $value,
                            'count' => 0,
                            'enabled' => true
                        ];
                        
                        if ($attr === 'color' && $variation->color_hex) {
                            $attributeGroups[$attr][$key]['hex'] = $variation->color_hex;
                        }
                    }
                    
                    $attributeGroups[$attr][$key]['count']++;
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'variations' => $variationsData,
            'attribute_groups' => $attributeGroups,
            'total' => count($variationsData),
            'active' => $variations->where('is_active', true)->count()
        ]);
    }

    /**
     * Adiciona única variação (AJAX)
     */
    public function addVariation(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => 'required|string|in:ram,storage,color',
            'value' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);
        
        $variationData = [
            $data['type'] => $data['value'],
            'price' => $data['price'] ?? $product->price,
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'is_active' => true,
        ];
        
        // Gerar SKU automático
        $variationData['sku'] = $this->generateVariationSku($product, $variationData);
        
        $variation = $product->variations()->create($variationData);
        
        return response()->json([
            'success' => true,
            'variation' => $variation,
            'message' => 'Variação adicionada com sucesso'
        ]);
    }

    /**
     * Adiciona múltiplas variações (JSON/bulk)
     */
    public function bulkAddVariations(Request $request, Product $product)
    {
        try {
            $data = $request->validate([
                'combinations' => 'required|array|min:1',
                'combinations.*' => 'array',
            ]);
            
            $generator = new \App\Services\VariationGenerator();
            $results = $generator->generate($product, $data['combinations']);
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => count($results) . ' variações processadas'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in bulkAddVariations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar variações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle ativação de variação
     */
    public function toggleVariation(Request $request, Product $product)
    {
        $request->validate([
            'variation_id' => 'required|exists:product_variations,id'
        ]);
        
        $variation = $product->variations()->findOrFail($request->variation_id);
        $variation->is_active = !$variation->is_active;
        $variation->save();
        
        return response()->json([
            'success' => true,
            'is_active' => $variation->is_active,
            'message' => $variation->is_active ? 'Variação ativada' : 'Variação desativada'
        ]);
    }

    /**
     * Atualiza estoque de variações em massa
     */
    public function updateStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'updates' => 'required|array|min:1',
            'updates.*.variation_id' => 'required|exists:product_variations,id',
            'updates.*.stock_quantity' => 'required|integer|min:0',
        ]);
        
        $updated = 0;
        foreach ($data['updates'] as $update) {
            $variation = $product->variations()->find($update['variation_id']);
            if ($variation) {
                $variation->stock_quantity = $update['stock_quantity'];
                $variation->in_stock = $update['stock_quantity'] > 0;
                $variation->save();
                $updated++;
            }
        }
        
        return response()->json([
            'success' => true,
            'updated' => $updated,
            'message' => $updated . ' estoques atualizados'
        ]);
    }

    /**
     * Atualiza imagens por cor
     */
    public function updateColorImages(Request $request, Product $product)
    {
        $data = $request->validate([
            'color_images' => 'required|array',
            'color_images.*.color' => 'required|string',
            'color_images.*.images' => 'required|array',
        ]);
        
        $product->variation_images = $data['color_images'];
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Imagens por cor atualizadas'
        ]);
    }

    /**
     * Atualiza cor HEX
     */
    public function updateColorHex(Request $request, Product $product)
    {
        $data = $request->validate([
            'color' => 'required|string',
            'hex' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);
        
        $product->variations()
            ->where('color', $data['color'])
            ->update(['color_hex' => $data['hex']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Cor HEX atualizada'
        ]);
    }

    /**
     * Atualiza preço de variação específica
     */
    public function updateVariationPrice(Request $request, ProductVariation $variation)
    {
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:price,b2b_price,cost_price',
        ]);
        
        $variation->{$data['price_type']} = $data['price'];
        $variation->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Preço atualizado'
        ]);
    }

    /**
     * Exclui valor de atributo
     */
    public function deleteVariationValue(Request $request, Product $product)
    {
        $data = $request->validate([
            'attribute' => 'required|string|in:ram,storage,color',
            'value' => 'required|string',
        ]);
        
        $deleted = $product->variations()
            ->where($data['attribute'], $data['value'])
            ->delete();
        
        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => $deleted . ' variações excluídas'
        ]);
    }

    /**
     * Exclui todas as variações inativas
     */
    public function deleteInactiveVariations(Product $product)
    {
        $deleted = $product->variations()
            ->where('is_active', false)
            ->delete();
        
        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => $deleted . ' variações inativas excluídas'
        ]);
    }

    private function generateVariationSku(Product $product, array $data): string
    {
        $parts = [$product->id];
        foreach (['ram', 'storage', 'color'] as $field) {
            if (!empty($data[$field])) {
                $parts[] = \Illuminate\Support\Str::slug($data[$field]);
            }
        }
        
        $base = implode('-', $parts);
        $sku = strtoupper($base);
        
        $counter = 1;
        $candidate = $sku;
        while (\App\Models\ProductVariation::where('sku', $candidate)->exists()) {
            $candidate = $sku . '-' . $counter++;
        }
        
        return $candidate;
    }

    /**
     * Atualiza um campo específico de uma variação (AJAX)
     */
    public function updateVariationField(Request $request, Product $product, ProductVariation $variation)
    {
        // Garante que a variação pertence ao produto
        if ($variation->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Variação não encontrada para este produto'], 404);
        }

        $field = $request->input('field');
        $value = $request->input('value');

        if (!in_array($field, ['sku', 'price', 'stock_quantity', 'is_active'])) {
            return response()->json(['success' => false, 'message' => 'Campo inválido'], 422);
        }

        // Validações básicas por campo
        if ($field === 'sku') {
            $value = trim($value);
            if (empty($value)) $value = null;
        } elseif ($field === 'price') {
            $value = is_numeric($value) ? (float) $value : 0;
        } elseif ($field === 'stock_quantity') {
            $value = is_numeric($value) ? (int) $value : 0;
        } elseif ($field === 'is_active') {
            $value = (int) $value;
        }

        $variation->{$field} = $value;
        $saved = $variation->save();

        if ($saved) {
            return response()->json(['success' => true, 'message' => 'Campo atualizado com sucesso']);
        } else {
            return response()->json(['success' => false, 'message' => 'Erro ao salvar campo'], 500);
        }
    }

    /**
     * Exclui uma variação (AJAX)
     */
    public function deleteVariation(Product $product, ProductVariation $variation)
    {
        // Garante que a variação pertence ao produto
        if ($variation->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Variação não encontrada para este produto'], 404);
        }

        $deleted = $variation->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Variação excluída com sucesso']);
        } else {
            return response()->json(['success' => false, 'message' => 'Erro ao excluir variação'], 500);
        }
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
