<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DetectDepartmentTheme
{
    /**
     * Handle an incoming request.
     *
     * This middleware looks for a department slug in route parameters, query
     * parameters or the HTTP referer and stores it in session as
     * 'current_department_slug' so layouts can apply department-specific themes.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1) If route has parameter named 'slug' and we're on a department route
        $route = $request->route();
        $slug = null;

        if ($route) {
            $parameters = $route->parameters();
            if (!empty($parameters['slug'])) {
                // route /departamento/{slug} or /produto/{slug} - but prefer department routes
                $slug = $parameters['slug'];
                // if this route is a department route, store directly
                $name = $route->getName();
                if (Str::startsWith($name, 'department.') || Str::contains($route->getActionName(), 'DepartmentController')) {
                    session(['current_department_slug' => $slug]);
                }
            }
        }

        // 2) If query param 'dept' present, prefer it
        if (!$request->session()->has('current_department_slug') && $request->query('dept')) {
            $request->session()->put('current_department_slug', $request->query('dept'));
        }

        // 3) If still not set, try to parse HTTP referer for /departamento/{slug}
        if (!$request->session()->has('current_department_slug')) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $path = parse_url($referer, PHP_URL_PATH) ?: '';
                // match /departamento/{slug}
                if (preg_match('#/departamento/([^/]+)#i', $path, $m)) {
                    $request->session()->put('current_department_slug', $m[1]);
                }
            }
        }

        return $next($request);
    }
}
