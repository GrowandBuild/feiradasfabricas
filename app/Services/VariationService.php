<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\ProductVariationAttribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VariationService
{
    /**
     * Gera SKU único para variação baseado no produto e atributos
     */
    public function generateSku(Product $product, array $attributeValueIds): string
    {
        $baseSku = $product->sku;
        
        // Obter valores dos atributos
        $values = AttributeValue::whereIn('id', $attributeValueIds)
                              ->orderBy('attribute_id')
                              ->get();
        
        // Criar sufixo com slugs dos valores
        $suffix = $values->map(function($value) {
            return Str::upper(Str::substr($value->slug, 0, 4));
        })->implode('-');
        
        $sku = $suffix ? "{$baseSku}-{$suffix}" : $baseSku;
        
        // Garantir unicidade
        $counter = 1;
        $originalSku = $sku;
        while (ProductVariation::where('sku', $sku)->exists()) {
            $sku = "{$originalSku}-{$counter}";
            $counter++;
        }
        
        return $sku;
    }

    /**
     * Gera nome da variação baseado no produto e atributos
     */
    public function generateVariationName(Product $product, array $attributeValueIds): string
    {
        $values = AttributeValue::whereIn('id', $attributeValueIds)
                              ->with('attribute')
                              ->orderBy('attribute_id')
                              ->get();
        
        $attributes = $values->map(function($value) {
            return $value->display_value ?: $value->value;
        })->implode(' - ');
        
        return $attributes ? "{$product->name} - {$attributes}" : $product->name;
    }

    /**
     * Encontra variação baseada nos atributos selecionados
     */
    public function findVariationByAttributes(Product $product, array $attributeValueIds): ?ProductVariation
    {
        if (empty($attributeValueIds) || !$product->has_variations) {
            return null;
        }

        // Buscar variações que tenham exatamente esses valores de atributos
        $variationIds = DB::table('product_variation_attributes')
            ->whereIn('attribute_value_id', $attributeValueIds)
            ->groupBy('variation_id')
            ->havingRaw('COUNT(DISTINCT attribute_id) = ?', [count($attributeValueIds)])
            ->havingRaw('COUNT(*) = ?', [count($attributeValueIds)])
            ->pluck('variation_id');

        return ProductVariation::where('product_id', $product->id)
                              ->whereIn('id', $variationIds)
                              ->first();
    }

    /**
     * Valida se todas as combinações de atributos têm variações
     */
    public function validateCombinations(Product $product, array $attributeIds): array
    {
        $errors = [];
        
        if (empty($attributeIds)) {
            return $errors;
        }

        // Obter todos os valores de cada atributo
        $attributeValues = [];
        foreach ($attributeIds as $attributeId) {
            $attribute = ProductAttribute::find($attributeId);
            if (!$attribute) {
                $errors[] = "Atributo ID {$attributeId} não encontrado.";
                continue;
            }
            
            $values = $attribute->values()->pluck('id')->toArray();
            if (empty($values)) {
                $errors[] = "Atributo '{$attribute->name}' não tem valores cadastrados.";
                continue;
            }
            
            $attributeValues[$attributeId] = $values;
        }

        // Gerar todas as combinações possíveis
        $combinations = $this->generateCombinations($attributeValues);
        
        // Verificar se cada combinação tem uma variação
        foreach ($combinations as $combination) {
            $variation = $this->findVariationByAttributes($product, $combination);
            if (!$variation) {
                $values = AttributeValue::whereIn('id', $combination)->pluck('value')->toArray();
                $errors[] = "Combinação não tem variação: " . implode(' - ', $values);
            }
        }

        return $errors;
    }

    /**
     * Gera todas as combinações possíveis de valores de atributos
     */
    private function generateCombinations(array $attributeValues): array
    {
        if (empty($attributeValues)) {
            return [];
        }

        $keys = array_keys($attributeValues);
        $combinations = [[]];

        foreach ($keys as $key) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($attributeValues[$key] as $value) {
                    $newCombinations[] = array_merge($combination, [$value]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Cria variação com atributos
     */
    public function createVariation(Product $product, array $data, array $attributeValueIds): ProductVariation
    {
        return DB::transaction(function() use ($product, $data, $attributeValueIds) {
            // Gerar SKU e nome se não fornecidos
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSku($product, $attributeValueIds);
            }
            
            if (empty($data['name'])) {
                $data['name'] = $this->generateVariationName($product, $attributeValueIds);
            }

            // Criar variação
            $variation = ProductVariation::create(array_merge($data, [
                'product_id' => $product->id,
            ]));

            // Associar atributos (CORRIGIDO: validar que não há valores duplicados do mesmo atributo)
            $attributeIdsUsed = []; // Rastrear quais attribute_id já foram usados nesta variação
            
            // CORRIGIDO: Validar ANTES de criar qualquer registro
            foreach ($attributeValueIds as $valueId) {
                $value = AttributeValue::with('attribute')->findOrFail($valueId);
                
                // Verificar se já existe um valor deste atributo nesta variação
                if (isset($attributeIdsUsed[$value->attribute_id])) {
                    // Se já existe um valor deste atributo, lançar erro
                    // Uma variação só pode ter UM valor por atributo
                    throw new \Exception(
                        "Não é possível criar variação com múltiplos valores do mesmo atributo '{$value->attribute->name}'. " .
                        "Uma variação deve ter apenas um valor por atributo. " .
                        "Use o botão 'Gerar Combinações' para criar múltiplas variações automaticamente (uma para cada combinação)."
                    );
                }
                
                // Marcar este attribute_id como usado
                $attributeIdsUsed[$value->attribute_id] = $value;
            }
            
            // Se passou na validação, criar os registros
            foreach ($attributeValueIds as $valueId) {
                $value = AttributeValue::findOrFail($valueId);
                
                // Verificar se já existe no banco (proteção adicional contra race conditions)
                $exists = ProductVariationAttribute::where('variation_id', $variation->id)
                    ->where('attribute_id', $value->attribute_id)
                    ->exists();
                
                if ($exists) {
                    throw new \Exception(
                        "Já existe um valor do atributo '{$value->attribute->name}' associado a esta variação."
                    );
                }
                
                ProductVariationAttribute::create([
                    'variation_id' => $variation->id,
                    'attribute_id' => $value->attribute_id,
                    'attribute_value_id' => $valueId,
                ]);
            }

            // Atualizar flag has_variations do produto
            $product->update(['has_variations' => true]);

            // Se for a primeira variação e não tiver default, tornar esta default
            if ($product->variations()->count() === 1 && !$variation->is_default) {
                $variation->update(['is_default' => true]);
            }

            return $variation->fresh(['attributeValues.attribute']);
        });
    }

    /**
     * Atualiza variação e seus atributos
     */
    public function updateVariation(ProductVariation $variation, array $data, ?array $attributeValueIds = null): ProductVariation
    {
        return DB::transaction(function() use ($variation, $data, $attributeValueIds) {
            // Atualizar dados da variação
            $variation->update($data);

            // Se fornecido, atualizar atributos (CORRIGIDO: validar duplicados)
            if ($attributeValueIds !== null) {
                // CORRIGIDO: Validar que não há múltiplos valores do mesmo atributo
                $attributeIdsUsed = [];
                foreach ($attributeValueIds as $valueId) {
                    $value = AttributeValue::findOrFail($valueId);
                    if (isset($attributeIdsUsed[$value->attribute_id])) {
                        throw new \Exception(
                            "Não é possível atualizar variação com múltiplos valores do mesmo atributo '{$value->attribute->name}'. " .
                            "Uma variação deve ter apenas um valor por atributo."
                        );
                    }
                    $attributeIdsUsed[$value->attribute_id] = true;
                }
                
                // Remover atributos antigos
                $variation->variationAttributes()->delete();

                // Adicionar novos atributos
                foreach ($attributeValueIds as $valueId) {
                    $value = AttributeValue::findOrFail($valueId);
                    
                    // Verificar se já existe (proteção adicional)
                    $exists = ProductVariationAttribute::where('variation_id', $variation->id)
                        ->where('attribute_id', $value->attribute_id)
                        ->exists();
                    
                    if ($exists) {
                        throw new \Exception(
                            "Já existe um valor do atributo '{$value->attribute->name}' associado a esta variação."
                        );
                    }
                    
                    ProductVariationAttribute::create([
                        'variation_id' => $variation->id,
                        'attribute_id' => $value->attribute_id,
                        'attribute_value_id' => $valueId,
                    ]);
                }

                // Atualizar nome se necessário
                if (empty($data['name'])) {
                    $variation->update([
                        'name' => $this->generateVariationName($variation->product, $attributeValueIds)
                    ]);
                }
            }

            return $variation->fresh(['attributeValues.attribute']);
        });
    }

    /**
     * Obtém atributos globais com cache
     */
    public function getGlobalAttributes(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('product_attributes_global', 3600, function() {
            return ProductAttribute::with(['values' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        });
    }

    /**
     * Limpa cache de atributos
     */
    public function clearAttributesCache(): void
    {
        Cache::forget('product_attributes_global');
    }

    /**
     * Verifica quais combinações de atributos estão disponíveis em estoque
     */
    public function getAvailableCombinations(Product $product): array
    {
        if (!$product->has_variations) {
            return [];
        }

        $variations = $product->variations()
                             ->with('attributeValues')
                             ->get();

        $available = [];
        foreach ($variations as $variation) {
            if ($variation->in_stock && $variation->stock_quantity > 0) {
                $key = $variation->attributeValues->pluck('id')->sort()->implode('-');
                $available[$key] = [
                    'variation_id' => $variation->id,
                    'in_stock' => true,
                    'stock_quantity' => $variation->stock_quantity,
                    'price' => $variation->price,
                ];
            }
        }

        return $available;
    }

    /**
     * Retorna TODAS as combinações de atributos (com e sem estoque)
     * Útil para exibir todas as opções, mesmo as sem estoque
     */
    public function getAllCombinations(Product $product): array
    {
        if (!$product->has_variations) {
            return [];
        }

        $variations = $product->variations()
                             ->with('attributeValues')
                             ->get();

        $combinations = [];
        foreach ($variations as $variation) {
            $key = $variation->attributeValues->pluck('id')->sort()->implode('-');
            $combinations[$key] = [
                'variation_id' => $variation->id,
                'in_stock' => $variation->in_stock && $variation->stock_quantity > 0,
                'stock_quantity' => $variation->stock_quantity,
                'price' => $variation->price,
            ];
        }

        return $combinations;
    }
}

