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
            // OAuth
            'scopes' => (string) setting('melhor_envio_scopes', 'users-read,shipping-companies,shipping-calculate'),
            'oauth_redirect' => route('admin.melhor-envio.callback'),
            'expires_at' => (string) setting('melhor_envio_token_expires_at', ''),
            'token_minutes_left' => $this->computeMinutesLeft(setting('melhor_envio_token_expires_at')),
            'token_scopes_payload' => $this->decodeJwtScopes(setting('melhor_envio_token')), // tentativa de ler scopes do JWT
            // Defaults de dimensões/peso
            'default_weight' => (float) setting('shipping_default_weight', 0.3),
            'default_length' => (int) setting('shipping_default_length', 20),
            'default_height' => (int) setting('shipping_default_height', 20),
            'default_width'  => (int) setting('shipping_default_width', 20),
        ];
        return view('admin.melhor-envio.index', $data);
    }

    // Página de gerenciamento de serviços / transportadoras
    public function services(Request $request)
    {
        $sandbox = (bool) setting('melhor_envio_sandbox', true);
        $token = setting('melhor_envio_token');
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $enabledIdsRaw = (string) setting('melhor_envio_service_ids', '');
        $enabledIds = collect(array_filter(array_map('trim', explode(',', $enabledIdsRaw))))->filter()->values()->all();

        $base = $sandbox ? 'https://sandbox.melhorenvio.com.br' : 'https://www.melhorenvio.com.br';
        // Candidatos de host (alguns ambientes não respondem em www)
        $candidates = $sandbox
            ? ['https://sandbox.melhorenvio.com.br']
            : ['https://www.melhorenvio.com.br', 'https://melhorenvio.com.br', 'https://api.melhorenvio.com.br'];
        // Candidatos de path (algumas rotas exigem prefixo /me)
        $pathCandidates = [
            '/api/v2/me/shipment/companies',
            '/api/v2/shipment/companies',
            '/api/v2/shipping/companies',
        ];
        $companiesEndpoint = $base . $pathCandidates[0]; // default exibido inicialmente
    $services = [];
    $error = null;
    $fromApi = false; // indicador se veio da API oficial
        $apiStatus = null;
        $apiBodySnippet = null;
        $apiUrl = $companiesEndpoint;
        try {
            $httpBase = \Http::timeout(12)->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'FeiraDasFabricas Admin/1.0'
            ]);
            if ($token) {
                $httpBase = $httpBase->withToken($token);
            } elseif ($clientId && $clientSecret) {
                $httpBase = $httpBase->withBasicAuth($clientId, $clientSecret);
            }

            foreach ($candidates as $host) {
                foreach ($pathCandidates as $path) {
                    $apiUrl = rtrim($host, '/').$path;
                    $resp = $httpBase->get($apiUrl);
                    $apiStatus = $resp->status();
                    $apiBodySnippet = substr($resp->body(), 0, 800);
                    $companiesEndpoint = $apiUrl; // para exibir na view/log

                    if ($resp->successful()) {
                        // Tenta decodificar JSON; se vier HTML, json() pode retornar null
                        $json = $resp->json();
                        if (is_array($json)) {
                            foreach ($json as $company) {
                                $companyName = $company['name'] ?? ($company['company'] ?? 'Transportadora');
                                $companyServices = $company['services'] ?? [];
                                if (is_array($companyServices)) {
                                    foreach ($companyServices as $svc) {
                                        $id = (string) ($svc['id'] ?? '');
                                        if ($id === '') continue;
                                        $services[] = [
                                            'id' => $id,
                                            'name' => $svc['name'] ?? ('Serviço '.$id),
                                            'company' => $companyName,
                                            'enabled' => in_array($id, $enabledIds, true),
                                            'delivery_time' => $svc['delivery_time'] ?? null,
                                        ];
                                    }
                                }
                            }
                            if (!empty($services)) {
                                $fromApi = true;
                                break 2; // sucesso: sai dos dois loops
                            }
                        }
                    }
                }
            }
            if (!$fromApi && !$error) {
                $error = 'Não foi possível obter a lista pela API (resposta não-JSON ou vazia).';
            }
        } catch (\Throwable $e) {
            $error = 'Exceção ao buscar serviços: '.$e->getMessage();
        }

        // Fallback estático se nada veio
        if (empty($services)) {
            $staticDefaults = [
                ['id'=>'1','name'=>'Correios PAC','company'=>'Correios'],
                ['id'=>'2','name'=>'Correios SEDEX','company'=>'Correios'],
                ['id'=>'3','name'=>'Jadlog Econômico','company'=>'Jadlog'],
                ['id'=>'4','name'=>'Jadlog Expresso','company'=>'Jadlog'],
                ['id'=>'17','name'=>'Melhor Envio Flex','company'=>'Melhor Envio'],
            ];
            foreach ($staticDefaults as $sd) {
                $services[] = [
                    'id' => $sd['id'],
                    'name' => $sd['name'],
                    'company' => $sd['company'],
                    'enabled' => in_array($sd['id'], $enabledIds, true),
                    'delivery_time' => null,
                ];
            }
            // Log detalhado quando cai em fallback
            Log::warning('MelhorEnvio Services fallback usado', [
                'sandbox' => $sandbox,
                'endpoint' => $companiesEndpoint,
                'status' => $apiStatus,
                'body_snippet' => $apiBodySnippet,
                'has_token' => !empty($token),
            ]);
        }

        return view('admin.melhor-envio.services', [
            'services' => $services,
            'error' => $error,
            'sandbox' => $sandbox,
            'fromApi' => $fromApi,
            'apiStatus' => $apiStatus,
            'apiUrl' => $apiUrl,
            'apiBodySnippet' => $apiBodySnippet,
        ]);
    }

    public function servicesSave(Request $request)
    {
        $ids = $request->input('service_ids', []);
        if (!is_array($ids)) { $ids = []; }
        $clean = collect($ids)->map(fn($i)=>trim(preg_replace('/[^0-9]/','',$i)))->filter()->unique()->values()->all();
        \App\Models\Setting::set('melhor_envio_service_ids', implode(',', $clean));
        return redirect()->route('admin.melhor-envio.services')->with('success', 'Serviços atualizados.');
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
            'melhor_envio_scopes' => 'nullable|string',
            // Defaults
            'shipping_default_weight' => 'nullable|numeric|min:0.01',
            'shipping_default_length' => 'nullable|integer|min:1',
            'shipping_default_height' => 'nullable|integer|min:1',
            'shipping_default_width'  => 'nullable|integer|min:1',
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
        if (array_key_exists('melhor_envio_scopes', $validated)) {
            Setting::set('melhor_envio_scopes', trim((string) $validated['melhor_envio_scopes']));
        }
        // Defaults persistence
        if (array_key_exists('shipping_default_weight', $validated)) {
            Setting::set('shipping_default_weight', (float) $validated['shipping_default_weight'], 'number');
        }
        if (array_key_exists('shipping_default_length', $validated)) {
            Setting::set('shipping_default_length', (int) $validated['shipping_default_length'], 'number');
        }
        if (array_key_exists('shipping_default_height', $validated)) {
            Setting::set('shipping_default_height', (int) $validated['shipping_default_height'], 'number');
        }
        if (array_key_exists('shipping_default_width', $validated)) {
            Setting::set('shipping_default_width', (int) $validated['shipping_default_width'], 'number');
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

        // Refresh automático se expirado (quando temos refresh_token salvo)
        $expiresAt = setting('melhor_envio_token_expires_at');
        $refreshToken = setting('melhor_envio_refresh_token');
        if ($token && $refreshToken && $expiresAt && now()->greaterThanOrEqualTo($expiresAt)) {
            $this->attemptRefreshToken($sandbox, $clientId, $clientSecret, $refreshToken);
            $token = setting('melhor_envio_token'); // recarrega token possivelmente renovado
        }

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
                    'expires_at' => $expiresAt,
                    'now' => now()->toDateTimeString(),
                    'body' => $lastError,
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $msgBase . ($status ? " (HTTP $status)" : ''),
                'status' => $status,
                'token_hash' => $debug && $token ? substr(md5((string)$token),0,8) : null,
                'expires_at' => $expiresAt,
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
                $refreshToken = trim((string)($data['refresh_token'] ?? ''));
                $expiresIn = (int)($data['expires_in'] ?? 0);
                if ($accessToken) {
                    Setting::set('melhor_envio_token', $accessToken);
                    if ($refreshToken) { Setting::set('melhor_envio_refresh_token', $refreshToken); }
                    if ($expiresIn > 0) {
                        Setting::set('melhor_envio_token_expires_at', now()->addSeconds(max($expiresIn - 60,0))->toDateTimeString());
                    }
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
            // Escopos: obtidos da configuração; separados por espaço conforme padrão OAuth
            'scope' => trim(str_replace(',', ' ', (string) setting('melhor_envio_scopes', 'users-read shipping-companies shipping-calculate'))),
        ]);

        return redirect()->away($authUrl . '?' . $query);
    }

    // OAuth: Callback para trocar o code por token e salvar
    // (Método duplicado removido - versão consolidada acima)

    protected function attemptRefreshToken(bool $sandbox, ?string $clientId, ?string $clientSecret, string $refreshToken): void
    {
        try {
            $tokenUrl = $sandbox
                ? 'https://sandbox.melhorenvio.com.br/oauth/token'
                : 'https://www.melhorenvio.com.br/oauth/token';
            $payload = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ];
            if ($clientId && $clientSecret) {
                $payload['client_id'] = $clientId;
                $payload['client_secret'] = $clientSecret;
            }
            $resp = Http::asForm()->timeout(12)->post($tokenUrl, $payload);
            if ($resp->successful()) {
                $data = $resp->json();
                if (!empty($data['access_token'])) {
                    Setting::set('melhor_envio_token', trim((string)$data['access_token']));
                }
                if (!empty($data['refresh_token'])) {
                    Setting::set('melhor_envio_refresh_token', trim((string)$data['refresh_token']));
                }
                if (!empty($data['expires_in'])) {
                    $expiresIn = (int)$data['expires_in'];
                    Setting::set('melhor_envio_token_expires_at', now()->addSeconds(max($expiresIn - 60,0))->toDateTimeString());
                }
                Log::info('Melhor Envio token atualizado via refresh');
            } else {
                Log::warning('Falha ao renovar token Melhor Envio', ['status'=>$resp->status(),'body'=>substr($resp->body(),0,200)]);
            }
        } catch (\Throwable $e) {
            Log::error('Exceção refresh Melhor Envio', ['error'=>$e->getMessage()]);
        }
    }

    private function computeMinutesLeft(?string $expiresAt): ?int
    {
        if (!$expiresAt) return null;
        try { $dt = \Carbon\Carbon::parse($expiresAt); return max($dt->diffInMinutes(now(), false) * -1, 0); } catch (\Throwable $e) { return null; }
    }

    private function decodeJwtScopes(?string $token): array
    {
        if (!$token || strpos($token, '.') === false) return [];
        try {
            [$h,$p,$s] = explode('.', $token);
            $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
            $scopes = $payload['scopes'] ?? [];
            if (is_string($scopes)) { $scopes = [$scopes]; }
            return is_array($scopes) ? $scopes : [];
        } catch (\Throwable $e) { return []; }
    }
}
