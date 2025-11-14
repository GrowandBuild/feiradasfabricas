<?php

namespace App\Services\Shipping;

use App\Services\Shipping\Contracts\ShippingProviderInterface;

class ShippingAggregatorService
{
    /** @var ShippingProviderInterface[] */
    protected array $providers;

    public function __construct(array $providers)
    {
        // Expect array of ShippingProviderInterface instances
        $this->providers = $providers;
    }

    /**
     * Aggregate quotes from enabled providers.
     * $origin, $destination: ['cep' => '01234000'] minimal.
     * $packages: list of package arrays.
     */
    public function quotes(array $origin, array $destination, array $packages): array
    {
        $cacheKey = $this->buildCacheKey($origin, $destination, $packages);
        $ttl = (int) config('shipping.cache_ttl', 600);
        if (\Cache::has($cacheKey)) {
            $cached = (array) \Cache::get($cacheKey);
            if (config('shipping.cache_log_hits', false)) {
                \Log::debug('SHIPPING CACHE HIT', [
                    'key' => $cacheKey,
                    'count' => count($cached),
                ]);
            }
            return $cached;
        }

        $strategy = config('shipping.aggregate_strategy', 'single');
        if ($strategy === 'multi') {
            // Sanitiza cada pacote individualmente (aplica defaults + mínimo de peso)
            $packagesForProviders = array_map(fn($p) => $this->sanitizePackage($p), $packages);
        } else {
            // Aggregate packages into single optimized package (volumetric weight)
            $aggregated = $this->aggregatePackages($packages);
            $packagesForProviders = [$aggregated];
        }
        $all = [];
        foreach ($this->providers as $provider) {
            $quotes = $provider->quote($origin, $destination, $packagesForProviders);
            foreach ($quotes as $q) {
                $q['price'] = (float) ($q['price'] ?? 0);
                $q['delivery_time'] = (int) ($q['delivery_time'] ?? 0);
                $all[] = $q;
            }
        }
        usort($all, function ($a, $b) {
            $priceCmp = $a['price'] <=> $b['price'];
            if ($priceCmp !== 0) return $priceCmp;
            return $a['delivery_time'] <=> $b['delivery_time'];
        });

        // Não cachear resultados 100% de erro para evitar "congelar" falhas temporárias
        $hasSuccess = false;
        foreach ($all as $q) { if (empty($q['error'])) { $hasSuccess = true; break; } }
        if ($hasSuccess) {
            \Cache::put($cacheKey, $all, $ttl);
            if (config('shipping.cache_log_hits', false)) {
                \Log::debug('SHIPPING CACHE STORE', [
                    'key' => $cacheKey,
                    'ttl' => $ttl,
                    'count' => count($all),
                    'strategy' => $strategy,
                ]);
            }
        } else if (config('shipping.cache_log_hits', false)) {
            \Log::debug('SHIPPING CACHE SKIP_ALL_ERRORS', [
                'key' => $cacheKey,
                'strategy' => $strategy,
            ]);
        }
        return $all;
    }

    protected function buildCacheKey(array $origin, array $destination, array $packages): string
    {
        $dest = $destination['cep'] ?? 'na';
        $originCep = $origin['cep'] ?? 'na';
        $signature = md5(json_encode($packages));
        $providersNames = implode(',', array_map(fn($p) => $p->getName(), $this->providers));
        // Include a small signature of relevant settings to avoid stale cache after config changes
        try {
            $settingsSig = md5(json_encode([
                'origin' => setting('correios_cep_origem', ''),
                'me_enabled' => (bool) setting('melhor_envio_enabled', false),
                'me_sandbox' => (bool) setting('melhor_envio_sandbox', true),
                'me_services'=> (string) setting('melhor_envio_service_ids', ''),
                // Token hashed to not expose
                'me_token'   => substr(md5((string) setting('melhor_envio_token', '')),0,8),
                // Defaults e TTL influenciam peso/dimensões e duração lógica
                'min_w' => (float) config('shipping.defaults.min_weight'),
                'fb_w'  => (float) config('shipping.defaults.fallback_weight'),
                'dims'  => implode('x',[config('shipping.defaults.length'),config('shipping.defaults.height'),config('shipping.defaults.width')]),
                'ttl'   => (int) config('shipping.cache_ttl'),
                'agg'   => (string) config('shipping.aggregate_strategy','single'),
            ]));
        } catch (\Throwable $e) {
            $settingsSig = 'na';
        }
        $prefix = config('shipping.cache_prefix','shipping_quotes');
        return "$prefix:$originCep:$dest:$providersNames:$signature:$settingsSig";
    }

    protected function aggregatePackages(array $packages): array
    {
        $minWeight = (float) config('shipping.defaults.min_weight', 0.3);
        $fallbackWeight = (float) config('shipping.defaults.fallback_weight', 1.0);
        $defL = (int) config('shipping.defaults.length', 20);
        $defH = (int) config('shipping.defaults.height', 20);
        $defW = (int) config('shipping.defaults.width', 20);

        $totalWeight = 0.0;
        $maxLength = $maxHeight = $maxWidth = 0;
        $totalValue = 0.0;
        foreach ($packages as $p) {
            $w = (float)($p['weight'] ?? 0);
            $totalWeight += $w;
            $maxLength = max($maxLength, (int)($p['length'] ?? 0));
            $maxHeight = max($maxHeight, (int)($p['height'] ?? 0));
            $maxWidth  = max($maxWidth,  (int)($p['width'] ?? 0));
            $totalValue += (float)($p['value'] ?? 0);
        }
        // Volumetric weight (length * height * width / 6000) using cm to kg heuristic
        $volumetric = ($maxLength * $maxHeight * $maxWidth) / 6000;
        $baseWeight = $totalWeight > 0 ? $totalWeight : $fallbackWeight;
        $finalWeight = max($baseWeight, $volumetric, $minWeight); // ensure minimal plausible weight
        return [
            'weight' => round($finalWeight, 3),
            'length' => $maxLength ?: $defL,
            'height' => $maxHeight ?: $defH,
            'width'  => $maxWidth  ?: $defW,
            'value'  => $totalValue,
        ];
    }

    protected function sanitizePackage(array $p): array
    {
        $minWeight = (float) config('shipping.defaults.min_weight', 0.3);
        $fallbackWeight = (float) config('shipping.defaults.fallback_weight', 1.0);
        $defL = (int) config('shipping.defaults.length', 20);
        $defH = (int) config('shipping.defaults.height', 20);
        $defW = (int) config('shipping.defaults.width', 20);

        $weight = (float)($p['weight'] ?? 0);
        if ($weight <= 0) { $weight = $fallbackWeight; }
        $weight = max($weight, $minWeight);
        return [
            'weight' => round($weight,3),
            'length' => (int)($p['length'] ?? $defL) ?: $defL,
            'height' => (int)($p['height'] ?? $defH) ?: $defH,
            'width'  => (int)($p['width']  ?? $defW) ?: $defW,
            'value'  => (float)($p['value'] ?? 0),
        ];
    }
}
