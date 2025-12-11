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
        // ONLY use admin-provided icons. No static/public fallback. This enforces
        // that PWA icons come from the admin panel (site_app_icon or site_favicon).
        $customIcons = self::getCustomIcons($baseUrl);
        if (!empty($customIcons)) {
            $icons = $customIcons;
            if (config('app.debug')) {
                \Log::info('PWA: Usando ícones customizados do admin', [
                    'count' => count($icons),
                    'first_icon' => $icons[0]['src'] ?? null
                ]);
            }
        } else {
            if (config('app.debug')) {
                \Log::warning('PWA: Nenhum ícone customizado encontrado no admin; manifest terá lista de ícones vazia');
            }
            // Deliberately return empty icons array when admin did not provide icons.
            return [];
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
        
        // 4. Construir versões 192x192 e 512x512 a partir do ícone customizado
        $generated = self::generateResizedIcons($iconPath, $baseUrl);
        if ($generated) {
            foreach ($generated as $g) {
                $icons[] = $g;
            }
        }
        
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
        return $icons;
    }

    /**
     * Generate 192x192 and 512x512 PNGs from an admin-provided icon and
     * store them under public/storage/pwa-icons/. Returns array of icon
     * entries ready for the manifest, or empty array on failure.
     */
    private static function generateResizedIcons(string $iconPath, string $baseUrl): array
    {
        $srcFull = public_path('storage/' . $iconPath);
        if (!file_exists($srcFull)) {
            return [];
        }

        $destDir = public_path('storage/pwa-icons');
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        $basename = pathinfo($iconPath, PATHINFO_FILENAME);
        $hash = md5_file($srcFull);
        $dest192 = $destDir . '/' . $basename . '-' . $hash . '-192.png';
        $dest512 = $destDir . '/' . $basename . '-' . $hash . '-512.png';

        // If already generated, return immediately
        if (file_exists($dest192) && file_exists($dest512)) {
            return [
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest192), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest192), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest512), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest512), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ];
        }

        // Attempt to load source image
        $data = @file_get_contents($srcFull);
        if ($data === false) return [];
        $srcImg = @imagecreatefromstring($data);
        if ($srcImg === false) return [];

        // Helper to resize and save PNG with alpha
        $resizeAndSave = function($w, $h, $destPath) use ($srcImg) {
            $dst = imagecreatetruecolor($w, $h);
            imagesavealpha($dst, true);
            $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $trans_colour);
            $srcW = imagesx($srcImg);
            $srcH = imagesy($srcImg);
            imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $w, $h, $srcW, $srcH);
            @imagepng($dst, $destPath, 6);
            imagedestroy($dst);
            return file_exists($destPath);
        };

        $ok192 = $resizeAndSave(192, 192, $dest192);
        $ok512 = $resizeAndSave(512, 512, $dest512);
        imagedestroy($srcImg);

        if ($ok192 && $ok512) {
            return [
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest192), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest192), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest512), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $baseUrl . '/storage/pwa-icons/' . basename($dest512), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ];
        }

        return [];
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

