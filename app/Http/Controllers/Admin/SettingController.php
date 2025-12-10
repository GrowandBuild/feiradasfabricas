<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\FiscalService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Garante que a sessão nativa do PHP está disponível para SDKs que usam $_SESSION.
     * O Laravel usa sua própria store, então precisamos iniciar a sessão nativa quando o SDK exige.
     */
    private function ensureNativeSession(): void
    {
        // Evita tentar abrir sessão em CLI (tests/artisan) e previne avisos
        if (PHP_SAPI === 'cli') {
            return;
        }
        if (function_exists('session_status')) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
        } else {
            // Fallback: definir superglobal se não existir
            if (!isset($_SESSION)) {
                $_SESSION = [];
            }
        }
    }
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get();
        $groups = $settings->groupBy('group');
        
        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request)
    {
        return $this->updateWithTests($request);
    }
    
    public function updateWithTests(Request $request)
    {
        try {
            // Ações de teste rápidas
            if ($request->has('test_connection')) {
                $provider = $request->input('provider');
                $result = $this->testProviderConnection($provider);
                return response()->json($result);
            }

            if ($request->has('test_smtp')) {
                $emailService = new EmailService();
                $result = $emailService->testarSMTP();
                return response()->json($result);
            }

            if ($request->has('test_email')) {
                $email = $request->input('email', setting('contact_email', 'contato@feiradasfabricas.com'));
                $emailService = new EmailService();
                $emailService->enviarEmailTeste($email);
                return response()->json([
                    'success' => true,
                    'message' => 'Email de teste enviado para ' . $email,
                ]);
            }

            // Atualização normal de configurações
            $settings = $request->except(['_token', '_method', 'action', 'provider', 'test_connection', 'test_smtp', 'test_email', 'email']);

            if (empty($settings)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma configuração fornecida',
                ], 400);
            }

            foreach ($settings as $key => $value) {
                // Pular chaves vazias
                if (empty($key)) {
                    continue;
                }
                
                // Converter valores booleanos em boolean
                if (in_array($value, ['true', 'false', '1', '0'], true)) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                
                try {
                    Setting::set($key, $value);
                } catch (\Exception $e) {
                    \Log::error('Error saving setting', [
                        'key' => $key,
                        'value' => $value,
                        'error' => $e->getMessage()
                    ]);
                    // Continuar com os outros settings mesmo se um falhar
                }
            }

            // If site identity related settings changed, regenerate web manifest
            $identityKeys = ['site_name', 'site_description', 'site_app_icon', 'site_favicon', 'theme_secondary'];
            foreach ($identityKeys as $k) {
                if (array_key_exists($k, $settings)) {
                    try { $this->generateWebManifest(); } catch (\Throwable $e) { Log::warning('generateWebManifest failed: '.$e->getMessage()); }
                    break;
                }
            }

            // also return current theme map so frontend can apply immediately
            $themeKeys = ['theme_primary','theme_secondary','theme_accent','theme_dark_bg','theme_text_light','theme_text_dark','theme_success','theme_warning','theme_danger','theme_border'];
            $theme = [];
            foreach ($themeKeys as $k) {
                $v = Setting::get($k);
                if ($v !== null && $v !== '') $theme[$k] = $v;
            }

            return response()->json([
                'success' => true,
                'message' => 'Configurações atualizadas com sucesso!',
                'theme' => $theme,
                'slug' => session('current_department_slug', null),
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configurações: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testConnection(Request $request)
    {
        $provider = $request->input('provider');
        $action = $request->input('action');

        if ($action !== 'test_connection') {
            return response()->json([
                'success' => false,
                'message' => 'Ação inválida'
            ], 400);
        }

        try {
            $result = $this->testProviderConnection($provider);
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Erro ao testar conexão do provider {$provider}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage()
            ], 500);
        }
    }

    private function testProviderConnection($provider)
    {
        switch ($provider) {
            case 'stripe':
                return $this->testStripeConnection();
            case 'pagseguro':
                return $this->testPagSeguroConnection();
            case 'paypal':
                return $this->testPayPalConnection();
            case 'mercadopago':
                return $this->testMercadoPagoConnection();
            case 'correios':
                return $this->testCorreiosConnection();
            case 'total_express':
                return $this->testTotalExpressConnection();
            case 'jadlog':
                return $this->testJadlogConnection();
            case 'loggi':
                return $this->testLoggiConnection();
            case 'melhor_envio':
                return $this->testMelhorEnvioConnection();
            default:
                return [
                    'success' => false,
                    'message' => 'Provider não reconhecido'
                ];
        }
    }

    // Testes de conexão para APIs de pagamento
    private function testStripeConnection()
    {
        $secretKey = setting('stripe_secret_key');
        
        if (empty($secretKey)) {
            return [
                'success' => false,
                'message' => 'Chave secreta do Stripe não configurada'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey
            ])->get('https://api.stripe.com/v1/account');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Stripe estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na autenticação com Stripe: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Stripe: ' . $e->getMessage()
            ];
        }
    }

    private function testPagSeguroConnection()
    {
        $email = setting('pagseguro_email');
        $token = setting('pagseguro_token');
        
        if (empty($email) || empty($token)) {
            return [
                'success' => false,
                'message' => 'Email ou token do PagSeguro não configurados'
            ];
        }

        $sandbox = setting('pagseguro_sandbox', true);
        $baseUrl = $sandbox ? 'https://ws.sandbox.pagseguro.uol.com.br' : 'https://ws.pagseguro.uol.com.br';

        try {
            $response = Http::get($baseUrl . '/v2/sessions', [
                'email' => $email,
                'token' => $token
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com PagSeguro estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão com PagSeguro: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com PagSeguro: ' . $e->getMessage()
            ];
        }
    }

    private function testPayPalConnection()
    {
        $clientId = setting('paypal_client_id');
        $clientSecret = setting('paypal_client_secret');
        
        if (empty($clientId) || empty($clientSecret)) {
            return [
                'success' => false,
                'message' => 'Client ID ou Client Secret do PayPal não configurados'
            ];
        }

        $sandbox = setting('paypal_sandbox', true);
        $baseUrl = $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com PayPal estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na autenticação com PayPal: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com PayPal: ' . $e->getMessage()
            ];
        }
    }

    private function testMercadoPagoConnection()
    {
        $accessToken = setting('mercadopago_access_token');
        
        if (empty($accessToken)) {
            return [
                'success' => false,
                'message' => 'Access Token do Mercado Pago não configurado'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken
            ])->get('https://api.mercadopago.com/users/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Mercado Pago estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na autenticação com Mercado Pago: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Mercado Pago: ' . $e->getMessage()
            ];
        }
    }

    // Testes de conexão para APIs de entrega
    private function testCorreiosConnection()
    {
        $codigoEmpresa = setting('correios_codigo_empresa');
        $senha = setting('correios_senha');
        $cepOrigem = setting('correios_cep_origem');
        
        if (empty($codigoEmpresa) || empty($senha) || empty($cepOrigem)) {
            return [
                'success' => false,
                'message' => 'Código da empresa, senha ou CEP de origem dos Correios não configurados'
            ];
        }

        try {
            // Teste básico de conectividade com os Correios
            $response = Http::timeout(10)->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx', [
                'nCdEmpresa' => $codigoEmpresa,
                'sDsSenha' => $senha,
                'sCepOrigem' => str_replace('-', '', $cepOrigem),
                'sCepDestino' => '01310-100',
                'nVlPeso' => '1',
                'nCdFormato' => '1',
                'nVlComprimento' => '20',
                'nVlAltura' => '20',
                'nVlLargura' => '20',
                'nVlDiametro' => '0',
                'sCdMaoPropria' => 'n',
                'nVlValorDeclarado' => '0',
                'sCdAvisoRecebimento' => 'n',
                'nCdServico' => '04014',
                'nVlDiametro' => '0'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Correios estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão com Correios: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Correios: ' . $e->getMessage()
            ];
        }
    }

    private function testTotalExpressConnection()
    {
        $apiKey = setting('total_express_api_key');
        
        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key do Total Express não configurada'
            ];
        }

        $sandbox = setting('total_express_sandbox', true);
        $baseUrl = $sandbox ? 'https://api-sandbox.totalexpress.com.br' : 'https://api.totalexpress.com.br';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($baseUrl . '/v1/status');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Total Express estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão com Total Express: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Total Express: ' . $e->getMessage()
            ];
        }
    }

    private function testJadlogConnection()
    {
        $cnpj = setting('jadlog_cnpj');
        $apiKey = setting('jadlog_api_key');
        
        if (empty($cnpj) || empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'CNPJ ou API Key do Jadlog não configurados'
            ];
        }

        $sandbox = setting('jadlog_sandbox', true);
        $baseUrl = $sandbox ? 'https://api-sandbox.jadlog.com.br' : 'https://api.jadlog.com.br';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($baseUrl . '/v1/status');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Jadlog estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão com Jadlog: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Jadlog: ' . $e->getMessage()
            ];
        }
    }

    private function testLoggiConnection()
    {
        $apiKey = setting('loggi_api_key');
        
        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key do Loggi não configurada'
            ];
        }

        $sandbox = setting('loggi_sandbox', true);
        $baseUrl = $sandbox ? 'https://api-sandbox.loggi.com' : 'https://api.loggi.com';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get($baseUrl . '/v1/status');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexão com Loggi estabelecida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão com Loggi: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Loggi: ' . $e->getMessage()
            ];
        }
    }

    private function testMelhorEnvioConnection()
    {
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $token = setting('melhor_envio_token');
        $refreshToken = setting('melhor_envio_refresh_token');

        if (empty($clientId) || empty($clientSecret)) {
            return [
                'success' => false,
                'message' => 'Client ID e Client Secret do Melhor Envio não configurados'
            ];
        }

        $sandbox = setting('melhor_envio_sandbox', true);
        $env = $sandbox ? \MelhorEnvio\Enums\Environment::SANDBOX : \MelhorEnvio\Enums\Environment::PRODUCTION;
        $cepOrigem = setting('melhor_envio_cep_origem') ?: '01010010';
        $cepDestino = '20271130';
        // Normalizar CEPs (apenas dígitos) e validar tamanho
        $cepOrigem = preg_replace('/\D+/', '', (string) $cepOrigem);
        $cepDestino = preg_replace('/\D+/', '', (string) $cepDestino);
        if (strlen($cepOrigem) !== 8 || strlen($cepDestino) !== 8) {
            return [
                'success' => false,
                'message' => 'CEP de origem/destino inválido. Informe 8 dígitos (ex.: 74673030).'
            ];
        }

        try {
            if (empty($token)) {
                return [
                    'success' => false,
                    'message' => 'Sem token de acesso. Autorize o Melhor Envio no botão Conectar (OAuth).'
                ];
            }

            // Tentar calcular uma cotação simples usando o SDK oficial
            $shipment = new \MelhorEnvio\Shipment($token, $env);
            $calculator = $shipment->calculator();
            $calculator->postalCode($cepOrigem, $cepDestino);
            $calculator->addProducts(
                new \MelhorEnvio\Resources\Shipment\Product(uniqid(), 1, 1, 1, 0.1, 10.0, 1)
            );
            // Serviços opcionais: se houver IDs configurados
            $services = setting('melhor_envio_service_ids');
            if (is_string($services) && trim($services) !== '') {
                $ids = array_filter(array_map('trim', explode(',', $services)));
                if (!empty($ids)) { $calculator->addServices(...array_map('intval', $ids)); }
            } elseif (is_array($services) && !empty($services)) {
                $calculator->addServices(...array_map('intval', $services));
            }
            $quotes = $calculator->calculate();
            // Se chegou aqui, conexão ok
            return [
                'success' => true,
                'message' => 'Conexão com Melhor Envio OK. Cotações retornadas: ' . (is_array($quotes) ? count($quotes) : 0)
            ];
        } catch (\MelhorEnvio\Exceptions\CalculatorException $e) {
            // 401/expirado: tentar refresh
            Log::warning('Falha no cálculo (provável token expirado): ' . $e->getMessage());
            if (!empty($refreshToken)) {
                try {
                    $provider = new \MelhorEnvio\Auth\OAuth2($clientId, $clientSecret);
                    $provider->setEnvironment($sandbox ? 'sandbox' : 'production');
                    $new = $provider->refreshToken($refreshToken);
                    if (!empty($new['access_token'])) {
                        Setting::set('melhor_envio_token', $new['access_token'], 'string', 'delivery');
                        if (!empty($new['refresh_token'])) {
                            Setting::set('melhor_envio_refresh_token', $new['refresh_token'], 'string', 'delivery');
                        }
                        if (!empty($new['expires_in'])) {
                            Setting::set('melhor_envio_token_expires_at', now()->addSeconds((int)$new['expires_in'])->toIso8601String(), 'string', 'delivery');
                        }
                        // Tentar de novo
                        $shipment = new \MelhorEnvio\Shipment($new['access_token'], $env);
                        $calculator = $shipment->calculator();
                        $calculator->postalCode($cepOrigem, $cepDestino);
                        $calculator->addProducts(
                            new \MelhorEnvio\Resources\Shipment\Product(uniqid(), 1, 1, 1, 0.1, 10.0, 1)
                        );
                        $quotes = $calculator->calculate();
                        return [
                            'success' => true,
                            'message' => 'Token atualizado e conexão OK. Cotações: ' . (is_array($quotes) ? count($quotes) : 0)
                        ];
                    }
                } catch (\Throwable $ex) {
                    Log::error('Erro ao atualizar token do Melhor Envio: ' . $ex->getMessage());
                }
            }
            return [
                'success' => false,
                'message' => 'Falha na conexão (SDK). Verifique token/credenciais. Detalhes: ' . $e->getMessage()
            ];
        } catch (\Throwable $e) {
            Log::error('Exceção ao testar conexão Melhor Envio (SDK): ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Melhor Envio (SDK): ' . $e->getMessage()
            ];
        }
    }

    /**
     * Inicia o fluxo OAuth do Melhor Envio (redirect para página de autorização)
     */
    public function melhorEnvioAuthorize(Request $request)
    {
        // SDK do Melhor Envio usa $_SESSION para state
        $this->ensureNativeSession();
        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $sandbox = setting('melhor_envio_sandbox', true);

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Defina o Client ID e o Client Secret do Melhor Envio antes de autorizar.');
        }
        // Construir provedor via SDK oficial
        $redirectUri = route('admin.settings.melhor-envio.callback');
        try {
            // O SDK utiliza $_SESSION internamente para controlar o state
            // A middleware de sessão do Laravel já inicia a sessão PHP no grupo web
            $provider = new \MelhorEnvio\Auth\OAuth2($clientId, $clientSecret, $redirectUri);
            $provider->setEnvironment($sandbox ? 'sandbox' : 'production');
            // escopo mínimo para cálculo; ajuste conforme necessidade
            $provider->setScopes('shipping-calculate');

            session(['active_tab' => 'logistica']);
            return redirect()->away($provider->getAuthorizationUrl());
        } catch (\Throwable $e) {
            Log::error('Erro ao iniciar autorização Melhor Envio (SDK): ' . $e->getMessage());
            return redirect()->route('admin.settings.index')
                ->with('error', 'Erro ao iniciar autorização do Melhor Envio: ' . $e->getMessage());
        }
    }

    /**
     * Callback OAuth do Melhor Envio (troca code por token e salva settings)
     */
    public function melhorEnvioCallback(Request $request)
    {
        // SDK do Melhor Envio usa $_SESSION para state
        $this->ensureNativeSession();
        $state = $request->input('state');
        $code = $request->input('code');
        $error = $request->input('error');

        // garantir que a aba de logística abrirá
        session(['active_tab' => 'logistica']);

        if ($error) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Autorização com Melhor Envio cancelada: ' . $error);
        }

        if (!$code) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Código de autorização não recebido do Melhor Envio.');
        }

        $clientId = setting('melhor_envio_client_id');
        $clientSecret = setting('melhor_envio_client_secret');
        $sandbox = setting('melhor_envio_sandbox', true);

        try {
            $redirectUri = route('admin.settings.melhor-envio.callback');
            $provider = new \MelhorEnvio\Auth\OAuth2($clientId, $clientSecret, $redirectUri);
            $provider->setEnvironment($sandbox ? 'sandbox' : 'production');
            // O SDK valida state internamente via $_SESSION
            $data = $provider->getAccessToken($code, $state);
            // Salvar tokens e expiração
            Setting::set('melhor_envio_token', $data['access_token'] ?? '', 'string', 'delivery');
            if (!empty($data['refresh_token'])) {
                Setting::set('melhor_envio_refresh_token', $data['refresh_token'], 'string', 'delivery');
            }
            if (!empty($data['expires_in'])) {
                $expiresAt = now()->addSeconds((int) $data['expires_in'])->toIso8601String();
                Setting::set('melhor_envio_token_expires_at', $expiresAt, 'string', 'delivery');
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'Melhor Envio autorizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro no callback OAuth Melhor Envio (SDK): ' . $e->getMessage());
            return redirect()->route('admin.settings.index')
                ->with('error', 'Erro ao processar autorização do Melhor Envio: ' . $e->getMessage());
        }
    }

    /**
     * Revogar tokens salvos (limpar do settings)
     */
    public function melhorEnvioRevoke(Request $request)
    {
        try {
            Setting::set('melhor_envio_token', '', 'string', 'delivery');
            Setting::set('melhor_envio_refresh_token', '', 'string', 'delivery');
            Setting::set('melhor_envio_token_expires_at', '', 'string', 'delivery');

            return response()->json([
                'success' => true,
                'message' => 'Tokens do Melhor Envio removidos.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao revogar tokens: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required',
            'type' => 'required|in:string,number,boolean,json',
        ]);

        Setting::create($request->all());

        return redirect()->back()
                        ->with('success', 'Configuração criada com sucesso!');
    }

    /**
     * Generate a site.webmanifest file in public/ based on current settings.
     */
    private function generateWebManifest()
    {
        $name = setting('site_name', 'Feira das Fábricas');
        $short = setting('site_short_name', str_replace(' ', '', substr($name, 0, 12)));
        $description = setting('site_description', 'Loja online');
        $startUrl = setting('site_start_url', '/?source=pwa');
        $theme = setting('theme_secondary', '#ff9900');

        $appIcon = setting('site_app_icon');
        $fav = setting('site_favicon');

        $icons = [];
        // prefer storage URLs with cache-bust
        if ($appIcon) {
            $path = public_path('storage/' . $appIcon);
            $ver = file_exists($path) ? filemtime($path) : time();
            $url = asset('storage/' . $appIcon) . '?_=' . $ver;
            $icons[] = [ 'src' => $url, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable' ];
            $icons[] = [ 'src' => $url, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable' ];
        }
        if ($fav) {
            $path = public_path('storage/' . $fav);
            $ver = file_exists($path) ? filemtime($path) : time();
            $url = asset('storage/' . $fav) . '?_=' . $ver;
            $icons[] = [ 'src' => $url, 'sizes' => '32x32', 'type' => 'image/png' ];
            $icons[] = [ 'src' => $url, 'sizes' => '16x16', 'type' => 'image/png' ];
        }

        // fallbacks to public assets
        if (empty($icons)) {
            $icons = [
                [ 'src' => asset('android-chrome-192x192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable' ],
                [ 'src' => asset('android-chrome-512x512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable' ],
                [ 'src' => asset('favicon-32x32.png'), 'sizes' => '32x32', 'type' => 'image/png' ],
                [ 'src' => asset('favicon-16x16.png'), 'sizes' => '16x16', 'type' => 'image/png' ],
                [ 'src' => asset('apple-touch-icon.png'), 'sizes' => '180x180', 'type' => 'image/png' ],
            ];
        }

        $manifest = [
            'name' => $name,
            'short_name' => $short,
            'description' => $description,
            'start_url' => $startUrl,
            'scope' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => setting('theme_primary', '#0f172a'),
            'theme_color' => $theme,
            'icons' => $icons,
        ];

        $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents(public_path('site.webmanifest'), $json);
    }

    /**
     * Upload da logo do site via painel admin (chamada AJAX)
     */
    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:10240'
            ]);

            $file = $request->file('logo');

            // remove old logo file if present
            $old = Setting::get('site_logo');
            if ($old) {
                $oldPath = public_path('storage/' . $old);
                try { if (file_exists($oldPath)) @unlink($oldPath); } catch (\Throwable $e) { Log::warning('Could not unlink old site_logo: '.$e->getMessage()); }
            }

            // Gerar nome único
            $filename = 'site_logo_' . time() . '.' . $file->getClientOriginalExtension();

            // Armazenar em storage/app/public/site-logos
            $path = $file->storeAs('site-logos', $filename, 'public');

            // Salvar em settings (usa Setting::set helper em outro lugar)
            Setting::set('site_logo', $path, 'string', 'general');

            // regenerate manifest if needed
            try { $this->generateWebManifest(); } catch (\Throwable $e) { Log::warning('generateWebManifest failed: '.$e->getMessage()); }

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'message' => 'Logo atualizada com sucesso.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([ 'success' => false, 'errors' => $ve->errors() ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload da logo: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Erro ao fazer upload da logo.' ], 500);
        }
    }


    /**
     * Upload do favicon do site via painel admin (chamada AJAX)
     */
    public function uploadFavicon(Request $request)
    {
        try {
            $request->validate([
                'favicon' => 'required|file|mimes:png,ico,svg,webp|dimensions:max_width=1024,max_height=1024|max:5120'
            ]);

            $file = $request->file('favicon');

            // remove old favicon
            $old = Setting::get('site_favicon');
            if ($old) {
                $oldPath = public_path('storage/' . $old);
                try { if (file_exists($oldPath)) @unlink($oldPath); } catch (\Throwable $e) { Log::warning('Could not unlink old site_favicon: '.$e->getMessage()); }
            }

            $filename = 'site_favicon_' . time() . '.' . $file->getClientOriginalExtension();

            // Armazenar em storage/app/public/site-logos (shared area)
            $path = $file->storeAs('site-logos', $filename, 'public');

            Setting::set('site_favicon', $path, 'string', 'general');

            // regenerate manifest so favicon is reflected
            try { $this->generateWebManifest(); } catch (\Throwable $e) { Log::warning('generateWebManifest failed: '.$e->getMessage()); }

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'message' => 'Favicon atualizado com sucesso.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([ 'success' => false, 'errors' => $ve->errors() ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload do favicon: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Erro ao fazer upload do favicon.' ], 500);
        }
    }

    /**
     * Upload do app icon (ícone para instalação / apple-touch-icon)
     */
    public function uploadAppIcon(Request $request)
    {
        try {
            $request->validate([
                'app_icon' => 'required|image|mimes:png,jpg,jpeg,svg,webp|dimensions:max_width=2048,max_height=2048|max:10240'
            ]);

            $file = $request->file('app_icon');

            // remove old app icon
            $old = Setting::get('site_app_icon');
            if ($old) {
                $oldPath = public_path('storage/' . $old);
                try { if (file_exists($oldPath)) @unlink($oldPath); } catch (\Throwable $e) { Log::warning('Could not unlink old site_app_icon: '.$e->getMessage()); }
            }

            $filename = 'site_app_icon_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('site-logos', $filename, 'public');

            Setting::set('site_app_icon', $path, 'string', 'general');

            // regenerate manifest so app icon is reflected
            try { $this->generateWebManifest(); } catch (\Throwable $e) { Log::warning('generateWebManifest failed: '.$e->getMessage()); }

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'message' => 'App icon atualizado com sucesso.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([ 'success' => false, 'errors' => $ve->errors() ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload do app icon: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Erro ao fazer upload do app icon.' ], 500);
        }
    }

    /**
     * Store a theme map in the session so subsequent public pages can use it immediately.
     * Expects JSON payload: { theme: { theme_primary: '#...', theme_secondary: '#...', ... }, slug: 'optional-dept-slug' }
     */
    public function setSessionTheme(Request $request)
    {
        try {
            $data = $request->input('theme');
            $slug = $request->input('slug');
            if (!is_array($data)) {
                return response()->json([ 'success' => false, 'message' => 'Payload inválido: theme deve ser um objeto.' ], 422);
            }

            // sanitize: only keep keys we expect
            $allowed = [
                'theme_primary','theme_secondary','theme_accent','theme_dark_bg','theme_text_light','theme_text_dark',
                'theme_success','theme_warning','theme_danger','theme_border'
            ];

            $theme = [];
            foreach ($allowed as $k) {
                if (array_key_exists($k, $data) && $data[$k] !== null && $data[$k] !== '') {
                    $theme[$k] = (string) $data[$k];
                }
            }

            if ($slug) {
                session(['current_department_slug' => $slug]);
            }
            session(['current_department_theme' => $theme]);

            return response()->json([ 'success' => true, 'message' => 'Tema salvo na sessão.', 'theme' => $theme, 'slug' => $slug ?? null ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao setSessionTheme: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Erro ao salvar tema na sessão.' ], 500);
        }
    }
}
