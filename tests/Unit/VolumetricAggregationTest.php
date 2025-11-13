<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Shipping\Contracts\ShippingProviderInterface;
use App\Services\Shipping\ShippingAggregatorService;

class VolumetricAggregationTest extends TestCase
{
    public function testVolumetricWeightOverridesRealWhenBigger()
    {
        $capturedPackages = [];
        $provider = new class($capturedPackages) implements ShippingProviderInterface {
            private $storeRef;
            public function __construct(& $ref) { $this->storeRef = & $ref; }
            public function getName(): string { return 'mock'; }
            public function quote(array $origin, array $destination, array $packages): array { $this->storeRef = $packages; return []; }
            public function track(string $t): array { return ['success'=>false]; }
            public function create(array $s): array { return ['success'=>false]; }
        };
        $aggregator = new ShippingAggregatorService([$provider]);
        // Real total weight small (0.5kg) but volumetric large (50*40*30 / 6000 = 10kg)
        $aggregator->quotes(['cep'=>'01001000'],['cep'=>'20040002'],[
            ['weight'=>0.2,'length'=>50,'height'=>40,'width'=>30,'value'=>100],
            ['weight'=>0.3,'length'=>10,'height'=>5,'width'=>5,'value'=>50]
        ]);
        $this->assertNotEmpty($capturedPackages);
        $pkg = $capturedPackages[0];
        $this->assertArrayHasKey('weight',$pkg);
        $this->assertGreaterThan(9.9, $pkg['weight'], 'Volumetric weight (~10kg) should override real 0.5kg');
    }
}
