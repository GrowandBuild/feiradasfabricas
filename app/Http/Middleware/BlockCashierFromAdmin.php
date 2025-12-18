<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockCashierFromAdmin
{
    /**
     * Bloqueia acesso de atendentes de caixa ao painel admin
     * Redireciona para o PDV
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check() && auth('admin')->user()->isCashier()) {
            // Atendente de caixa só pode acessar:
            // - Rotas de cashier (PDV)
            // - Rotas de PDV compartilhadas (busca, criar venda)
            // - Logout
            $allowedRoutes = [
                'admin.cashier.*',
                'admin.pdv.search-products',
                'admin.pdv.get-product',
                'admin.pdv.create-sale',
                'admin.pdv.confirm-payment',
                'admin.logout',
            ];

            $isAllowed = false;
            foreach ($allowedRoutes as $route) {
                if ($request->routeIs($route)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->route('admin.cashier.pdv.index')
                    ->with('error', 'Você não tem permissão para acessar esta área. Apenas o PDV está disponível.');
            }
        }

        return $next($request);
    }
}
