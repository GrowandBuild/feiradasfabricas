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
        $ttl = config('shipping.cache_ttl', 900);
        if (\Cache::has($cacheKey)) {
            return (array) \Cache::get($cacheKey);
        }

        // Aggregate packages into single optimized package (volumetric weight)
        $aggregated = $this->aggregatePackages($packages);
        $packagesForProviders = [$aggregated];
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
            ]));
        } catch (\Throwable $e) {
            $settingsSig = 'na';
        }
        return "shipping_quotes:$originCep:$dest:$providersNames:$signature:$settingsSig";
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
}
