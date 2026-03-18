<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InstagramEmbedService
{
    private $cacheTime = 30; // minutos

    /**
     * Processa uma lista de URLs do Instagram
     */
    public function processUrls(array $urls)
    {
        $processed = [];
        
        foreach ($urls as $url) {
            $processed[] = $this->processSingleUrl($url);
        }
        
        return array_filter($processed);
    }

    /**
     * Processa uma única URL do Instagram
     */
    public function processSingleUrl($url)
    {
        try {
            // Limpar a URL
            $cleanUrl = $this->cleanUrl($url);
            
            // Extrair tipo de conteúdo
            $type = $this->getContentType($cleanUrl);
            
            if (!$type) {
                Log::warning("Unsupported Instagram URL: {$cleanUrl}");
                return null;
            }

            // Buscar dados do post
            $data = $this->fetchInstagramData($cleanUrl, $type);
            
            // Se não conseguiu buscar dados, criar dados básicos
            if (!$data) {
                $data = $this->getBasicData($cleanUrl, $type);
            }

            return [
                'url' => $cleanUrl,
                'type' => $type,
                'data' => $data,
                'thumbnail_url' => $this->getThumbnailUrl($data),
                'title' => $this->getTitle($data),
                'caption' => $this->getCaption($data),
                'media_url' => $this->getMediaUrl($data),
                'is_video' => $this->isVideo($data)
            ];

        } catch (\Exception $e) {
            Log::error('Error processing Instagram URL: ' . $e->getMessage());
            
            // Retornar dados básicos mesmo em caso de erro
            return [
                'url' => $this->cleanUrl($url),
                'type' => 'post',
                'data' => $this->getBasicData($url, 'post'),
                'thumbnail_url' => null,
                'title' => $this->extractUsernameFromUrl($url) . ' - Instagram',
                'caption' => '',
                'media_url' => null,
                'is_video' => false
            ];
        }
    }

    /**
     * Limpa e normaliza a URL do Instagram
     */
    private function cleanUrl($url)
    {
        // Remover parâmetros desnecessários
        $url = preg_replace('/\?.+/', '/', $url);
        $url = rtrim($url, '/');
        
        // Garantir que começa com https://
        if (!str_starts_with($url, 'http')) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }

    /**
     * Determina o tipo de conteúdo do Instagram
     */
    private function getContentType($url)
    {
        // Padrões de URL do Instagram
        if (str_contains($url, '/p/')) {
            return 'post';
        }
        
        if (str_contains($url, '/reel/')) {
            return 'reel';
        }
        
        if (str_contains($url, '/stories/highlights/')) {
            return 'highlight';
        }
        
        if (str_contains($url, '/tv/')) {
            return 'tv';
        }
        
        if (str_contains($url, '/reels/')) {
            return 'reel';
        }
        
        // Padrão de perfil
        if (preg_match('/instagram\.com\/([^\/]+)/', $url)) {
            return 'profile';
        }
        
        return null;
    }

    /**
     * Busca dados do Instagram usando múltiplos métodos
     */
    private function fetchInstagramData($url, $type)
    {
        try {
            // Método 1: Tentar oEmbed do Instagram
            $oembedData = $this->fetchOEmbedData($url);
            if ($oembedData) {
                return $oembedData;
            }

            // Método 2: Tentar scraping básico
            $scrapedData = $this->fetchScrapedData($url);
            if ($scrapedData) {
                return $scrapedData;
            }

            // Método 3: Retornar dados básicos
            return $this->getBasicData($url, $type);

        } catch (\Exception $e) {
            Log::error('Error fetching Instagram data: ' . $e->getMessage());
            return $this->getBasicData($url, $type);
        }
    }

    /**
     * Busca dados usando oEmbed
     */
    private function fetchOEmbedData($url)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/json',
                ])
                ->get("https://www.instagram.com/oembed?url=" . urlencode($url));

            if ($response->successful()) {
                return $response->json();
            }

        } catch (\Exception $e) {
            Log::info('Instagram oEmbed failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Busca dados usando scraping
     */
    private function fetchScrapedData($url)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($url);

            if ($response->successful()) {
                $html = $response->body();
                
                // Extrair dados básicos do HTML
                $data = [];
                
                // Tentar extrair título
                if (preg_match('/<title>(.*?)<\/title>/i', $html, $matches)) {
                    $data['title'] = trim($matches[1]);
                }
                
                // Tentar extrair description
                if (preg_match('/<meta name="description" content="(.*?)"/i', $html, $matches)) {
                    $data['caption'] = trim($matches[1]);
                }
                
                // Tentar extrair imagem (múltiplos métodos)
                $imagePatterns = [
                    '/<meta property="og:image" content="(.*?)"/i',
                    '/<meta property="og:image:url" content="(.*?)"/i',
                    '/<meta property="og:image:secure_url" content="(.*?)"/i',
                    '/<meta name="twitter:image" content="(.*?)"/i',
                    '/<link rel="image_src" href="(.*?)"/i'
                ];
                
                foreach ($imagePatterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        $imageUrl = trim($matches[1]);
                        // Limpar a URL da imagem
                        $imageUrl = str_replace('&amp;', '&', $imageUrl);
                        if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $data['thumbnail_url'] = $imageUrl;
                            break;
                        }
                    }
                }
                
                // Tentar extrair tipo
                if (preg_match('/<meta property="og:type" content="(.*?)"/i', $html, $matches)) {
                    $type = trim($matches[1]);
                    $data['type'] = $type === 'video' || $type === 'video.other' ? 'video' : 'photo';
                }
                
                // Se não encontrou imagem, tentar extrair do JSON-LD
                if (!isset($data['thumbnail_url'])) {
                    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/is', $html, $matches)) {
                        $jsonContent = $matches[1];
                        $jsonData = json_decode($jsonContent, true);
                        
                        if (isset($jsonData['image'])) {
                            if (is_array($jsonData['image'])) {
                                $data['thumbnail_url'] = $jsonData['image'][0] ?? null;
                            } else {
                                $data['thumbnail_url'] = $jsonData['image'];
                            }
                        }
                    }
                }
                
                if (!empty($data)) {
                    return $data;
                }
            }

        } catch (\Exception $e) {
            Log::info('Instagram scraping failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Retorna dados básicos como fallback
     */
    private function getBasicData($url, $type)
    {
        $username = $this->extractUsernameFromUrl($url);
        
        return [
            'title' => $username . ' - Instagram',
            'caption' => '',
            'thumbnail_url' => "https://ui-avatars.com/api/?name={$username}&background=E4405F&color=fff&size=300&rounded=true&bold=true&font-size=0.5",
            'type' => 'photo',
            'url' => $url
        ];
    }

    /**
     * Extrai username da URL
     */
    private function extractUsernameFromUrl($url)
    {
        if (preg_match('/instagram\.com\/([^\/]+)/', $url, $matches)) {
            return $matches[1];
        }
        return 'Instagram';
    }

    /**
     * Extrai thumbnail da mídia
     */
    private function getThumbnailUrl($data)
    {
        if (isset($data['thumbnail_url']) && !empty($data['thumbnail_url'])) {
            return $data['thumbnail_url'];
        }
        
        if (isset($data['thumbnail_src']) && !empty($data['thumbnail_src'])) {
            return $data['thumbnail_src'];
        }
        
        // Fallback: gerar avatar com base no nome
        $title = $data['title'] ?? 'Instagram';
        $name = explode(' - ', $title)[0] ?? 'IG';
        
        return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=E4405F&color=fff&size=300&rounded=true&bold=true&font-size=0.5";
    }

    /**
     * Extrai título do post
     */
    private function getTitle($data)
    {
        return $data['title'] ?? $data['author_name'] ?? 'Instagram Post';
    }

    /**
     * Extrai caption do post
     */
    private function getCaption($data)
    {
        return $data['caption'] ?? '';
    }

    /**
     * Extrai URL da mídia
     */
    private function getMediaUrl($data)
    {
        if (isset($data['url'])) {
            return $data['url'];
        }
        
        if (isset($data['media_url'])) {
            return $data['media_url'];
        }
        
        return null;
    }

    /**
     * Verifica se é vídeo
     */
    private function isVideo($data)
    {
        return $data['type'] === 'video';
    }

    /**
     * Salva URLs processadas no banco
     */
    public function saveUrls(array $urls)
    {
        try {
            $processed = $this->processUrls($urls);
            
            // Salvar no cache
            Cache::put('instagram_embed_urls', $processed, now()->addMinutes($this->cacheTime));
            
            Log::info('Saved ' . count($processed) . ' Instagram URLs');
            
            return $processed;

        } catch (\Exception $e) {
            Log::error('Error saving Instagram URLs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca URLs salvas no banco
     */
    public function getSavedUrls()
    {
        return Cache::get('instagram_embed_urls', []);
    }

    /**
     * Limpa URLs salvas
     */
    public function clearSavedUrls()
    {
        Cache::forget('instagram_embed_urls');
        Log::info('Cleared Instagram embed URLs cache');
    }
}
