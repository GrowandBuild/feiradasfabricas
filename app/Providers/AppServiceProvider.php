<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Frete removido: não registrar agregador/serviços de shipping
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Inject preloaded sections into the smart-search partial so the Sessions panel
        // can render immediately without requiring an AJAX request.
        View::composer('partials.smart-search', function ($view) {
            $sections = [];
            try {
                // Detect department slug from route or request
                $deptSlug = request()->route('department') ?? request()->get('department') ?? null;
                $deptId = null;

                // Try to resolve department id if Department model exists
                if ($deptSlug && class_exists(\App\Models\Department::class)) {
                    try {
                        $dept = \App\Models\Department::where('slug', $deptSlug)->first();
                        $deptId = $dept ? $dept->id : null;
                    } catch (\Throwable $e) {
                        $deptId = null;
                    }
                }

                // If HomepageSection model exists, load sections (defensive)
                if (class_exists(\App\Models\HomepageSection::class)) {
                    $query = \App\Models\HomepageSection::query();
                    if ($deptId) $query->where('department_id', $deptId);
                    $rows = $query->orderBy('sort_order', 'asc')->get(['id','title','department_id','enabled'])->toArray();
                    $sections = array_map(function($s){
                        return [
                            'id' => $s['id'] ?? null,
                            'title' => $s['title'] ?? ($s['name'] ?? ''),
                            'reference' => isset($s['department_id']) && $s['department_id'] ? (string)$s['department_id'] : null,
                            'enabled' => isset($s['enabled']) ? (bool)$s['enabled'] : true,
                            'metadata' => ['homepage_section_id' => $s['id'] ?? null],
                        ];
                    }, $rows ?: []);
                }
            } catch (\Throwable $e) {
                try { \Log::debug('preloadedSections composer failed: ' . $e->getMessage()); } catch (\Throwable $_) {}
                $sections = [];
            }

            $view->with('preloadedSections', $sections);
        });
    }

    // Frete removido: método de registro de shipping removido
}
