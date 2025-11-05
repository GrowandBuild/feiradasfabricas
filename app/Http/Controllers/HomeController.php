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
                $query->active()->featured()->take(4);
            }])
            ->get();

        return view('home', compact('departments'));
    }

    /**
     * Exibe a página de produtos
     */
    public function products(Request $request)
    {
        $query = Product::active()->inStock()->with(['categories']);

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
        $brands = Product::active()->distinct()->pluck('brand')->filter()->sort();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Exibe detalhes de um produto
     */
    public function product($slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['categories'])
            ->firstOrFail();

        // Produtos relacionados (mesma categoria)
        $relatedProducts = Product::active()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
