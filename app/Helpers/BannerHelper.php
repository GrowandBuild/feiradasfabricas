<?php

namespace App\Helpers;

use App\Models\Banner;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;

class BannerHelper
{
    /**
     * Obter banners ativos para um departamento específico
     */
    public static function getBannersForDepartment($departmentId, $position = null, $limit = null)
    {
        $query = Banner::active()
            ->forDepartment($departmentId)
            ->orderBy('sort_order');

        if ($position) {
            $query->byPosition($position);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Obter banners globais (sem departamento específico)
     */
    public static function getGlobalBanners($position = null, $limit = null)
    {
        $query = Banner::active()
            ->global()
            ->orderBy('sort_order');

        if ($position) {
            $query->byPosition($position);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Obter banners para exibição (departamento específico + globais)
     */
    public static function getBannersForDisplay($departmentId, $position = null, $limit = null)
    {
        // Buscar banners específicos do departamento
        $departmentBanners = self::getBannersForDepartment($departmentId, $position, $limit);
        
        // Se não há limite ou não atingiu o limite, buscar banners globais
        if (!$limit || $departmentBanners->count() < $limit) {
            $remainingLimit = $limit ? $limit - $departmentBanners->count() : null;
            $globalBanners = self::getGlobalBanners($position, $remainingLimit);
            
            return $departmentBanners->merge($globalBanners);
        }

        return $departmentBanners;
    }

    /**
     * Obter banners para a home (departamento padrão ou global)
     */
    public static function getBannersForHome($position = null, $limit = null)
    {
        return self::getGlobalBanners($position, $limit);
    }

    /**
     * Obter banners para página de departamento
     */
    public static function getBannersForDepartmentPage($departmentSlug, $position = null, $limit = null)
    {
        $department = Department::where('slug', $departmentSlug)->first();
        
        if (!$department) {
            return self::getGlobalBanners($position, $limit);
        }

        return self::getBannersForDisplay($department->id, $position, $limit);
    }

    /**
     * Verificar se um banner está ativo
     */
    public static function isBannerActive($banner)
    {
        return $banner->isActive();
    }

    /**
     * Obter URL da imagem do banner
     */
    public static function getBannerImageUrl($banner, $mobile = false)
    {
        $imagePath = $mobile && $banner->mobile_image ? $banner->mobile_image : $banner->image;
        
        if (!$imagePath) {
            return null;
        }

        // Normalize common stored values to the storage disk relative path
        // Some records may contain "storage/filename" or "/storage/filename" or leading slashes
        // We want paths relative to storage/app/public, e.g. "banners/xxx.jpg"
        if (str_starts_with($imagePath, '/')) {
            $imagePath = ltrim($imagePath, '/');
        }

        if (str_starts_with($imagePath, 'storage/')) {
            $imagePath = substr($imagePath, strlen('storage/'));
        }

        if (str_starts_with($imagePath, 'public/')) {
            $imagePath = substr($imagePath, strlen('public/'));
        }

        // Se for uma URL externa (começa com http), retorna diretamente
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }

        // Se for um arquivo local, usa Storage::url() que é mais confiável
        // Verifica se o arquivo existe no storage público
        if (Storage::disk('public')->exists($imagePath)) {
            $url = Storage::disk('public')->url($imagePath);

            // If running in an HTTP request and the generated URL host doesn't match the current request host
            // prefer returning a relative /storage/... path so the image is requested on the same host/port
            try {
                if (function_exists('request') && request()) {
                    $generatedHost = parse_url($url, PHP_URL_HOST);
                    $currentHost = request()->getHost();
                    if ($generatedHost && $generatedHost !== $currentHost) {
                        return '/storage/' . $imagePath;
                    }
                }
            } catch (\Throwable $e) {
                // if anything goes wrong, fall back to the absolute URL
            }

            return $url;
        }
        
        // Fallback para asset() caso o Storage não funcione
        return asset('storage/' . $imagePath);
    }

    /**
     * Obter classes CSS para banner baseado na posição
     */
    public static function getBannerClasses($banner)
    {
        $classes = ['banner'];
        
        switch ($banner->position) {
            case 'hero':
                $classes[] = 'banner-hero';
                break;
            case 'category':
                $classes[] = 'banner-category';
                break;
            case 'product':
                $classes[] = 'banner-product';
                break;
            case 'footer':
                $classes[] = 'banner-footer';
                break;
        }

        if ($banner->department_id) {
            $classes[] = 'banner-department-' . $banner->department_id;
        } else {
            $classes[] = 'banner-global';
        }

        return implode(' ', $classes);
    }

    /**
     * Obter estatísticas de banners
     */
    public static function getBannerStats()
    {
        return [
            'total' => Banner::count(),
            'active' => Banner::active()->count(),
            'global' => Banner::global()->count(),
            'by_department' => Banner::whereNotNull('department_id')->count(),
            'by_position' => Banner::selectRaw('position, COUNT(*) as count')
                ->groupBy('position')
                ->pluck('count', 'position')
                ->toArray()
        ];
    }
}
