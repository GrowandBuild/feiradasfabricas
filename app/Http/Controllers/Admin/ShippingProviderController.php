<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class ShippingProviderController extends Controller
{
    protected array $providers = [
        'correios' => [
            'label' => 'Correios',
            'icon' => 'bi-mailbox'
        ],
        'jadlog' => [
            'label' => 'Jadlog',
            'icon' => 'bi-box'
        ],
        'total_express' => [
            'label' => 'Total Express',
            'icon' => 'bi-box-seam'
        ],
        'loggi' => [
            'label' => 'Loggi',
            'icon' => 'bi-lightning-charge'
        ],
        'melhor_envio' => [
            'label' => 'Melhor Envio',
            'icon' => 'bi-truck'
        ],
    ];

    public function index(Request $request)
    {
        $data = [];
        foreach ($this->providers as $key => $meta) {
            $configDefault = (bool) config('shipping.providers.' . $key, false);
            $settingVal = Setting::get($key . '_enabled');
            $enabled = $settingVal !== null ? (bool) $settingVal : $configDefault;
            $source = $settingVal !== null ? 'Override' : 'Config';
            $data[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'enabled' => $enabled,
                'source' => $source,
                'config_default' => $configDefault,
                'override_value' => $settingVal,
            ];
        }
        return view('admin.shipping.providers', [
            'providers' => $data
        ]);
    }

    public function save(Request $request)
    {
        $payload = $request->input('providers');
        if (!is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'Formato inválido'], 422);
        }
        $updated = [];
        foreach ($this->providers as $key => $meta) {
            if (array_key_exists($key, $payload)) {
                Setting::set($key . '_enabled', $payload[$key] ? 1 : 0);
            }
            $val = Setting::get($key . '_enabled');
            $updated[$key] = (bool) $val;
        }
        return response()->json([
            'ok' => true,
            'updated' => $updated,
            'message' => 'Status atualizado com sucesso.'
        ]);
    }

    public function clearCache(Request $request)
    {
        try {
            \Artisan::call('optimize:clear');
            return response()->json(['ok' => true, 'message' => 'Caches limpos']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Falha ao limpar cache: ' . $e->getMessage()], 500);
        }
    }

    public function diagnose(Request $request)
    {
        // Monta lista de providers ativos e seus metadados
        $aggregator = app(\App\Services\Shipping\ShippingAggregatorService::class);
        $providersInfo = [];
        foreach ($this->providers as $key => $meta) {
            $enabledVal = Setting::get($key.'_enabled');
            $enabled = $enabledVal !== null ? (bool)$enabledVal : (bool) config('shipping.providers.'.$key, false);
            $classMap = [
                'correios' => \App\Services\Shipping\Providers\CorreiosProvider::class,
                'jadlog' => \App\Services\Shipping\Providers\JadlogProvider::class,
                'total_express' => \App\Services\Shipping\Providers\TotalExpressProvider::class,
                'loggi' => \App\Services\Shipping\Providers\LoggiProvider::class,
                'melhor_envio' => \App\Services\Shipping\Providers\MelhorEnvioProvider::class,
            ];
            $class = $classMap[$key] ?? null;
            $version = ($class && defined($class.'::VERSION')) ? constant($class.'::VERSION') : 'n/a';
            $providersInfo[] = [
                'key' => $key,
                'label' => $meta['label'],
                'enabled' => $enabled,
                'version' => $version,
                'override' => $enabledVal,
                'config_default' => (bool) config('shipping.providers.'.$key, false),
            ];
        }
        \Log::info('Shipping diagnose', [
            'timestamp' => now()->toDateTimeString(),
            'providers' => $providersInfo,
            'app_env' => config('app.env'),
            'php_version' => PHP_VERSION,
        ]);
        return response()->json(['ok' => true, 'diagnose' => $providersInfo]);
    }
}
