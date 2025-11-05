<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
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

        $products = $query->paginate(20);
        $categories = Category::all();

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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        // Gerenciar imagens
        $imagePaths = [];
        
        // Debug: verificar dados recebidos
        \Log::info('Dados de imagens recebidos:', [
            'has_images' => $request->hasFile('images'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'has_existing_images' => $request->has('existing_images'),
            'existing_images' => $request->existing_images ?? [],
            'all_images_removed' => $request->all_images_removed ?? '0'
        ]);
        
        // Verificar se todas as imagens foram removidas
        if ($request->has('all_images_removed') && $request->all_images_removed == '1') {
            $imagePaths = [];
            \Log::info('Todas as imagens foram removidas');
        } else {
            // Adicionar imagens existentes que não foram removidas
            if ($request->has('existing_images')) {
                $imagePaths = $request->existing_images;
                \Log::info('Imagens existentes mantidas:', ['images' => $imagePaths]);
            }
        }
        
        // Adicionar novas imagens se houver
        if ($request->hasFile('images')) {
            \Log::info('Processando novas imagens...');
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
                \Log::info('Nova imagem salva:', ['path' => $path]);
            }
        }
        
        $data['images'] = $imagePaths;
        \Log::info('Array final de imagens:', ['images' => $imagePaths]);

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
}
