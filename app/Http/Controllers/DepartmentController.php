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

    // Compartilhar contexto para layouts (tema por departamento, etc.)
    view()->share('currentDepartmentSlug', $department->slug);
    view()->share('currentDepartmentName', $department->name);
        // Persistir slug e tema do departamento na sessão para que páginas
        // subsequentes (álbuns, produtos, etc.) respeitem as cores do dept
        try {
            session(['current_department_slug' => $department->slug]);
            $theme = [
                'theme_primary' => setting('dept_' . $department->slug . '_theme_primary', setting('theme_primary')),
                'theme_secondary' => setting('dept_' . $department->slug . '_theme_secondary', setting('theme_secondary')),
                'theme_accent' => setting('dept_' . $department->slug . '_theme_accent', setting('theme_accent')),
                'theme_dark_bg' => setting('dept_' . $department->slug . '_theme_dark_bg', setting('theme_dark_bg')),
                'theme_text_light' => setting('dept_' . $department->slug . '_theme_text_light', setting('theme_text_light')),
                'theme_text_dark' => setting('dept_' . $department->slug . '_theme_text_dark', setting('theme_text_dark')),
            ];
            session(['current_department_theme' => $theme]);
        } catch (\Exception $e) {
            // se session estiver indisponível por algum motivo, apenas continue
        }
        
        // Produtos em destaque do departamento - priorizar disponíveis
        $featuredProducts = $department->products()
            ->active()
            ->featured()
            ->orderBy('is_unavailable', 'asc') // Disponíveis primeiro
            ->orderBy('is_featured', 'desc')
            ->take(12)
            ->get();

        // Categorias do departamento
        $categories = $department->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        // Produtos por marca (REMOVED): prevent any brand-specific DB queries.
        // Keep an empty collection for compatibility with views that expect `brandProducts`.
        $brandProducts = collect();

        // Banners do departamento
        $heroBanners = BannerHelper::getBannersForDisplay($department->id, 'hero', 5);

        // Selos de marcas do departamento
        $departmentBadges = DepartmentBadge::where('department_id', $department->id)
            ->active()
            ->ordered()
            ->get();

        // Para eletrônicos: evitar carregar produtos por marca (REMOVED).
        // Criar placeholders vazios para compatibilidade com as views.
        if ($department->slug === 'eletronicos') {
            $appleProducts = collect();
            $samsungProducts = collect();
            $xiaomiProducts = collect();
            $motorolaProducts = collect();
            $infinixProducts = collect();
            $jblProducts = collect();
            $oppoProducts = collect();
            $realmeProducts = collect();
            $tecnoProducts = collect();

            // Produtos mais recentes - manter consulta genérica (não é diretamente por marca)
            $latestProducts = $department->products()
                ->active()
                ->inStock()
                ->with(['categories'])
                ->orderBy('is_unavailable', 'asc') // Disponíveis primeiro
                ->latest()
                ->take(8)
                ->get();

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
