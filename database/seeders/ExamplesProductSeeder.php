<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Services\ProductNormalizer;
use Illuminate\Support\Str;

class ExamplesProductSeeder extends Seeder
{
    public function run()
    {
        $examples = [
            // Moda - Camisa Polo
            [
                'sku' => 'VET-NIKE-POLO21-MR-BR-0001',
                'name' => 'Camisa Polo • Nike • Polo 21 • Azul Marinho/Masculino',
                'brand' => 'Nike',
                'category' => 'Moda',
                'subcategory' => 'Roupas/Camisas',
                'price' => 159.90,
                'images' => ['/images/VET-NIKE-POLO21-MR-BR-0001_1.jpg'],
                'weight' => 0.25,
                'length' => 30,
                'width' => 25,
                'height' => 2,
                'materials' => ['Algodao','Elastano'],
                'variants' => [
                    ['sku' => 'VET-NIKE-POLO21-MR-BR-0001-M', 'attributes' => ['size' => 'M'], 'price' => 159.90, 'stock_quantity' => 50],
                    ['sku' => 'VET-NIKE-POLO21-MR-BR-0001-L', 'attributes' => ['size' => 'L'], 'price' => 159.90, 'stock_quantity' => 30],
                ],
            ],

            // Eletrônico - Smartphone
            [
                'sku' => 'CEL-SAM-S20U-128G-PRE-0001',
                'name' => 'Smartphone • Samsung • S20 Ultra • 128GB/Preto',
                'brand' => 'Samsung',
                'category' => 'Eletrônicos',
                'subcategory' => 'Celulares/Smartphones',
                'price' => 4999.00,
                'images' => ['/images/CEL-SAM-S20U-128G-PRE-0001_1.jpg'],
                'weight' => 0.21,
                'length' => 16,
                'width' => 7.4,
                'height' => 0.8,
                'materials' => ['Aluminio','Vidro'],
                'variants' => [
                    ['sku' => 'CEL-SAM-S20U-128G-PRE-0001-8-128', 'attributes' => ['ram' => '8GB','storage' => '128GB','color' => 'Preto Fosco'], 'price' => 4999.00, 'stock_quantity' => 20],
                ],
            ],

            // Cosmético - Creme Facial
            [
                'sku' => 'COS-LAN-CF50-50ML-0001',
                'name' => 'Creme Facial • LaNatura • HydraSkin 50 • 50mL',
                'brand' => 'LaNatura',
                'category' => 'Beleza',
                'subcategory' => 'Cosmeticos/Cuidados Faciais',
                'price' => 89.90,
                'images' => ['/images/COS-LAN-CF50-50ML-0001_1.jpg'],
                'weight' => 0.07,
                'length' => 5,
                'width' => 5,
                'height' => 12,
                'materials' => ['Embalagem Plastico','Conteudo Cremoso'],
                'variants' => [],
            ],
        ];

        foreach ($examples as $item) {
            $sku = $item['sku'];
            $name = ProductNormalizer::normalizeName($item['name']);

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'sku' => $sku,
                    'description' => $item['description'] ?? '',
                    'short_description' => $item['short_description'] ?? null,
                    'brand' => $item['brand'] ?? null,
                    'price' => $item['price'] ?? null,
                    'images' => $item['images'] ?? null,
                    'weight' => $item['weight'] ?? null,
                    'length' => $item['length'] ?? null,
                    'width' => $item['width'] ?? null,
                    'height' => $item['height'] ?? null,
                    'specifications' => isset($item['materials']) ? ['materials' => $item['materials']] : null,
                ]
            );

            // Create variants if provided (encode attributes as JSON to avoid binding issues)
            if (!empty($item['variants']) && is_array($item['variants'])) {
                foreach ($item['variants'] as $v) {
                    $vSku = $v['sku'] ?? strtoupper(substr(md5(json_encode($v)),0,12));
                    $attributes = $v['attributes'] ?? [];

                    $attrsJson = is_array($attributes) ? json_encode($attributes, JSON_UNESCAPED_UNICODE) : ($attributes ?? null);
                    $slug = isset($product->slug) ? $product->slug . '-' . strtolower(str_replace(' ', '-', substr($vSku, 0, 8))) : strtolower(substr($vSku, 0, 12));

                    $payload = [
                        'product_id' => $product->id,
                        'sku' => $vSku,
                        'price' => $v['price'] ?? $product->price,
                        'stock_quantity' => $v['stock_quantity'] ?? 0,
                        'in_stock' => ($v['stock_quantity'] ?? 0) > 0,
                        'is_active' => true,
                        'attributes' => $attrsJson,
                        'slug' => $slug,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $existing = DB::table('product_variations')->where('sku', $vSku)->first();
                    if ($existing) {
                        DB::table('product_variations')->where('sku', $vSku)->update($payload);
                        $this->command->info('Updated variation (via DB): ' . $vSku);
                    } else {
                        DB::table('product_variations')->insert($payload);
                        $this->command->info('Inserted variation (via DB): ' . $vSku);
                    }
                }
            }

            $this->command->info("Seeded product: {$product->sku}");
        }
    }
}
