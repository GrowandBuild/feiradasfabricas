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
            ->with(['categories', 'variations.attributeValues.attribute'])
            ->firstOrFail();

        // Carregar atributos se produto tem variações
        // SIMPLIFICADO: Sempre retornar Eloquent Collection ou null
        $attributes = null;
        if ($product->has_variations && $product->variations()->count() > 0) {
            try {
                $attributes = $product->attributeSets();
                // Se não for Eloquent Collection válida, não passar nada
                if (!$attributes instanceof \Illuminate\Database\Eloquent\Collection || $attributes->count() === 0) {
                    $attributes = null;
                }
            } catch (\Exception $e) {
                // Em caso de erro, não passar atributos (componente não será renderizado)
                \Log::warning("Erro ao carregar atributos do produto {$product->id}: " . $e->getMessage());
                $attributes = null;
            }
        }

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

        return view('products.show', compact('product', 'relatedProducts', 'attributes'))
            ->with('currentDepartmentSlug', $currentDepartment);
    }

}
