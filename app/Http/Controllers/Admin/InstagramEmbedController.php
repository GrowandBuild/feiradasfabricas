<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InstagramEmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramEmbedController extends Controller
{
    private $instagramService;

    public function __construct(InstagramEmbedService $instagramService)
    {
        $this->instagramService = $instagramService;
    }

    /**
     * Exibe a página de configuração do Instagram Embed
     */
    public function settings()
    {
        $savedUrls = $this->instagramService->getSavedUrls();
        $configuredUrls = setting('instagram_embed_urls');
        
        return view('admin.instagram-embed.settings', compact('savedUrls', 'configuredUrls'));
    }

    /**
     * Salva as URLs do Instagram
     */
    public function saveUrls(Request $request)
    {
        $request->validate([
            'instagram_urls' => 'required|string',
            'auto_sync' => 'nullable|boolean',
        ]);

        try {
            $urlsText = $request->instagram_urls;
            
            // Separar URLs por linha
            $urls = array_filter(array_map('trim', explode("\n", $urlsText)));
            
            if (empty($urls)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, adicione pelo menos uma URL do Instagram.'
                ]);
            }

            // Validar URLs
            $validUrls = [];
            foreach ($urls as $url) {
                if ($this->isValidInstagramUrl($url)) {
                    $validUrls[] = $url;
                }
            }

            if (count($validUrls) !== count($urls)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algumas URLs são inválidas. Verifique se são URLs do Instagram.'
                ]);
            }

            // Salvar URLs mesmo que não consiga processar todas
            $processed = $this->instagramService->saveUrls($validUrls);
            
            // Salvar configurações
            setting([
                'instagram_embed_urls' => implode("\n", $validUrls),
                'instagram_embed_auto_sync' => $request->boolean('auto_sync', true),
                'instagram_embed_last_sync' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'URLs do Instagram salvas com sucesso! ' . 
                           (count($processed) < count($validUrls) ? 'Algumas URLs foram salvas sem dados completos.' : ''),
                'processed_count' => count($processed),
                'total_count' => count($validUrls)
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving Instagram URLs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar URLs: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API pública para buscar embeds
     */
    public function getEmbeds()
    {
        try {
            $embeds = $this->instagramService->getSavedUrls();

            return response()->json([
                'success' => true,
                'embeds' => $embeds,
                'last_sync' => setting('instagram_embed_last_sync'),
                'total_count' => count($embeds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching Instagram embeds: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar embeds',
                'embeds' => []
            ]);
        }
    }

    /**
     * Limpa os embeds
     */
    public function clearEmbeds()
    {
        try {
            $this->instagramService->clearSavedUrls();
            
            setting([
                'instagram_embed_urls' => '',
                'instagram_embed_last_sync' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Embeds do Instagram removidos com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing Instagram embeds: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover embeds: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Testa uma URL específica
     */
    public function testUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|string|url'
        ]);

        try {
            $url = $request->url;
            
            if (!$this->isValidInstagramUrl($url)) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL inválida. Deve ser uma URL do Instagram.'
                ]);
            }

            $processed = $this->instagramService->processSingleUrl($url);
            
            if (!$processed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível processar esta URL. Verifique se o post é público.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'URL processada com sucesso!',
                'data' => $processed
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing Instagram URL: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar URL: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Valida se é uma URL do Instagram
     */
    private function isValidInstagramUrl($url)
    {
        $patterns = [
            '/instagram\.com\/p\//',
            '/instagram\.com\/reel\//',
            '/instagram\.com\/stories\/highlights\//',
            '/instagram\.com\/tv\//',
            '/instagram\.com\/reels\//',
            '/instagram\.com\/[^\/]+\/?$/',
            '/www\.instagram\.com\/p\//',
            '/www\.instagram\.com\/reel\//',
            '/www\.instagram\.com\/stories\/highlights\//',
            '/www\.instagram\.com\/tv\//',
            '/www\.instagram\.com\/reels\//',
            '/www\.instagram\.com\/[^\/]+\/?$/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }
}
