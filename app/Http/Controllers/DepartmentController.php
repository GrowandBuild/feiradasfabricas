<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\Category;
use App\Models\DepartmentBadge;
use App\Helpers\BannerHelper;

class DepartmentController extends Controller
{
    /**
     * Exibe a página inicial de um departamento
     */
    public function index(Request $request, $slug)
    {
        $department = Department::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        // Produtos em destaque do departamento
        $featuredProducts = $department->products()
            ->active()
            ->featured()
            ->take(12)
            ->get();

        // Categorias do departamento
        $categories = $department->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        // Produtos por marca (se for eletrônicos)
        $brandProducts = collect();
        if ($department->slug === 'eletronicos') {
            $brands = ['Apple', 'Samsung', 'Xiaomi', 'Motorola', 'Infinix', 'JBL', 'Oppo', 'Realme', 'Tecno'];
            foreach ($brands as $brand) {
                $products = $department->products()
                    ->active()
                    ->where('brand', $brand)
                    ->take(4)
                    ->get();
                if ($products->count() > 0) {
                    $brandProducts->put(strtolower($brand), $products);
                }
            }
        }

        // Banners do departamento
        $heroBanners = BannerHelper::getBannersForDisplay($department->id, 'hero', 5);

        // Selos de marcas do departamento
        $departmentBadges = DepartmentBadge::where('department_id', $department->id)
            ->active()
            ->ordered()
            ->get();

        // Para eletrônicos, usar a view específica com todos os produtos por marca
        if ($department->slug === 'eletronicos') {
            // Buscar produtos por marca para eletrônicos
            $appleProducts = $department->products()
                ->active()
                ->where('brand', 'Apple')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $samsungProducts = $department->products()
                ->active()
                ->where('brand', 'Samsung')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $xiaomiProducts = $department->products()
                ->active()
                ->where('brand', 'Xiaomi')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $motorolaProducts = $department->products()
                ->active()
                ->where('brand', 'Motorola')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $infinixProducts = $department->products()
                ->active()
                ->where('brand', 'Infinix')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $jblProducts = $department->products()
                ->active()
                ->where('brand', 'JBL')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $oppoProducts = $department->products()
                ->active()
                ->where('brand', 'Oppo')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $realmeProducts = $department->products()
                ->active()
                ->where('brand', 'Realme')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            $tecnoProducts = $department->products()
                ->active()
                ->where('brand', 'Tecno')
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('sort_order', 'desc')
                ->orderBy('name')
                ->get();

            // Produtos mais recentes
            $latestProducts = $department->products()
                ->active()
                ->inStock()
                ->with(['categories'])
                ->latest()
                ->take(8)
                ->get();

            // Banners específicos do departamento (já carregados acima)
            // $heroBanners já contém os banners específicos do departamento

            return view('department.eletronicos', compact(
                'department',
                'featuredProducts',
                'appleProducts',
                'samsungProducts',
                'xiaomiProducts',
                'motorolaProducts',
                'infinixProducts',
                'jblProducts',
                'oppoProducts',
                'realmeProducts',
                'tecnoProducts',
                'categories',
                'latestProducts',
                'heroBanners',
                'departmentBadges'
            ));
        }

        // Verificar se existe uma view específica para o departamento
        $specificView = "department.{$slug}";
        if (view()->exists($specificView)) {
            return view($specificView, compact(
                'department',
                'featuredProducts',
                'categories',
                'brandProducts',
                'heroBanners',
                'departmentBadges'
            ));
        }

        // Usar view padrão se não existir view específica
        return view('department.index', compact(
            'department',
            'featuredProducts',
            'categories',
            'brandProducts',
            'heroBanners',
            'departmentBadges'
        ));
    }

    /**
     * Lista todos os departamentos
     */
    public function list()
    {
        $departments = Department::active()->ordered()->get();
        
        return view('department.list', compact('departments'));
    }
}
