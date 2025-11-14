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
}
