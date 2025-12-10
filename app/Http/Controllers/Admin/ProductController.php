<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\InventoryLog;
use App\Models\AlbumImage;
use App\Services\VariationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $variationService;

    public function __construct(VariationService $variationService)
    {
        $this->variationService = $variationService;
    }
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
        $departments = Department::all();
        
        // Processar imagens selecionadas do álbum
        $preselectedImages = [];
        if ($request->has('image_ids')) {
            $imageIds = explode(',', $request->image_ids);
            $albumImages = AlbumImage::whereIn('id', $imageIds)->get();
            
            foreach ($albumImages as $albumImage) {
                $preselectedImages[] = [
                    'id' => $albumImage->id,
                    'url' => $albumImage->url,
                    'path' => $albumImage->path,
                    'alt' => $albumImage->alt ?? 'Imagem do álbum'
                ];
            }
        }
        
        return view('admin.products.create', compact('categories', 'departments', 'preselectedImages'));
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
        
        // CORRIGIDO: Garantir que o slug seja único
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        
        // Verificar se slug já existe e gerar um único
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $data['slug'] = $slug;

        $uploaded = [];
        
        // Processar imagens enviadas via upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                if ($path) $uploaded[] = $path;
            }
        }
        
        // Processar imagens selecionadas do álbum
        if ($request->has('existing_image_ids') && is_array($request->existing_image_ids)) {
            foreach ($request->existing_image_ids as $albumImageId) {
                $albumImage = AlbumImage::find($albumImageId);
                if ($albumImage) {
                    // Copiar imagem do álbum para a pasta de produtos
                    $sourcePath = $albumImage->path;
                    $destinationPath = 'products/' . basename($sourcePath);
                    
                    // Se o arquivo existe, copiar
                    if (Storage::disk('public')->exists($sourcePath)) {
                        $copied = Storage::disk('public')->copy($sourcePath, $destinationPath);
                        if ($copied) {
                            $uploaded[] = $destinationPath;
                        }
                    } else {
                        // Se não existe localmente, pode ser URL externa - usar diretamente
                        if (strpos($sourcePath, 'http') === 0) {
                            $uploaded[] = $sourcePath;
                        }
                    }
                }
            }
        }
        
        if (!empty($uploaded)) {
            $data['images'] = $uploaded;
        }

        $product = DB::transaction(function() use ($data, $request) {
            $prod = Product::create($data);
            if ($request->has('categories')) {
                $prod->categories()->sync($request->categories);
            }
            InventoryLog::create([
                'product_id' => $prod->id,
                'admin_id' => auth('admin')->id(),
                'type' => 'in', // CORRIGIDO: 'initial' não existe no enum, usar 'in' (entrada)
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
        
        // Carregar variações e atributos
        $product->load(['variations.attributeValues.attribute']);
        $variations = $product->variations ?? collect();
        $attributes = $this->variationService->getGlobalAttributes();
        
        return view('admin.products.edit', compact('product', 'categories', 'productCategories', 'departments', 'variations', 'attributes'));
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
        
        // CORRIGIDO: Garantir que o slug seja único ao atualizar
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        
        // Verificar se slug já existe (exceto para o produto atual)
        while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $data['slug'] = $slug;

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

    /**
     * Criar nova variação
     */
    public function createVariation(Request $request, Product $product)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'b2b_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:product_variations,sku',
            'name' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:attribute_values,id'
        ]);

        try {
            $variation = $this->variationService->createVariation(
                $product,
                [
                    'price' => $request->price,
                    'b2b_price' => $request->b2b_price,
                    'stock_quantity' => $request->stock_quantity,
                    'in_stock' => $request->stock_quantity > 0,
                    'sku' => $request->sku,
                    'name' => $request->name,
                    'is_default' => $request->is_default ?? false
                ],
                $request->attribute_values
            );

            return response()->json([
                'success' => true,
                'message' => 'Variação criada com sucesso!',
                'variation' => $variation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar variação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar variação
     */
    public function updateVariation(Request $request, ProductVariation $variation)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'b2b_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:product_variations,sku,' . $variation->id,
            'name' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'attribute_values' => 'nullable|array|min:1',
            'attribute_values.*' => 'exists:attribute_values,id'
        ]);

        try {
            // Preparar dados para atualização
            $updateData = [
                'price' => $request->price,
                'b2b_price' => $request->b2b_price,
                'stock_quantity' => $request->stock_quantity,
                'in_stock' => $request->stock_quantity > 0,
                'is_default' => $request->is_default ?? false
            ];
            
            // Só incluir SKU e nome se forem fornecidos (não null)
            // Isso evita sobrescrever valores existentes quando apenas o estoque é atualizado
            if ($request->has('sku') && $request->sku !== null) {
                $updateData['sku'] = $request->sku;
            }
            
            if ($request->has('name') && $request->name !== null) {
                $updateData['name'] = $request->name;
            }
            
            $variation = $this->variationService->updateVariation(
                $variation,
                $updateData,
                $request->attribute_values ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Variação atualizada com sucesso!',
                'variation' => $variation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar variação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar variação específica
     */
    public function getVariation(ProductVariation $variation)
    {
        $variation->load('attributeValues.attribute');
        
        // Preparar imagens
        $images = $variation->images ?? [];
        $formattedImages = array_map(function($img) {
            return [
                'path' => $img,
                'url' => strpos($img, 'http') === 0 ? $img : asset('storage/' . $img)
            ];
        }, $images);
        
        return response()->json([
            'success' => true,
            'variation' => [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'name' => $variation->name,
                'price' => $variation->price,
                'b2b_price' => $variation->b2b_price,
                'stock_quantity' => $variation->stock_quantity,
                'is_default' => $variation->is_default,
                'images' => $formattedImages,
                'first_image' => $variation->first_image,
                'attribute_values' => $variation->attributeValues->map(function($av) {
                    return [
                        'attribute_id' => $av->attribute->id,
                        'attribute_value_id' => $av->id,
                        'value' => $av->value,
                        'display_value' => $av->display_value
                    ];
                })
            ]
        ]);
    }

    /**
     * Excluir variação
     */
    public function destroyVariation(ProductVariation $variation)
    {
        try {
            $variation->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Variação excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir variação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de imagem para variação
     */
    public function uploadVariationImage(Request $request, ProductVariation $variation)
    {
        // Validar: ou image (upload) ou album_image_id (do álbum)
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);
        } elseif ($request->has('album_image_id')) {
            $request->validate([
                'album_image_id' => 'required|exists:album_images,id'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Envie uma imagem ou selecione uma do álbum'
            ], 422);
        }

        try {
            $path = null;
            
            // Se é upload de arquivo
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products/variations', 'public');
            } 
            // Se é imagem do álbum
            elseif ($request->has('album_image_id')) {
                $albumImage = AlbumImage::find($request->album_image_id);
                if ($albumImage) {
                    // Copiar imagem do álbum para a pasta de variações
                    $sourcePath = $albumImage->path;
                    $destinationPath = 'products/variations/' . basename($sourcePath);
                    
                    // Se o arquivo existe, copiar
                    if (Storage::disk('public')->exists($sourcePath)) {
                        $copied = Storage::disk('public')->copy($sourcePath, $destinationPath);
                        if ($copied) {
                            $path = $destinationPath;
                        }
                    } else {
                        // Se não existe localmente, pode ser URL externa - usar diretamente
                        if (strpos($sourcePath, 'http') === 0) {
                            $path = $sourcePath;
                        }
                    }
                }
            }
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar imagem'
                ], 500);
            }
            
            $images = $variation->images ?? [];
            $images[] = $path;
            $variation->update(['images' => $images]);

            return response()->json([
                'success' => true,
                'message' => 'Imagem adicionada com sucesso!',
                'image' => [
                    'path' => $path,
                    'url' => asset('storage/' . $path)
                ],
                'images' => array_map(function($img) {
                    return [
                        'path' => $img,
                        'url' => asset('storage/' . $img)
                    ];
                }, $images)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover imagem de variação
     */
    public function removeVariationImage(Request $request, ProductVariation $variation)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        try {
            $images = $variation->images ?? [];
            $imagePath = $request->image_path;
            
            // Remover da lista
            $images = array_filter($images, function($img) use ($imagePath) {
                return $img !== $imagePath;
            });
            $images = array_values($images); // Reindexar
            
            $variation->update(['images' => $images]);

            // Tentar remover arquivo físico
            $fullPath = storage_path('app/public/' . $imagePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso!',
                'images' => array_map(function($img) {
                    return [
                        'path' => $img,
                        'url' => asset('storage/' . $img)
                    ];
                }, $images)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter imagens da variação
     */
    public function getVariationImages(ProductVariation $variation)
    {
        $images = $variation->images ?? [];
        
        return response()->json([
            'success' => true,
            'images' => array_map(function($img) {
                return [
                    'path' => $img,
                    'url' => strpos($img, 'http') === 0 ? $img : asset('storage/' . $img)
                ];
            }, $images),
            'product_images' => $variation->product ? array_map(function($img) {
                return [
                    'path' => $img,
                    'url' => strpos($img, 'http') === 0 ? $img : asset('storage/' . $img)
                ];
            }, $variation->product->images ?? []) : []
        ]);
    }

    /**
     * Definir imagem principal da variação
     */
    public function setVariationPrimaryImage(Request $request, ProductVariation $variation)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        try {
            $images = $variation->images ?? [];
            $imagePath = $request->image_path;
            
            // Verificar se a imagem existe na lista
            if (!in_array($imagePath, $images)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagem não encontrada na variação'
                ], 404);
            }
            
            // Mover para primeira posição
            $images = array_filter($images, function($img) use ($imagePath) {
                return $img !== $imagePath;
            });
            array_unshift($images, $imagePath);
            $images = array_values($images);
            
            $variation->update(['images' => $images]);

            return response()->json([
                'success' => true,
                'message' => 'Imagem principal definida!',
                'images' => array_map(function($img) {
                    return [
                        'path' => $img,
                        'url' => asset('storage/' . $img)
                    ];
                }, $images)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar imagens de álbuns para usar em produtos/variações
     */
    public function getAlbumImages(Request $request)
    {
        $albums = \App\Models\Album::with('images')->get();
        
        $formattedAlbums = $albums->map(function($album) {
            return [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'images' => $album->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                        'path' => $image->path,
                        'alt' => $image->alt ?? ''
                    ];
                })
            ];
        });
        
        return response()->json([
            'success' => true,
            'albums' => $formattedAlbums
        ]);
    }
}
