<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InstagramService
{
    private $accessToken;
    private $baseUrl = 'https://graph.instagram.com';

    public function __construct()
    {
        $this->accessToken = setting('instagram_access_token');
    }

    /**
     * Busca os destaques (highlights) do perfil do Instagram
     */
    public function getHighlights()
    {
        try {
            if (!$this->accessToken) {
                Log::warning('Instagram access token not configured');
                return $this->getFallbackHighlights();
            }

            // Primeiro, busca o ID do usuário
            $userId = $this->getUserId();
            if (!$userId) {
                return $this->getFallbackHighlights();
            }

            // Busca os highlights do usuário
            $response = Http::get("{$this->baseUrl}/{$userId}/highlights", [
                'access_token' => $this->accessToken,
                'fields' => 'id,title,media_count,cover_media'
            ]);

            if (!$response->successful()) {
                Log::error('Instagram API error: ' . $response->body());
                return $this->getFallbackHighlights();
            }

            $highlights = $response->json()['data'] ?? [];

            // Para cada highlight, busca os mídias
            $processedHighlights = [];
            foreach ($highlights as $highlight) {
                $medias = $this->getHighlightMedias($highlight['id']);
                
                $processedHighlights[] = [
                    'id' => $highlight['id'],
                    'title' => $highlight['title'] ?? 'Sem título',
                    'media_count' => $highlight['media_count'] ?? 0,
                    'cover_media' => $this->processMedia($highlight['cover_media'] ?? null),
                    'medias' => $medias
                ];
            }

            // Cache por 30 minutos
            Cache::put('instagram_highlights', $processedHighlights, now()->addMinutes(30));

            return $processedHighlights;

        } catch (\Exception $e) {
            Log::error('Error fetching Instagram highlights: ' . $e->getMessage());
            return $this->getFallbackHighlights();
        }
    }

    /**
     * Busca as mídias de um highlight específico
     */
    private function getHighlightMedias($highlightId)
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$highlightId}/media", [
                'access_token' => $this->accessToken,
                'fields' => 'id,media_type,media_url,thumbnail_url,caption,timestamp',
                'limit' => 10
            ]);

            if (!$response->successful()) {
                return [];
            }

            $medias = $response->json()['data'] ?? [];
            
            return array_map([$this, 'processMedia'], $medias);

        } catch (\Exception $e) {
            Log::error('Error fetching highlight medias: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Processa uma mídia individual
     */
    private function processMedia($media)
    {
        if (!$media) {
            return null;
        }

        return [
            'id' => $media['id'] ?? null,
            'media_type' => $media['media_type'] ?? 'IMAGE',
            'media_url' => $media['media_url'] ?? null,
            'thumbnail_url' => $media['thumbnail_url'] ?? null,
            'caption' => $media['caption'] ?? '',
            'timestamp' => $media['timestamp'] ?? null,
            'is_video' => ($media['media_type'] ?? 'IMAGE') === 'VIDEO'
        ];
    }

    /**
     * Busca o ID do usuário do Instagram
     */
    private function getUserId()
    {
        try {
            $response = Http::get("{$this->baseUrl}/me", [
                'access_token' => $this->accessToken,
                'fields' => 'id,username'
            ]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json()['id'] ?? null;

        } catch (\Exception $e) {
            Log::error('Error fetching Instagram user ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retorna destaques de fallback para demonstração
     */
    private function getFallbackHighlights()
    {
        return [
            [
                'id' => 'demo_1',
                'title' => 'Novidades',
                'media_count' => 5,
                'cover_media' => [
                    'media_url' => asset('images/demo/highlight1.jpg'),
                    'media_type' => 'IMAGE'
                ],
                'medias' => [
                    [
                        'id' => 'demo_media_1',
                        'media_type' => 'VIDEO',
                        'media_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                        'thumbnail_url' => asset('images/demo/thumb1.jpg'),
                        'caption' => 'Confira nossas novidades! 🎉',
                        'is_video' => true
                    ],
                    [
                        'id' => 'demo_media_2',
                        'media_type' => 'IMAGE',
                        'media_url' => asset('images/demo/demo1.jpg'),
                        'caption' => 'Produtos em destaque ✨',
                        'is_video' => false
                    ]
                ]
            ],
            [
                'id' => 'demo_2',
                'title' => 'Promoções',
                'media_count' => 3,
                'cover_media' => [
                    'media_url' => asset('images/demo/highlight2.jpg'),
                    'media_type' => 'IMAGE'
                ],
                'medias' => [
                    [
                        'id' => 'demo_media_3',
                        'media_type' => 'VIDEO',
                        'media_url' => 'https://www.w3schools.com/html/movie.mp4',
                        'thumbnail_url' => asset('images/demo/thumb2.jpg'),
                        'caption' => 'Super promoções imperdíveis! 🔥',
                        'is_video' => true
                    ]
                ]
            ],
            [
                'id' => 'demo_3',
                'title' => 'Lançamentos',
                'media_count' => 4,
                'cover_media' => [
                    'media_url' => asset('images/demo/highlight3.jpg'),
                    'media_type' => 'IMAGE'
                ],
                'medias' => [
                    [
                        'id' => 'demo_media_4',
                        'media_type' => 'VIDEO',
                        'media_url' => 'https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4',
                        'thumbnail_url' => asset('images/demo/thumb3.jpg'),
                        'caption' => 'Novos produtos chegando! 🚀',
                        'is_video' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Verifica se o token é válido
     */
    public function validateToken()
    {
        try {
            $response = Http::get("{$this->baseUrl}/me", [
                'access_token' => $this->accessToken,
                'fields' => 'id'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Atualiza o token de acesso
     */
    public function refreshToken()
    {
        try {
            $response = Http::get("{$this->baseUrl}/refresh_access_token", [
                'grant_type' => 'refresh_token',
                'access_token' => $this->accessToken
            ]);

            if ($response->successful()) {
                $newToken = $response->json()['access_token'] ?? null;
                if ($newToken) {
                    // Salvar o novo token nas configurações
                    setting(['instagram_access_token' => $newToken]);
                    $this->accessToken = $newToken;
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error refreshing Instagram token: ' . $e->getMessage());
        }

        return false;
    }
}
