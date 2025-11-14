<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

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

    public function opcacheReset(Request $request)
    {
        try {
            $actions = [];
            if (function_exists('opcache_reset')) {
                $ok = @opcache_reset();
                $actions[] = ['opcache_reset' => $ok ? 'ok' : 'fail'];
            } else {
                $actions[] = ['opcache_reset' => 'not-available'];
            }
            if (function_exists('apcu_clear_cache')) {
                @apcu_clear_cache();
                $actions[] = ['apcu_clear_cache' => 'ok'];
            }
            \Log::info('Admin OpCache reset acionado', ['actions' => $actions]);
            return response()->json(['ok' => true, 'message' => 'OpCache/APCu reset acionado', 'actions' => $actions]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Falha ao resetar OpCache: ' . $e->getMessage()], 500);
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
        $shippingConfig = [
            'timeout' => config('shipping.timeout'),
            'cache_ttl' => config('shipping.cache_ttl'),
            'defaults' => config('shipping.defaults'),
        ];
        \Log::info('Shipping diagnose', [
            'timestamp' => now()->toDateTimeString(),
            'providers' => $providersInfo,
            'app_env' => config('app.env'),
            'php_version' => PHP_VERSION,
            'shipping_config' => $shippingConfig,
        ]);
        return response()->json(['ok' => true, 'diagnose' => $providersInfo, 'config' => $shippingConfig]);
    }

    public function testMelhorEnvio(Request $request)
    {
        // Parâmetros de teste (com defaults)
        $originCep = preg_replace('/[^0-9]/','', $request->input('from_cep', Setting::get('correios_cep_origem') ?? ''));
        $destCep   = preg_replace('/[^0-9]/','', $request->input('to_cep', '01001000'));
        $length = (int)($request->input('length', config('shipping.defaults.length', 20)));
        $height = (int)($request->input('height', config('shipping.defaults.height', 20)));
        $width  = (int)($request->input('width',  config('shipping.defaults.width', 20)));
        $weight = (float)($request->input('weight', config('shipping.defaults.fallback_weight', 1.0)));
        $insurance = (float)($request->input('insurance_value', 0));
        $services = trim((string)($request->input('services', Setting::get('melhor_envio_service_ids') ?? '')));
        if ($services === '') { $services = '1,2,3,4,17'; }

        if (strlen((string)$originCep) < 8 || strlen((string)$destCep) < 8) {
            return response()->json(['ok' => false, 'message' => 'Informe CEPs válidos (origem e destino)'], 422);
        }

        $sandbox = (bool) Setting::get('melhor_envio_sandbox', env('MELHOR_ENVIO_SANDBOX', true));
        $hosts = $sandbox
            ? ['https://sandbox.melhorenvio.com.br']
            : ['https://www.melhorenvio.com.br','https://melhorenvio.com.br','https://api.melhorenvio.com.br'];

        $probe = [];
        foreach ($hosts as $host) {
            $h = parse_url($host, PHP_URL_HOST);
            $dnsIp = null; $dnsRecords = null; $status = null; $body = null; $ctype = null; $err = null;
            try { $dnsIp = @gethostbyname($h); } catch (\Throwable $e) { $dnsIp = null; }
            try { if (function_exists('dns_get_record')) { $dnsRecords = @dns_get_record($h, DNS_A + DNS_AAAA); } } catch (\Throwable $e) { $dnsRecords = null; }
            try {
                $url = rtrim($host,'/').'/api/v2/shipment/calculate';
                $resp = Http::timeout(8)->get($url, [
                    'from_postal_code' => $originCep,
                    'to_postal_code' => $destCep,
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                    'insurance_value' => $insurance,
                    'services' => $services,
                ]);
                $status = $resp->status();
                $ctype = $resp->header('content-type');
                $body = substr($resp->body(), 0, 200);
            } catch (\Throwable $e) {
                $err = $e->getMessage();
            }
            $probe[] = [
                'host' => $host,
                'dns_ip' => $dnsIp,
                'dns_records' => $dnsRecords,
                'get_status' => $status,
                'content_type' => $ctype,
                'body_snippet' => $body,
                'error' => $err,
            ];
        }

        // Chama provider real para tentar cotar
        try {
            /** @var \App\Services\Shipping\Providers\MelhorEnvioProvider $provider */
            $provider = app(\App\Services\Shipping\Providers\MelhorEnvioProvider::class);
            $quotes = $provider->quote(['cep'=>$originCep], ['cep'=>$destCep], [[
                'length'=>$length,'height'=>$height,'width'=>$width,'weight'=>$weight,'value'=>$insurance
            ]]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>'Exceção ao cotar: '.$e->getMessage(),'probe'=>$probe], 500);
        }
        $firstError = null; if (is_array($quotes)) { foreach ($quotes as $q) { if (!empty($q['error'])) { $firstError = $q['error']; break; } } }
        return response()->json([
            'ok' => true,
            'probe' => $probe,
            'quotes' => $quotes,
            'count' => is_array($quotes) ? count($quotes) : 0,
            'first_error' => $firstError,
            'used_defaults' => compact('length','height','width','weight','insurance','services'),
            'sandbox' => $sandbox,
        ]);
    }
}
