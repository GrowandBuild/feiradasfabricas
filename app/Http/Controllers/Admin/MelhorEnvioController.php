<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class MelhorEnvioController extends Controller
{
    public function index()
    {
        // Read current settings with sensible defaults
        $data = [
            'enabled' => (bool) setting('melhor_envio_enabled', false),
            'token' => (string) setting('melhor_envio_token', ''),
            'sandbox' => (bool) setting('melhor_envio_sandbox', true),
            'service_ids' => (string) setting('melhor_envio_service_ids', ''),
            'cep_origem' => (string) (setting('melhor_envio_cep_origem') ?: setting('correios_cep_origem', '')),
            // Advanced (hidden by default)
            'client_id' => (string) setting('melhor_envio_client_id', ''),
            'client_secret' => (string) setting('melhor_envio_client_secret', ''),
        ];
        return view('admin.melhor-envio.index', $data);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'melhor_envio_enabled' => 'nullable|boolean',
            'melhor_envio_token' => 'nullable|string',
            'melhor_envio_sandbox' => 'nullable|boolean',
            'melhor_envio_service_ids' => 'nullable|string',
            'correios_cep_origem' => 'nullable|string',
            // Advanced (optional)
            'melhor_envio_client_id' => 'nullable|string',
            'melhor_envio_client_secret' => 'nullable|string',
        ]);

        // Persist settings
        Setting::set('melhor_envio_enabled', (bool) ($validated['melhor_envio_enabled'] ?? false));
        if (array_key_exists('melhor_envio_token', $validated)) {
            Setting::set('melhor_envio_token', trim((string) $validated['melhor_envio_token']));
        }
        if (array_key_exists('melhor_envio_sandbox', $validated)) {
            Setting::set('melhor_envio_sandbox', (bool) $validated['melhor_envio_sandbox']);
        }
        if (array_key_exists('melhor_envio_service_ids', $validated)) {
            Setting::set('melhor_envio_service_ids', trim((string) $validated['melhor_envio_service_ids']));
        }
        if (array_key_exists('correios_cep_origem', $validated)) {
            Setting::set('correios_cep_origem', preg_replace('/[^0-9\-]/', '', (string) $validated['correios_cep_origem']));
            // Also keep a mirror under melhor_envio_cep_origem for completeness
            Setting::set('melhor_envio_cep_origem', preg_replace('/[^0-9\-]/', '', (string) $validated['correios_cep_origem']));
        }
        // Advanced
        if (array_key_exists('melhor_envio_client_id', $validated)) {
            Setting::set('melhor_envio_client_id', trim((string) $validated['melhor_envio_client_id']));
        }
        if (array_key_exists('melhor_envio_client_secret', $validated)) {
            Setting::set('melhor_envio_client_secret', trim((string) $validated['melhor_envio_client_secret']));
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Configurações salvas com sucesso.']);
        }
        return redirect()->route('admin.melhor-envio.index')->with('success', 'Configurações salvas com sucesso.');
    }

    public function test(Request $request)
    {
        // Use token if present; otherwise fall back to Basic Auth with client id/secret
        $token = setting('melhor_envio_token');
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $sandbox = setting('melhor_envio_sandbox', true);
        $baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2/me'
            : 'https://www.melhorenvio.com.br/api/v2/me';

        try {
            $response = null;
            $lastError = null;

            if (!empty($token)) {
                $response = Http::timeout(12)->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'FeiraDasFabricas Admin/1.0'
                ])->get($baseUrl);

                if ($response->successful()) {
                    $data = $response->json();
                    return response()->json([
                        'success' => true,
                        'message' => 'Conexão estabelecida via Token.',
                        'account' => [
                            'name' => $data['name'] ?? null,
                            'email' => $data['email'] ?? null,
                        ]
                    ]);
                }
                $lastError = $response->body();
            }

            if (!empty($clientId) && !empty($clientSecret)) {
                $response = Http::timeout(12)->withBasicAuth($clientId, $clientSecret)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'FeiraDasFabricas Admin/1.0'
                    ])->get($baseUrl);
                if ($response->successful()) {
                    $data = $response->json();
                    return response()->json([
                        'success' => true,
                        'message' => 'Conexão estabelecida via Basic Auth.',
                        'account' => [
                            'name' => $data['name'] ?? null,
                            'email' => $data['email'] ?? null,
                        ]
                    ]);
                }
                $lastError = $response->body();
            }

            $status = $response ? $response->status() : 0;
            return response()->json([
                'success' => false,
                'message' => $status === 401
                    ? 'Autenticação falhou. Verifique o Token (ou Client ID/Secret) e o ambiente (Sandbox/Produção).'
                    : ('Falha ao conectar: ' . ($lastError ?: 'Erro desconhecido') . ($status ? " (HTTP $status)" : '')),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Erro ao testar Melhor Envio', ['e' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Exceção ao conectar: ' . $e->getMessage()
            ], 500);
        }
    }
}
