<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DangerController extends Controller
{
    /**
     * Apaga dados de frete (settings e colunas auxiliares) de forma TEMPORÁRIA/EMERGENTE.
     * Segurança: Só permite em ambiente não-produtivo OU para admins autenticados.
     */
    public function dropShippingData(Request $request)
    {
        $isAllowed = !app()->environment('production') || Auth::guard('admin')->check();
        if (!$isAllowed) {
            abort(403, 'Operação não permitida em produção para usuários não-admin.');
        }

        $affected = [
            'settings_deleted' => 0,
            'customers_columns_dropped' => [],
        ];

        // Remover chaves de settings relacionadas a frete/provedores
        try {
            $affected['settings_deleted'] = DB::table('settings')
                ->where(function ($q) {
                    $q->where('key', 'like', 'shipping_%')
                      ->orWhere('key', 'like', 'melhor_envio_%')
                      ->orWhere('key', 'like', 'correios_%')
                      ->orWhere('key', 'like', 'jadlog_%')
                      ->orWhere('key', 'like', 'loggi_%')
                      ->orWhere('key', 'like', 'total_express_%')
                      ->orWhere('key', 'like', '%_enabled');
                })
                ->delete();
        } catch (\Throwable $e) {
            \Log::error('Falha ao remover settings de frete', ['error' => $e->getMessage()]);
        }

        // Dropar colunas auxiliares no customers (shipping_cep, shipping_option)
        try {
            Schema::table('customers', function ($table) use (&$affected) {
                if (Schema::hasColumn('customers', 'shipping_option')) {
                    $table->dropColumn('shipping_option');
                    $affected['customers_columns_dropped'][] = 'shipping_option';
                }
                if (Schema::hasColumn('customers', 'shipping_cep')) {
                    $table->dropColumn('shipping_cep');
                    $affected['customers_columns_dropped'][] = 'shipping_cep';
                }
            });
        } catch (\Throwable $e) {
            \Log::warning('Falha ao dropar colunas de customers (frete)', ['error' => $e->getMessage()]);
        }

        // Limpar seleção de frete na sessão atual (não global)
        try { $request->session()->forget('shipping_selection'); } catch (\Throwable $e) {}

        return redirect()->back()->with('success', 'Dados de frete removidos. Detalhes: '.json_encode($affected));
    }
}
