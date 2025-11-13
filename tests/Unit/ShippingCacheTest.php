<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Shipping\Contracts\ShippingProviderInterface;
use App\Services\Shipping\ShippingAggregatorService;
use Illuminate\Support\Facades\Cache;

class ShippingCacheTest extends TestCase
{
    public function testQuotesAreCached()
    {
        Cache::flush();
        $callCount = 0;
        $provider = new class($callCount) implements ShippingProviderInterface {
            private $ref; public function __construct(& $c){ $this->ref = & $c; } public function getName(): string { return 'mock'; }
            public function quote(array $o,array $d,array $p): array { $this->ref++; return [[ 'provider'=>'mock','service_code'=>'X','service_name'=>'Mock','price'=>10,'delivery_time'=>3,'delivery_time_text'=>'3 dias','error'=>null ]]; }
            public function track(string $t): array { return ['success'=>false]; }
            public function create(array $s): array { return ['success'=>false]; }
        };
    $aggregator = new ShippingAggregatorService([$provider]);
        $first = $aggregator->quotes(['cep'=>'01001000'],['cep'=>'20040002'],[['weight'=>1,'length'=>10,'height'=>10,'width'=>10]]);
        $second = $aggregator->quotes(['cep'=>'01001000'],['cep'=>'20040002'],[['weight'=>1,'length'=>10,'height'=>10,'width'=>10]]);
        $this->assertEquals(1, $callCount, 'Provider should be called once due to cache');
        $this->assertEquals($first, $second, 'Cached result should match');
    }
}
