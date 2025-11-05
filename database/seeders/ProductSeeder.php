<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'JBL Go 3 - Caixa de Som Bluetooth',
                'slug' => 'jbl-go-3-caixa-som-bluetooth',
                'description' => 'A JBL Go 3 é uma caixa de som Bluetooth compacta e portátil, perfeita para levar para qualquer lugar. Com som potente e design moderno, ela oferece até 5 horas de reprodução contínua.',
                'short_description' => 'Caixa de som Bluetooth portátil com som potente e design compacto.',
                'sku' => 'JBL-GO3-001',
                'price' => 299.90,
                'b2b_price' => 249.90,
                'stock_quantity' => 50,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'JBL',
                'model' => 'Go 3',
                'images' => [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Bluetooth' => '5.1',
                    'Bateria' => '5 horas',
                    'Peso' => '209g',
                    'Dimensões' => '71 x 86 x 32 mm'
                ],
                'weight' => 0.209,
                'sort_order' => 1,
            ],
            [
                'name' => 'iPhone 14 Pro - 128GB',
                'slug' => 'iphone-14-pro-128gb',
                'description' => 'O iPhone 14 Pro com chip A16 Bionic, câmera principal de 48MP, tela Super Retina XDR de 6.1" e sistema de câmera Pro. Disponível em várias cores.',
                'short_description' => 'iPhone 14 Pro com chip A16 Bionic e câmera de 48MP.',
                'sku' => 'IPH14-PRO-128',
                'price' => 8999.00,
                'b2b_price' => 8499.00,
                'stock_quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPhone 14 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.1" Super Retina XDR',
                    'Chip' => 'A16 Bionic',
                    'Câmera' => '48MP Principal',
                    'Armazenamento' => '128GB',
                    'Sistema' => 'iOS 16'
                ],
                'weight' => 0.206,
                'sort_order' => 2,
            ],
            [
                'name' => 'Samsung Galaxy S23 - 256GB',
                'slug' => 'samsung-galaxy-s23-256gb',
                'description' => 'Smartphone Samsung Galaxy S23 com tela Dynamic AMOLED 2X de 6.1", chip Snapdragon 8 Gen 2, câmera de 50MP e bateria de 3900mAh.',
                'short_description' => 'Galaxy S23 com tela Dynamic AMOLED e chip Snapdragon 8 Gen 2.',
                'sku' => 'SAMS-S23-256',
                'price' => 4599.00,
                'b2b_price' => 4299.00,
                'stock_quantity' => 30,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Samsung',
                'model' => 'Galaxy S23',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.1" Dynamic AMOLED 2X',
                    'Chip' => 'Snapdragon 8 Gen 2',
                    'Câmera' => '50MP Principal',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'Android 13'
                ],
                'weight' => 0.168,
                'sort_order' => 3,
            ],
            [
                'name' => 'Xiaomi Redmi Note 12 - 128GB',
                'slug' => 'xiaomi-redmi-note-12-128gb',
                'description' => 'Smartphone Xiaomi Redmi Note 12 com tela AMOLED de 6.67", câmera de 50MP, bateria de 5000mAh e carregamento rápido de 33W.',
                'short_description' => 'Redmi Note 12 com tela AMOLED e bateria de 5000mAh.',
                'sku' => 'XIAOMI-RN12-128',
                'price' => 1299.00,
                'b2b_price' => 1199.00,
                'stock_quantity' => 40,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Xiaomi',
                'model' => 'Redmi Note 12',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.67" AMOLED',
                    'Chip' => 'Snapdragon 685',
                    'Câmera' => '50MP Principal',
                    'Armazenamento' => '128GB',
                    'Bateria' => '5000mAh'
                ],
                'weight' => 0.183,
                'sort_order' => 4,
            ],
            [
                'name' => 'Motorola Edge 40 - 256GB',
                'slug' => 'motorola-edge-40-256gb',
                'description' => 'Smartphone Motorola Edge 40 com tela pOLED de 6.55", câmera de 50MP, chip MediaTek Dimensity 8020 e carregamento rápido de 68W.',
                'short_description' => 'Motorola Edge 40 com tela pOLED e carregamento rápido de 68W.',
                'sku' => 'MOTO-ED40-256',
                'price' => 2199.00,
                'b2b_price' => 1999.00,
                'stock_quantity' => 20,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Motorola',
                'model' => 'Edge 40',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.55" pOLED',
                    'Chip' => 'MediaTek Dimensity 8020',
                    'Câmera' => '50MP Principal',
                    'Armazenamento' => '256GB',
                    'Carregamento' => '68W'
                ],
                'weight' => 0.171,
                'sort_order' => 5,
            ],
            [
                'name' => 'Infinix Note 12 VIP - 256GB',
                'slug' => 'infinix-note-12-vip-256gb',
                'description' => 'Smartphone Infinix Note 12 VIP com tela AMOLED de 6.7", câmera de 108MP, chip MediaTek Helio G96 e bateria de 4500mAh.',
                'short_description' => 'Infinix Note 12 VIP com câmera de 108MP e tela AMOLED.',
                'sku' => 'INFINIX-N12V-256',
                'price' => 1499.00,
                'b2b_price' => 1399.00,
                'stock_quantity' => 15,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Infinix',
                'model' => 'Note 12 VIP',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" AMOLED',
                    'Chip' => 'MediaTek Helio G96',
                    'Câmera' => '108MP Principal',
                    'Armazenamento' => '256GB',
                    'Bateria' => '4500mAh'
                ],
                'weight' => 0.198,
                'sort_order' => 6,
            ],
            [
                'name' => 'Oppo Find X5 Pro - 256GB',
                'slug' => 'oppo-find-x5-pro-256gb',
                'description' => 'Smartphone Oppo Find X5 Pro com tela AMOLED de 6.7", câmera de 50MP, chip Snapdragon 8 Gen 1 e carregamento rápido de 80W.',
                'short_description' => 'Oppo Find X5 Pro com chip Snapdragon 8 Gen 1 e câmera Hasselblad.',
                'sku' => 'OPPO-FX5P-256',
                'price' => 3999.00,
                'b2b_price' => 3799.00,
                'stock_quantity' => 12,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Oppo',
                'model' => 'Find X5 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" AMOLED',
                    'Chip' => 'Snapdragon 8 Gen 1',
                    'Câmera' => '50MP Hasselblad',
                    'Armazenamento' => '256GB',
                    'Carregamento' => '80W'
                ],
                'weight' => 0.218,
                'sort_order' => 7,
            ],
            [
                'name' => 'Realme GT Neo 3 - 128GB',
                'slug' => 'realme-gt-neo-3-128gb',
                'description' => 'Smartphone Realme GT Neo 3 com tela AMOLED de 6.7", câmera de 50MP, chip MediaTek Dimensity 8100 e carregamento rápido de 150W.',
                'short_description' => 'Realme GT Neo 3 com carregamento ultrarrápido de 150W.',
                'sku' => 'REALME-GTN3-128',
                'price' => 1899.00,
                'b2b_price' => 1699.00,
                'stock_quantity' => 18,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Realme',
                'model' => 'GT Neo 3',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" AMOLED',
                    'Chip' => 'MediaTek Dimensity 8100',
                    'Câmera' => '50MP Principal',
                    'Armazenamento' => '128GB',
                    'Carregamento' => '150W'
                ],
                'weight' => 0.188,
                'sort_order' => 8,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Associar produtos às categorias
            if (in_array($product->brand, ['JBL'])) {
                $category = Category::where('slug', 'audio')->first();
                if ($category) {
                    $product->categories()->attach($category->id);
                }
            } else {
                $category = Category::where('slug', 'smartphones')->first();
                if ($category) {
                    $product->categories()->attach($category->id);
                }
            }
        }
    }
}
