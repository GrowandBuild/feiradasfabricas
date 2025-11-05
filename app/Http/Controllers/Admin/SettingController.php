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

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required',
            'type' => 'required|in:string,number,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        Setting::create($request->all());

        return redirect()->back()
                        ->with('success', 'Configuração criada com sucesso!');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->back()
                        ->with('success', 'Configuração excluída com sucesso!');
    }

    /**
     * Testar conexão SEFAZ
     */
    public function testFiscalConnection()
    {
        try {
            $fiscalService = new FiscalService();
            $result = $fiscalService->testarConexao();
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Erro ao testar conexão SEFAZ: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar conexão SEFAZ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testar conexão SMTP
     */
    public function testSMTPConnection()
    {
        try {
            $emailService = new EmailService();
            $result = $emailService->testarSMTP();
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Erro ao testar conexão SMTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar conexão SMTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar email de teste
     */
    public function sendTestEmail()
    {
        try {
            $adminEmail = Auth::guard('admin')->user()->email;
            
            if (!$adminEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email do administrador não encontrado'
                ], 400);
            }

            $emailService = new EmailService();
            $emailService->enviarEmailTeste($adminEmail);
            
            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de teste: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email de teste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar configurações com suporte a testes
     */
    public function updateWithTests(Request $request)
    {
        try {
            // Verificar se é uma requisição AJAX
            if (!$request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requisição deve ser AJAX'
                ], 400);
            }

            // Verificar se é um teste de conexão
            if ($request->has('test_connection')) {
                return $this->testFiscalConnection();
            }

            if ($request->has('test_smtp')) {
                return $this->testSMTPConnection();
            }

            if ($request->has('test_email')) {
                return $this->sendTestEmail();
            }

            // Atualização normal de configurações
            $settings = $request->except(['_token', '_method', 'action', 'provider', 'test_connection', 'test_smtp', 'test_email']);

            if (empty($settings)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma configuração fornecida'
                ], 400);
            }

            foreach ($settings as $key => $value) {
                // Converter valores booleanos
                if (in_array($value, ['true', 'false', '1', '0'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                
                Setting::set($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Configurações atualizadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
            ], 500);
        }
    }
}
