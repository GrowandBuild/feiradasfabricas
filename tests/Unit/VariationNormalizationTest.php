<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ProductVariation;
use App\Services\VariationNormalizer;

class VariationNormalizationTest extends TestCase
{
    public function test_attributes_accessor_decodes_json_and_handles_malformed_input()
    {
        $v = new ProductVariation();

        // well-formed JSON
        $v->attributes = '{"color":"Azul","color_hex":"#00F"}';
        $this->assertIsArray($v->attributes);
        $this->assertEquals('Azul', $v->attributes['color']);
        $this->assertEquals('#00f', $v->attributes['color_hex']);

        // single quotes (common malformed case)
        $v->attributes = "[{'color':'Vermelho','color_hex':'f00'}]";
        // accessor should gracefully return array (we expect empty or decoded)
        $this->assertIsArray($v->attributes);

        // HTML-entity encoded
        $json = htmlspecialchars('{"color":"Verde","color_hex":"00ff00"}');
        $v->attributes = $json;
        $this->assertIsArray($v->attributes);
        $this->assertEquals('Verde', $v->attributes['color']);
        $this->assertEquals('#00ff00', $v->attributes['color_hex']);
    }

    public function test_color_hex_setter_and_getter_normalizes_and_syncs_with_attributes()
    {
        $v = new ProductVariation();
        $v->color_hex = 'FF00FF';
        $this->assertEquals('#ff00ff', $v->color_hex);
        $this->assertIsArray($v->attributes);
        $this->assertEquals('#ff00ff', $v->attributes['color_hex']);

        // setting attributes with hex should also normalize
        $v->attributes = ['color' => 'Amarelo', 'color_hex' => 'f1f5f9'];
        $this->assertEquals('#f1f5f9', $v->color_hex);
        $this->assertEquals('#f1f5f9', $v->attributes['color_hex']);
    }

    public function test_variationnormalizer_builds_tolerant_map()
    {
        $normalizer = new VariationNormalizer();
        $map = ['Azul ' => '#00F', ' vermelho' => 'f00', 'Verde' => '#00ff00'];
        $normalized = $normalizer->buildNormalizedColorMap($map);

        $this->assertArrayHasKey('Azul', $normalized);
        $this->assertArrayHasKey('azul', $normalized);
        $this->assertEquals('#00f', $normalized['azul']);
        $this->assertEquals('#00ff00', $normalized['verde']);
    }
}
