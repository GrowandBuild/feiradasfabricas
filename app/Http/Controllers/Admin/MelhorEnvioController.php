<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller dedicado para integração com Melhor Envio
 */
class MelhorEnvioController extends Controller
{
    private MelhorEnvioService $service;
    private string $productionCallbackUrl = 'https://rosybrown-jackal-637541.hostingersite.com/admin/melhor-envio/callback';

    public function __construct(MelhorEnvioService $service)
    {
        $this->service = $service;
    }

    /**
     * Página de configurações do Melhor Envio
     */
    public function index()
    {
        return view('admin.melhor-envio.index', [
            'isConfigured' => $this->service->isConfigured(),
            'isConnected' => $this->service->isConnected()
        ]);
    }

    /**
     * Validar credenciais (AJAX)
     */
    public function validateCredentials(Request $request)
    {
        try {
            Log::info('=== VALIDAÇÃO DE CREDENCIAIS MELHOR ENVIO ===');

            $validated = $request->validate([
                'client_id' => 'required|string|min:3',
                'client_secret' => 'required|string|min:3'
            ], [
                'client_id.required' => 'Client ID é obrigatório',
                'client_id.min' => 'Client ID inválido (mínimo 3 caracteres)',
                'client_secret.required' => 'Client Secret é obrigatório',
                'client_secret.min' => 'Client Secret inválido (mínimo 3 caracteres)'
            ]);

            $clientId = $validated['client_id'];
            $clientSecret = $validated['client_secret'];

            Log::info('Tentando validar credenciais', [
                'client_id' => substr($clientId, 0, 10) . '...',
                'client_id_length' => strlen($clientId)
            ]);

            $result = $this->service->validateCredentials($clientId, $clientSecret);

            Log::info('Resultado da validação', $result);

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(', ');
            Log::warning('Erro de validação: ' . $errors);

            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $errors
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erro ao validar credenciais: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Conectar e salvar configurações (AJAX)
     */
    public function connect(Request $request)
    {
        try {
            Log::info('=== CONEXÃO COM MELHOR ENVIO ===');

            $validated = $request->validate([
                'client_id' => 'required|string|min:3',
                'client_secret' => 'required|string|min:3',
                'cep_origem' => 'required|string|regex:/^\d{5}-?\d{3}$/'
            ], [
                'client_id.required' => 'Client ID é obrigatório',
                'client_secret.required' => 'Client Secret é obrigatório',
                'cep_origem.required' => 'CEP de origem é obrigatório',
                'cep_origem.regex' => 'CEP deve estar no formato 00000-000 ou 00000000'
            ]);

            // Normalizar CEP
            $cepOrigem = preg_replace('/\D/', '', $validated['cep_origem']);
            $cepOrigem = substr($cepOrigem, 0, 5) . '-' . substr($cepOrigem, 5, 3);

            Log::info('Tentando conectar', [
                'client_id' => substr($validated['client_id'], 0, 10) . '...',
                'cep_origem' => $cepOrigem
            ]);

            $result = $this->service->connect(
                $validated['client_id'],
                $validated['client_secret'],
                $cepOrigem
            );

            Log::info('Resultado da conexão', $result);

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(', ');

            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $errors
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erro ao conectar: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar OAuth (redirecionar para Melhor Envio)
     */
    public function startAuthorization()
    {
        if (!$this->service->isConfigured()) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('error', 'Configure Client ID e Client Secret primeiro');
        }

        // Força o callback URL para o ambiente de produção
        $redirectUri = $this->productionCallbackUrl;
        $authUrl = $this->service->getAuthorizationUrl($redirectUri);

        return redirect()->away($authUrl);
    }

    /**
     * Callback OAuth
     */
    public function callback(Request $request)
    {
        $code = $request->input('code');
        $error = $request->input('error');
        $state = $request->input('state');

        // Verificar state
        $expectedState = session('melhor_envio_oauth_state');
        if ($state !== $expectedState) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('error', 'Erro de segurança: state inválido');
        }

        if ($error) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('error', 'Autorização cancelada: ' . $error);
        }

        if (!$code) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('error', 'Código de autorização não recebido');
        }

        $redirectUri = route('admin.melhor-envio.callback');
        $result = $this->service->exchangeCodeForToken($code, $redirectUri);

        if ($result['success']) {
            return redirect()->route('admin.melhor-envio.index')
                ->with('success', 'Melhor Envio autorizado com sucesso!');
        }

        return redirect()->route('admin.melhor-envio.index')
            ->with('error', $result['message']);
    }

    /**
     * Testar conexão (AJAX)
     */
    public function testConnection()
    {
        try {
            $result = $this->service->testConnection();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desconectar (AJAX)
     */
    public function disconnect()
    {
        try {
            $result = $this->service->disconnect();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desconectar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular frete (AJAX)
     */
    public function calculateShipping(Request $request)
    {
        try {
            $validated = $request->validate([
                'cep_origem' => 'required|string|regex:/^\d{5}-?\d{3}$/',
                'cep_destino' => 'required|string|regex:/^\d{5}-?\d{3}$/',
                'products' => 'required|array'
            ]);

            // Normalizar CEPs
            $cepOrigem = preg_replace('/\D/', '', $validated['cep_origem']);
            $cepDestino = preg_replace('/\D/', '', $validated['cep_destino']);

            $result = $this->service->calculateShipping(
                $cepOrigem,
                $cepDestino,
                $validated['products']
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter status atual (AJAX)
     */
    public function status()
    {
        return response()->json([
            'configured' => $this->service->isConfigured(),
            'connected' => $this->service->isConnected(),
            'client_id' => substr(setting('melhor_envio_client_id', ''), 0, 10) . '...',
            'cep_origem' => setting('melhor_envio_cep_origem', '')
        ]);
    }
}

