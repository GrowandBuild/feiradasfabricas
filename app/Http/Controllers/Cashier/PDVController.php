<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\PhysicalStoreService;
use Illuminate\Http\Request;

class PDVController extends Controller
{
    protected PhysicalStoreService $physicalStoreService;

    public function __construct(PhysicalStoreService $physicalStoreService)
    {
        $this->physicalStoreService = $physicalStoreService;
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            if (!auth('admin')->user()->isCashier()) {
                abort(403, 'Acesso negado. Apenas atendentes de caixa.');
            }
            return $next($request);
        });
    }

    /**
     * Exibe a interface do PDV para atendente
     */
    public function index()
    {
        if (!$this->physicalStoreService->isEnabled()) {
            return view('cashier.pdv', [
                'error' => 'Loja física não está ativada. Entre em contato com o administrador.'
            ]);
        }

        return view('cashier.pdv');
    }
}

