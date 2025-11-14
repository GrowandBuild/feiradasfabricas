<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
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
            $msgBase = $status === 401
                ? 'Autenticação falhou. Verifique Token / Client ID/Secret e se o ambiente coincide com onde o token foi gerado.'
                : ('Falha ao conectar: ' . ($lastError ?: 'Erro desconhecido'));

            // Adicionar detalhes de diagnóstico (apenas se app.debug = true)
            $debug = config('app.debug');
            if ($debug) {
                Log::warning('Melhor Envio teste falhou', [
                    'status' => $status,
                    'sandbox' => $sandbox,
                    'endpoint' => $baseUrl,
                    'has_token' => !empty($token),
                    'token_hash' => substr(md5((string)$token),0,10),
                    'has_basic' => (!empty($clientId) && !empty($clientSecret)),
                    'body' => $lastError,
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $msgBase . ($status ? " (HTTP $status)" : ''),
                'status' => $status,
                'token_hash' => $debug && $token ? substr(md5((string)$token),0,8) : null,
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Erro ao testar Melhor Envio', ['e' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Exceção ao conectar: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ajuste: garantir trim do token ao salvar via OAuth
    public function oauthCallback(Request $request)
    {
        $savedState = session('melhor_envio_oauth_state');
        $sessionSandbox = session('melhor_envio_oauth_sandbox', true);
        $state = $request->query('state');
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('admin.melhor-envio.index')->with('error', 'Código de autorização ausente.');
        }

        if (!$savedState || $state !== $savedState) {
            return redirect()->route('admin.melhor-envio.index')->with('error', 'State inválido na autorização.');
        }

        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $redirectUri = route('admin.melhor-envio.callback');

        $tokenUrl = $sessionSandbox
            ? 'https://sandbox.melhorenvio.com.br/oauth/token'
            : 'https://www.melhorenvio.com.br/oauth/token';

        try {
            $response = Http::asForm()->timeout(15)->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = trim((string)($data['access_token'] ?? ''));
                if ($accessToken) {
                    Setting::set('melhor_envio_token', $accessToken);
                    $request->session()->forget(['melhor_envio_oauth_state', 'melhor_envio_oauth_sandbox']);
                    return redirect()->route('admin.melhor-envio.index')->with('success', 'Conta autorizada e Token salvo com sucesso.');
                }
                return redirect()->route('admin.melhor-envio.index')->with('error', 'Resposta sem access_token.');
            }

            return redirect()->route('admin.melhor-envio.index')->with('error', 'Falha ao obter token: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('Erro OAuth Melhor Envio', ['e' => $e->getMessage()]);
            return redirect()->route('admin.melhor-envio.index')->with('error', 'Erro na autorização: ' . $e->getMessage());
        }
    }

    // OAuth: Iniciar autorização no Melhor Envio (opcional)
    public function authorizeStart(Request $request)
    {
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $sandbox = setting('melhor_envio_sandbox', true);

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('error', 'Defina Client ID e Client Secret para usar a autorização automática.');
        }

        $authUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/oauth/authorize'
            : 'https://www.melhorenvio.com.br/oauth/authorize';
        $redirectUri = route('admin.melhor-envio.callback');
        $state = Str::random(32);
        session([
            'melhor_envio_oauth_state' => $state,
            'melhor_envio_oauth_sandbox' => $sandbox,
        ]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'state' => $state,
            // 'scope' => 'read,write', // escopo opcional; deixe comentado se não for necessário
        ]);

        return redirect()->away($authUrl . '?' . $query);
    }

    // OAuth: Callback para trocar o code por token e salvar
    // (Método duplicado removido - versão consolidada acima)
}
