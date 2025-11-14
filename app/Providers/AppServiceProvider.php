<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerShipping();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    protected function registerShipping(): void
    {
        $this->app->singleton(\App\Services\Shipping\ShippingAggregatorService::class, function ($app) {
            $providers = [];
            $cfg = config('shipping.providers', []);
            // Helper: se houver setting explícito (0/1), ele prevalece; senão usa config
            $enabled = function(string $key) use ($cfg) {
                try {
                    $val = \App\Models\Setting::get($key.'_enabled');
                    if ($val !== null) {
                        return (bool) $val; // setting explícito vence
                    }
                } catch (\Throwable $e) {}
                return (bool) ($cfg[$key] ?? false);
            };
            if ($enabled('correios'))      $providers[] = $app->make(\App\Services\Shipping\Providers\CorreiosProvider::class);
            if ($enabled('jadlog'))        $providers[] = $app->make(\App\Services\Shipping\Providers\JadlogProvider::class);
            if ($enabled('total_express')) $providers[] = $app->make(\App\Services\Shipping\Providers\TotalExpressProvider::class);
            if ($enabled('loggi'))         $providers[] = $app->make(\App\Services\Shipping\Providers\LoggiProvider::class);
            if ($enabled('melhor_envio'))  $providers[] = $app->make(\App\Services\Shipping\Providers\MelhorEnvioProvider::class);
            return new \App\Services\Shipping\ShippingAggregatorService($providers);
        });
    }
}
