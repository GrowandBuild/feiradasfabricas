<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OAuthController extends Controller
{
    /**
     * Processa o callback OAuth após autorização
     * 
     * Esta rota recebe o código de autorização ou token
     * de diferentes serviços (Melhor Envio, APIs de pagamento, etc.)
     */
    public function callback(Request $request)
    {
        try {
            Log::info('Callback OAuth recebido', [
                'query_params' => $request->all(),
                'provider' => $request->input('provider', 'unknown')
            ]);

            // Verificar se há código de autorização
            $code = $request->input('code');
            $state = $request->input('state');
            $error = $request->input('error');

            // Se houver erro, redirecionar com mensagem
            if ($error) {
                Log::warning('Erro no callback OAuth', [
                    'error' => $error,
                    'error_description' => $request->input('error_description')
                ]);

                return redirect()->route('admin.settings.index')
                    ->with('error', 'Erro na autorização: ' . ($request->input('error_description') ?? $error))
                    ->with('active_tab', 'delivery');
            }

            // Se houver código, processar autorização
            if ($code) {
                // Determinar o provider baseado no state ou parâmetros
                $provider = $request->input('provider') ?? $this->detectProvider($state);

                // Processar autorização baseado no provider
                $result = $this->processAuthorization($provider, $code, $state);

                if ($result['success']) {
                    return redirect()->route('admin.settings.index')
                        ->with('success', $result['message'] ?? 'Autorização realizada com sucesso!')
                        ->with('active_tab', 'delivery');
                } else {
                    return redirect()->route('admin.settings.index')
                        ->with('error', $result['message'] ?? 'Erro ao processar autorização.')
                        ->with('active_tab', 'delivery');
                }
            }

            // Se não houver código nem erro, apenas confirmar recebimento
            return redirect()->route('admin.settings.index')
                ->with('info', 'Callback recebido. Verifique as configurações.')
                ->with('active_tab', 'delivery');

        } catch (\Exception $e) {
            Log::error('Erro ao processar callback OAuth: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return redirect()->route('admin.settings.index', ['tab' => 'delivery'])
                ->with('error', 'Erro ao processar callback: ' . $e->getMessage());
        }
    }

    /**
     * Redireciona para autorização OAuth
     */
    public function redirect(Request $request)
    {
        $provider = $request->input('provider', 'melhor_envio');
        
        // Redirecionar para a URL de autorização do provider
        $authUrl = $this->getAuthorizationUrl($provider);
        
        if ($authUrl) {
            return redirect($authUrl);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'delivery'])
            ->with('error', 'Provider não suportado ou não configurado.');
    }

    /**
     * Detecta o provider baseado no state
     */
    private function detectProvider($state)
    {
        if (empty($state)) {
            return 'melhor_envio'; // Default
        }

        // Decodificar state se necessário
        $decoded = json_decode(base64_decode($state), true);
        
        if (is_array($decoded) && isset($decoded['provider'])) {
            return $decoded['provider'];
        }

        return 'melhor_envio'; // Default
    }

    /**
     * Processa a autorização baseado no provider
     */
    private function processAuthorization($provider, $code, $state)
    {
        switch ($provider) {
            case 'melhor_envio':
                return $this->processMelhorEnvioAuthorization($code);
            
            default:
                return [
                    'success' => false,
                    'message' => 'Provider não suportado: ' . $provider
                ];
        }
    }

    /**
     * Processa autorização do Melhor Envio
     */
    private function processMelhorEnvioAuthorization($code)
    {
        try {
            $sandbox = setting('melhor_envio_sandbox', true);
            $baseUrl = $sandbox 
                ? 'https://sandbox.melhorenvio.com.br'
                : 'https://www.melhorenvio.com.br';

            $clientId = setting('melhor_envio_client_id');
            $clientSecret = setting('melhor_envio_client_secret');
            
            if (empty($clientId) || empty($clientSecret)) {
                return [
                    'success' => false,
                    'message' => 'Client ID ou Client Secret não configurados. Configure primeiro nas configurações.'
                ];
            }

            // Trocar código por token
            $response = Http::asForm()->post($baseUrl . '/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => route('auth.callback', ['provider' => 'melhor_envio'])
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Salvar token nas configurações usando o helper
                \App\Models\Setting::set('melhor_envio_token', $data['access_token'] ?? null, 'string', 'delivery');
                
                if (isset($data['refresh_token'])) {
                    \App\Models\Setting::set('melhor_envio_refresh_token', $data['refresh_token'], 'string', 'delivery');
                }
                
                Log::info('Token do Melhor Envio obtido com sucesso', [
                    'has_access_token' => !empty($data['access_token']),
                    'has_refresh_token' => !empty($data['refresh_token'])
                ]);

                return [
                    'success' => true,
                    'message' => 'Autorização do Melhor Envio realizada com sucesso! Token salvo automaticamente.'
                ];
            } else {
                $errorData = $response->json();
                $errorMessage = isset($errorData['message']) 
                    ? $errorData['message'] 
                    : $response->body();
                
                Log::error('Erro ao obter token do Melhor Envio', [
                    'status' => $response->status(),
                    'response' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => 'Erro ao obter token: ' . $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar autorização do Melhor Envio: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao processar autorização: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém URL de autorização do provider
     */
    private function getAuthorizationUrl($provider)
    {
        switch ($provider) {
            case 'melhor_envio':
                $sandbox = setting('melhor_envio_sandbox', true);
                $baseUrl = $sandbox 
                    ? 'https://sandbox.melhorenvio.com.br'
                    : 'https://www.melhorenvio.com.br';
                
                $clientId = setting('melhor_envio_client_id');
                $redirectUri = route('auth.callback', ['provider' => 'melhor_envio']);
                
                if (empty($clientId)) {
                    return null;
                }

                return $baseUrl . '/oauth/authorize?' . http_build_query([
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'response_type' => 'code',
                    'scope' => 'read write'
                ]);
            
            default:
                return null;
        }
    }
}

