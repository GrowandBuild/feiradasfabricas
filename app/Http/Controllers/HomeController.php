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

        // Filtro por marca
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

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
        $brands = Product::active()
            ->available() // Apenas marcas de produtos disponíveis
            ->distinct()
            ->pluck('brand')
            ->filter()
            ->sort();

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

        return view('products.show', compact('product', 'relatedProducts'));
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
