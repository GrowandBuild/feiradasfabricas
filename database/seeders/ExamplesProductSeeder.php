<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
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

            $this->command->info("Seeded product: {$product->sku}");
        }
    }
}
