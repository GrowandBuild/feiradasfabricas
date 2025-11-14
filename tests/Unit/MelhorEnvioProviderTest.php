<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Shipping\Providers\MelhorEnvioProvider;
use Illuminate\Support\Facades\Http;

class MelhorEnvioProviderTest extends TestCase
{
    /** @test */
    public function it_returns_error_quote_when_credentials_missing()
    {
        $provider = new MelhorEnvioProvider();
        $quotes = $provider->quote(['cep' => '01001000'], ['cep' => '20040002'], [[
            'weight' => 0.5,
            'length' => 20,
            'height' => 10,
            'width' => 15,
            'value' => 100
        ]]);
        $this->assertIsArray($quotes);
        $this->assertNotEmpty($quotes);
        $this->assertEquals('melhor_envio', $quotes[0]['provider']);
        $this->assertNotEmpty($quotes[0]['error']);
    }

    /** @test */
    public function it_uses_configured_service_ids_or_default()
    {
        // Provide credentials via env fallbacks
        putenv('MELHOR_ENVIO_CLIENT_ID=abc');
        putenv('MELHOR_ENVIO_CLIENT_SECRET=def');
        putenv('MELHOR_ENVIO_SERVICE_IDS=1,99');
        putenv('MELHOR_ENVIO_SANDBOX=true');

        // Mock HTTP using Laravel's Http::fake with pattern
        Http::fake([
            'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'PAC', 'price' => '25.50', 'delivery_time' => 5],
                    ['id' => 99, 'name' => 'Express', 'price' => '55.00', 'delivery_time' => 2],
                ]
            ], 200),
        ]);

        $provider = new MelhorEnvioProvider();
        $quotes = $provider->quote(['cep' => '01001000'], ['cep' => '20040002'], [[
            'weight' => 0.3,
            'length' => 18,
            'height' => 12,
            'width' => 14,
            'value' => 80
        ]]);

        $this->assertNotEmpty($quotes);
        $this->assertCount(2, $quotes);
        $this->assertEquals(1, $quotes[0]['service_code']);
        $this->assertEquals('PAC', $quotes[0]['service_name']);
        $this->assertEquals(25.50, $quotes[0]['price']);
    }
}
