<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AttributeValue;
use App\Models\Attribute;

$attributeId = 1; // atributo alvo
echo "Ativando todos os attribute_values para attribute_id={$attributeId}...\n";

$countBefore = AttributeValue::where('attribute_id', $attributeId)->count();
$activeBefore = AttributeValue::where('attribute_id', $attributeId)->where('is_active', 1)->count();
echo "Antes: total={$countBefore}, ativos={$activeBefore}\n";

$updated = AttributeValue::where('attribute_id', $attributeId)->update(['is_active' => 1]);

$countAfter = AttributeValue::where('attribute_id', $attributeId)->count();
$activeAfter = AttributeValue::where('attribute_id', $attributeId)->where('is_active', 1)->count();

echo "Atualizado registros: {$updated}\n";
echo "Depois: total={$countAfter}, ativos={$activeAfter}\n";

$attr = Attribute::with('values')->find($attributeId);
if ($attr) {
    echo "Atributo: id={$attr->id}, name={$attr->name}, values: \n";
    foreach ($attr->values as $v) {
        echo " - id={$v->id} value={$v->value} active=" . ($v->is_active ? '1' : '0') . "\n";
    }
} else {
    echo "Atributo id={$attributeId} não encontrado\n";
}

echo "Concluído. Atualize a página do produto para ver as sugestões.";
