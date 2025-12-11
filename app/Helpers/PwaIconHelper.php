<?php

namespace App\Helpers;

/**
 * Helper para gerenciar ícones do PWA Manifest
 * Garante que sempre temos os ícones obrigatórios (192x192 e 512x512)
 */
class PwaIconHelper
{
    /**
     * Obter todos os ícones para o manifest PWA
     * PRIORIZA ícones customizados do admin, usa nativos apenas como fallback
     * 
     * @param string $baseUrl URL base do site (ex: https://example.com)
     * @return array Array de ícones formatados para o manifest
     */
    public static function getManifestIcons(string $baseUrl): array
    {
        $icons = [];
        
        // 1. PRIORIDADE: Usar ícones customizados do admin (se existirem)
        $customIcons = self::getCustomIcons($baseUrl);
        if (!empty($customIcons)) {
            $icons = $customIcons;
            // Log para debug (apenas em desenvolvimento)
            if (config('app.debug')) {
                \Log::info('PWA: Usando ícones customizados do admin', [
                    'count' => count($icons),
                    'first_icon' => $icons[0]['src'] ?? null
                ]);
            }
        } else {
            // 2. FALLBACK: Usar ícones nativos apenas se não houver customizados
            $icons = self::getRequiredIcons($baseUrl);
            if (config('app.debug')) {
                \Log::info('PWA: Usando ícones nativos (fallback)', [
                    'count' => count($icons)
                ]);
            }
        }
        
        // 3. Validar e garantir que temos os tamanhos obrigatórios (192x192 e 512x512)
        $icons = self::ensureRequiredSizes($icons, $baseUrl);
        
        return $icons;
    }
    
    /**
     * Obter ícones nativos (apenas como FALLBACK se não houver customizados)
     * 
     * @param string $baseUrl
     * @return array
     */
    private static function getRequiredIcons(string $baseUrl): array
    {
        $icons = [];
        
        // Ícone 192x192 (fallback nativo)
        if (file_exists(public_path('android-chrome-192x192.png'))) {
            $icon192 = $baseUrl . '/android-chrome-192x192.png';
            $icons[] = [
                'src' => $icon192,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any'
            ];
            $icons[] = [
                'src' => $icon192,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable'
            ];
        }
        
        // Ícone 512x512 (fallback nativo)
        if (file_exists(public_path('android-chrome-512x512.png'))) {
            $icon512 = $baseUrl . '/android-chrome-512x512.png';
            $icons[] = [
                'src' => $icon512,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any'
            ];
            $icons[] = [
                'src' => $icon512,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable'
            ];
        }
        
        return $icons;
    }
    
    /**
     * Obter ícones customizados do admin (prioridade máxima)
     * Cria versões 192x192 e 512x512 do ícone customizado
     * 
     * @param string $baseUrl
     * @return array
     */
    private static function getCustomIcons(string $baseUrl): array
    {
        $icons = [];
        
        // 1. Tentar App Icon primeiro (prioridade)
        $siteAppIcon = setting('site_app_icon');
        $iconPath = null;
        
        if ($siteAppIcon) {
            // O caminho salvo no banco é relativo (ex: 'site-logos/filename.png')
            // O arquivo físico está em storage/app/public/site-logos/filename.png
            // E é acessível via public/storage/site-logos/filename.png (link simbólico)
            $fullPath = public_path('storage/' . $siteAppIcon);
            
            if (file_exists($fullPath)) {
                $iconPath = $siteAppIcon;
                if (config('app.debug')) {
                    \Log::info('PWA: App Icon encontrado', [
                        'path' => $siteAppIcon,
                        'full_path' => $fullPath,
                        'exists' => true
                    ]);
                }
            } else {
                if (config('app.debug')) {
                    \Log::warning('PWA: App Icon não encontrado no caminho esperado', [
                        'path' => $siteAppIcon,
                        'full_path' => $fullPath,
                        'exists' => false
                    ]);
                }
            }
        }
        
        // 2. Se não tiver App Icon, tentar Favicon
        if (!$iconPath) {
            $siteFavicon = setting('site_favicon');
            if ($siteFavicon) {
                $fullPath = public_path('storage/' . $siteFavicon);
                if (file_exists($fullPath)) {
                    $iconPath = $siteFavicon;
                    if (config('app.debug')) {
                        \Log::info('PWA: Favicon encontrado (usando como App Icon)', [
                            'path' => $siteFavicon,
                            'full_path' => $fullPath
                        ]);
                    }
                }
            }
        }
        
        // 3. Se não encontrou nenhum ícone customizado
        if (!$iconPath) {
            if (config('app.debug')) {
                \Log::warning('PWA: Nenhum ícone customizado encontrado, usando nativos');
            }
            return []; // Sem ícones customizados - vai usar nativos
        }
        
        // 4. Construir URL do ícone customizado
        $iconUrl = $baseUrl . '/storage/' . $iconPath;
        
        // Criar versões obrigatórias do ícone customizado
        // 192x192 (obrigatório)
        $icons[] = [
            'src' => $iconUrl,
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any'
        ];
        $icons[] = [
            'src' => $iconUrl,
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'maskable'
        ];
        
        // 512x512 (obrigatório)
        $icons[] = [
            'src' => $iconUrl,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any'
        ];
        $icons[] = [
            'src' => $iconUrl,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable'
        ];
        
        return $icons;
    }
    
    /**
     * Garantir que temos os tamanhos obrigatórios (192x192 e 512x512)
     * Se não tiver, tenta usar fallbacks (apenas nativos como último recurso)
     * 
     * @param array $icons
     * @param string $baseUrl
     * @return array
     */
    private static function ensureRequiredSizes(array $icons, string $baseUrl): array
    {
        $has192 = false;
        $has512 = false;
        
        // Verificar se já temos os tamanhos obrigatórios
        foreach ($icons as $icon) {
            if (isset($icon['sizes'])) {
                if (preg_match('/\b192\b/', $icon['sizes'])) {
                    $has192 = true;
                }
                if (preg_match('/\b512\b/', $icon['sizes'])) {
                    $has512 = true;
                }
            }
        }
        
        // Se não tiver os tamanhos obrigatórios, tentar usar fallbacks
        // PRIORIDADE: customizados > nativos > último recurso
        if (!$has192 || !$has512) {
            // Primeiro tentar ícones customizados como fallback
            $customFallback = self::getCustomFallbackIcon($baseUrl);
            
            if ($customFallback) {
                if (!$has192) {
                    array_unshift($icons, [
                        'src' => $customFallback,
                        'sizes' => '192x192',
                        'type' => 'image/png',
                        'purpose' => 'any maskable'
                    ]);
                }
                if (!$has512) {
                    array_unshift($icons, [
                        'src' => $customFallback,
                        'sizes' => '512x512',
                        'type' => 'image/png',
                        'purpose' => 'any maskable'
                    ]);
                }
            } else {
                // Último recurso: usar ícones nativos apenas se não houver NADA customizado
                $nativeFallback = self::getNativeFallbackIcon($baseUrl);
                if ($nativeFallback) {
                    if (!$has192) {
                        array_unshift($icons, [
                            'src' => $nativeFallback,
                            'sizes' => '192x192',
                            'type' => 'image/png',
                            'purpose' => 'any maskable'
                        ]);
                    }
                    if (!$has512) {
                        array_unshift($icons, [
                            'src' => $nativeFallback,
                            'sizes' => '512x512',
                            'type' => 'image/png',
                            'purpose' => 'any maskable'
                        ]);
                    }
                }
            }
        }
        
        // Se ainda não tiver ícones, usar último recurso absoluto
        if (empty($icons)) {
            $icons[] = [
                'src' => $baseUrl . '/favicon.ico',
                'sizes' => '192x192',
                'type' => 'image/x-icon',
                'purpose' => 'any'
            ];
        }
        
        return $icons;
    }
    
    /**
     * Obter ícone customizado como fallback (prioridade)
     * 
     * @param string $baseUrl
     * @return string|null
     */
    private static function getCustomFallbackIcon(string $baseUrl): ?string
    {
        // Tentar app icon customizado primeiro
        $siteAppIcon = setting('site_app_icon');
        if ($siteAppIcon && file_exists(public_path('storage/' . $siteAppIcon))) {
            return $baseUrl . '/storage/' . $siteAppIcon;
        }
        
        // Tentar favicon customizado
        $siteFavicon = setting('site_favicon');
        if ($siteFavicon && file_exists(public_path('storage/' . $siteFavicon))) {
            return $baseUrl . '/storage/' . $siteFavicon;
        }
        
        return null;
    }
    
    /**
     * Obter ícone nativo como fallback (último recurso)
     * 
     * @param string $baseUrl
     * @return string|null
     */
    private static function getNativeFallbackIcon(string $baseUrl): ?string
    {
        // Tentar apple-touch-icon nativo
        if (file_exists(public_path('apple-touch-icon.png'))) {
            return $baseUrl . '/apple-touch-icon.png';
        }
        
        // Tentar android-chrome nativos
        if (file_exists(public_path('android-chrome-192x192.png'))) {
            return $baseUrl . '/android-chrome-192x192.png';
        }
        
        if (file_exists(public_path('android-chrome-512x512.png'))) {
            return $baseUrl . '/android-chrome-512x512.png';
        }
        
        return null;
    }
}

