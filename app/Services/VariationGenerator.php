<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class VariationGenerator
{
    /**
     * Generate or update product variations from an array of combinations.
     * Each combination is an associative array of variant attributes and optional
     * keys: price, stock_quantity, sku, sort_order.
     *
     * Example combination: ['ram' => '8GB', 'storage' => '128GB', 'color' => 'Preto', 'price' => 99.99, 'stock_quantity' => 10]
     *
     * @param Product $product
     * @param array $combinations
     * @return array created/updated variations
     */
    public function generate(Product $product, array $combinations): array
    {
        $results = [];

        foreach ($combinations as $combo) {
            $data = $this->normalizeCombination($combo);

            // Prefer explicit SKU match
            $variation = null;
            if (!empty($data['sku'])) {
                $variation = ProductVariation::where('sku', $data['sku'])->where('product_id', $product->id)->first();
            }

            // Try to match by all attributes, not just ram/storage/color
            if (!$variation) {
                $query = ProductVariation::where('product_id', $product->id);
                
                // Match by all provided attributes
                foreach ($data as $key => $value) {
                    if (!in_array($key, ['price','b2b_price','cost_price','stock_quantity','in_stock','is_active','sku','sort_order','color_hex']) && !is_null($value)) {
                        $query->where($key, $value);
                    }
                }
                
                $variation = $query->first();
            }

            if ($variation) {
                $variation->fill($data);
                $variation->save();
                $results[] = ['action' => 'updated', 'variation' => $variation];
            } else {
                // ensure sku: generate if missing
                if (empty($data['sku'])) {
                    $data['sku'] = $this->generateSku($product, $data);
                }
                $data['product_id'] = $product->id;
                $variation = ProductVariation::create($data);
                $results[] = ['action' => 'created', 'variation' => $variation];
            }
        }

        return $results;
    }

    protected function normalizeCombination(array $combo): array
    {
        // Allow any attribute, not just ram/storage/color
        $allowed = ['ram','storage','color','color_hex','price','b2b_price','cost_price','stock_quantity','in_stock','is_active','sku','sort_order','tamanho','material','voltagem','peso','modelo','tamanho','cor','material'];
        $data = [];
        
        // Include all allowed fields
        foreach ($allowed as $k) {
            if (array_key_exists($k, $combo)) $data[$k] = $combo[$k];
        }

        // Include any other custom attributes dynamically
        foreach ($combo as $key => $value) {
            if (!in_array($key, $allowed) && !is_null($value)) {
                $data[$key] = $value;
            }
        }

        // Casts and defaults
        if (isset($data['price'])) $data['price'] = (float) $data['price'];
        if (isset($data['b2b_price'])) $data['b2b_price'] = (float) $data['b2b_price'];
        if (isset($data['cost_price'])) $data['cost_price'] = (float) $data['cost_price'];
        if (isset($data['stock_quantity'])) $data['stock_quantity'] = (int) $data['stock_quantity'];
        if (!isset($data['in_stock']) && isset($data['stock_quantity'])) $data['in_stock'] = $data['stock_quantity'] > 0;
        if (!isset($data['is_active'])) $data['is_active'] = true;

        return $data;
    }

    protected function generateSku(Product $product, array $data): string
    {
        $parts = [$product->id];
        
        // Include all attribute fields in SKU generation
        $attributeFields = ['ram','storage','color','tamanho','material','voltagem','peso','modelo','cor'];
        foreach ($attributeFields as $k) {
            if (!empty($data[$k])) {
                $parts[] = Str::slug((string) $data[$k]);
            }
        }
        
        // Include any other custom attributes
        foreach ($data as $key => $value) {
            if (!in_array($key, $attributeFields) && !in_array($key, ['price','b2b_price','cost_price','stock_quantity','in_stock','is_active','sku','sort_order','color_hex']) && !empty($value)) {
                $parts[] = Str::slug((string) $value);
            }
        }
        
        $base = implode('-', $parts);
        $sku = strtoupper($base);

        // ensure uniqueness
        $counter = 1;
        $candidate = $sku;
        while (ProductVariation::where('sku', $candidate)->exists()) {
            $candidate = $sku . '-' . $counter++;
        }

        return $candidate;
    }
}
