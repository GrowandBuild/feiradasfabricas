<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Shipping\ShippingAggregatorService;
use App\Services\Shipping\Contracts\ShippingProviderInterface;

class ShippingAggregatorTest extends TestCase
{
    public function testAggregatorMergesAndSortsQuotes()
    {
        $p1 = new class implements ShippingProviderInterface {
            public function getName(): string { return 'mock1'; }
            public function quote(array $origin, array $destination, array $packages): array {
                return [
                    ['provider'=>'mock1','service_code'=>'A','service_name'=>'A','price'=>30,'delivery_time'=>5,'delivery_time_text'=>'5 dias','error'=>null],
                    ['provider'=>'mock1','service_code'=>'B','service_name'=>'B','price'=>20,'delivery_time'=>7,'delivery_time_text'=>'7 dias','error'=>null],
                ];
            }
            public function track(string $trackingCode): array { return ['success'=>false]; }
            public function create(array $shipmentData): array { return ['success'=>false]; }
        };
        $p2 = new class implements ShippingProviderInterface {
            public function getName(): string { return 'mock2'; }
            public function quote(array $origin, array $destination, array $packages): array {
                return [
                    ['provider'=>'mock2','service_code'=>'C','service_name'=>'C','price'=>25,'delivery_time'=>3,'delivery_time_text'=>'3 dias','error'=>null],
                ];
            }
            public function track(string $trackingCode): array { return ['success'=>false]; }
            public function create(array $shipmentData): array { return ['success'=>false]; }
        };

        $aggregator = new ShippingAggregatorService([$p1,$p2]);
        $quotes = $aggregator->quotes(['cep'=>'01001000'],['cep'=>'20040002'],[["weight"=>1,"length"=>10,"height"=>10,"width"=>10]]);

        $this->assertCount(3, $quotes);
        // Sorted by price asc: 20,25,30
        $this->assertEquals(20, $quotes[0]['price']);
        $this->assertEquals(25, $quotes[1]['price']);
        $this->assertEquals(30, $quotes[2]['price']);
    }
}
