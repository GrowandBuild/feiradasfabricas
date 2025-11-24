<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial - Welcome geral
     */
    public function index()
    {
        // Buscar todos os departamentos ativos para mostrar na página inicial
        $departments = \App\Models\Department::active()
            ->ordered()
            ->with(['products' => function($query) {
                $query->active()
                    ->featured()
                    ->orderBy('is_unavailable', 'asc') // Disponíveis primeiro
                    ->take(4);
            }])
            ->get();

        return view('home', compact('departments'));
    }

    /**
     * Exibe a página de produtos
     */
    public function products(Request $request)
    {
        // Apenas produtos disponíveis
        $query = Product::active()
            ->available() // Apenas produtos disponíveis
            ->with(['categories']);

        // Filtro por categoria
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtro por marca (REMOVED): prevent any brand-specific DB queries
        // if ($request->filled('brand')) {
        //     $query->where('brand', $request->brand);
        // }

        // Busca por nome
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // Sempre priorizar produtos disponíveis primeiro
        $query->orderBy('is_unavailable', 'asc') // Disponíveis primeiro
              ->orderBy('in_stock', 'desc')
              ->orderBy('is_active', 'desc');
        
        // Ordenação
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'name':
                $query->orderBy('name', $sortDirection);
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $products = $query->paginate(12);
        $categories = Category::active()->ordered()->get();
        // Brands removed: provide empty collection for compatibility with views
        $brands = collect();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Exibe detalhes de um produto
     */
    public function product($slug)
    {
        // Produtos indisponíveis também podem ser visualizados
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['categories', 'activeVariations'])
            ->firstOrFail();

        // Produtos relacionados (mesma categoria) - apenas disponíveis
        $relatedProducts = Product::active()
            ->available() // Apenas produtos disponíveis
            ->inStock()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->orderBy('is_unavailable', 'asc') // Disponíveis primeiro
            ->take(4)
            ->get();

        // If the product page was reached from a department, forward that slug so the layout
        // can pick up department-specific theme settings on server render.
        $currentDepartment = request()->get('department');

        return view('products.show', compact('product', 'relatedProducts'))
            ->with('currentDepartmentSlug', $currentDepartment);
    }

    /**
     * Página indexável de uma variação específica (/produto/{slug}/{variantSlug})
     */
    public function productVariant($slug, $variantSlug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['categories', 'activeVariations'])
            ->firstOrFail();

        // Encontrar variação pela slug gerada previamente
        $variation = $product->activeVariations->first(function($v) use ($variantSlug) {
            return $v->slug === $variantSlug;
        });

        if (!$variation) {
            abort(404);
        }

        // Preparar título e meta dinâmicos
        $variantTitleParts = [$product->name];
        if ($variation->color) $variantTitleParts[] = $variation->color;
        if ($variation->storage) $variantTitleParts[] = $variation->storage;
        if ($variation->ram) $variantTitleParts[] = $variation->ram;
        $pageTitle = implode(' ', $variantTitleParts);

        // Selecionar imagens por cor se existir
        $variantImages = [];
        $variationImagesMap = $product->variation_images_urls ?? [];
        if ($variation->color && isset($variationImagesMap[$variation->color]) && count($variationImagesMap[$variation->color]) > 0) {
            $variantImages = $variationImagesMap[$variation->color];
        } else {
            $variantImages = $product->all_images;
        }

        // Metadados SEO
        $metaDescription = sprintf(
            '%s disponível na cor %s, armazenamento %s%s. Compre com garantia e entrega rápida.',
            $product->name,
            $variation->color ?? '—',
            $variation->storage ?? '—',
            $variation->ram ? ' e RAM '.$variation->ram : ''
        );

        // JSON-LD Schema Product + Offer
        $schemaProduct = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $pageTitle,
            'image' => $variantImages,
            'sku' => $variation->sku ?? $product->sku,
            // 'brand' removed from schema per request
            'description' => $metaDescription,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BRL',
                'price' => (string) ($variation->price ?: $product->price),
                'availability' => $variation->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => url()->current(),
            ],
            'color' => $variation->color,
            'additionalProperty' => array_values(array_filter([
                $variation->storage ? ['@type' => 'PropertyValue','name' => 'Armazenamento','value' => $variation->storage] : null,
                $variation->ram ? ['@type' => 'PropertyValue','name' => 'RAM','value' => $variation->ram] : null,
            ])),
        ];

        // Produtos relacionados (mesma categoria) - manter
        $relatedProducts = Product::active()
            ->available()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->orderBy('is_unavailable', 'asc')
            ->take(4)
            ->get();

        $currentDepartment = request()->get('department');

        return view('products.variant', [
            'product' => $product,
            'variation' => $variation,
            'variantImages' => $variantImages,
            'pageTitle' => $pageTitle,
            'metaDescription' => $metaDescription,
            'schemaProduct' => $schemaProduct,
            'relatedProducts' => $relatedProducts,
        ])->with('currentDepartmentSlug', $currentDepartment);
    }

    /**
     * Buscar variação de produto por RAM e armazenamento (AJAX)
     */
    public function getProductVariation(Request $request, $slug)
    {
        $request->validate([
            'ram' => 'nullable|string',
            'storage' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $product = Product::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $query = $product->activeVariations();

        if ($request->ram) {
            $query->where('ram', $request->ram);
        }

        if ($request->storage) {
            $query->where('storage', $request->storage);
        }

        if ($request->color) {
            $query->where('color', $request->color);
        }

        $variation = $query->first();

        if (!$variation) {
            return response()->json([
                'success' => false,
                'message' => 'Variação não encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'variation' => [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => number_format($variation->price, 2, ',', '.'),
                'b2b_price' => $variation->b2b_price ? number_format($variation->b2b_price, 2, ',', '.') : null,
                'stock_quantity' => $variation->stock_quantity,
                'in_stock' => $variation->in_stock,
                'ram' => $variation->ram,
                'storage' => $variation->storage,
                'color' => $variation->color,
                'color_hex' => $variation->color_hex,
            ],
        ]);
    }
}
