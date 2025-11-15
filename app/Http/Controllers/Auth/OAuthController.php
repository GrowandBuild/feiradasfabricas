<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    // Integração OAuth relacionada a frete (ex.: Melhor Envio) foi desativada
    public function callback(Request $request)
    {
        Log::info('Callback OAuth ignorado (integração de frete desativada)', [
            'query_params' => $request->all(),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('info', 'Integração de OAuth para frete foi desativada.')
            ->with('active_tab', 'delivery');
    }

    public function redirect(Request $request)
    {
        Log::info('Redirecionamento OAuth bloqueado (integração de frete desativada)', [
            'provider' => $request->input('provider')
        ]);

        return redirect()->route('admin.settings.index')
            ->with('error', 'Autorizações OAuth de frete estão desativadas.')
            ->with('active_tab', 'delivery');
    }
}

