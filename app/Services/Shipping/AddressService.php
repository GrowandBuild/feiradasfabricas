<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AddressService
{
    /** Normalize CEP to digits only and validate length */
    public function normalizeCep(?string $cep): ?string
    {
        if (!$cep) return null;
        $digits = preg_replace('/[^0-9]/','', $cep);
        return strlen($digits) === 8 ? $digits : null;
    }

    /** Lookup CEP info using ViaCEP then BrasilAPI fallback */
    public function lookupCep(string $cep): array
    {
        $cep = $this->normalizeCep($cep);
        if (!$cep) return ['success' => false, 'error' => 'CEP inválido'];

        return Cache::remember("cep_lookup:$cep", 3600, function () use ($cep) {
            // Try ViaCEP
            $via = Http::timeout(5)->get("https://viacep.com.br/ws/$cep/json/");
            if ($via->ok() && !$via->json('erro')) {
                return [
                    'success' => true,
                    'provider' => 'viacep',
                    'cep' => $cep,
                    'street' => $via->json('logradouro'),
                    'neighborhood' => $via->json('bairro'),
                    'city' => $via->json('localidade'),
                    'state' => $via->json('uf'),
                    'ibge' => $via->json('ibge'),
                ];
            }
            // Fallback BrasilAPI
            $br = Http::timeout(5)->get("https://brasilapi.com.br/api/cep/v2/$cep");
            if ($br->ok()) {
                return [
                    'success' => true,
                    'provider' => 'brasilapi',
                    'cep' => $cep,
                    'street' => $br->json('street'),
                    'neighborhood' => $br->json('neighborhood'),
                    'city' => $br->json('city'),
                    'state' => $br->json('state'),
                    'ibge' => $br->json('ibge'),
                ];
            }
            return [
                'success' => false,
                'error' => 'Serviços de CEP indisponíveis'
            ];
        });
    }
}
