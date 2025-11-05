<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class CompleteiPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar ou criar categoria de smartphones
        $smartphoneCategory = Category::firstOrCreate([
            'slug' => 'smartphones'
        ], [
            'name' => 'Smartphones',
            'description' => 'Smartphones e telefones celulares',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Dados completos dos iPhones (10 ao 17) - TODAS AS VARIAÇÕES REAIS
        $iphones = [
            // iPhone 10 (iPhone X) - 2017
            [
                'model' => 'iPhone X',
                'year' => 2017,
                'storages' => ['64GB', '256GB'],
                'colors' => ['Space Gray', 'Silver'],
                'base_price' => 3499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 2800.00,
                'screen' => '5.8" Super Retina HD',
                'processor' => 'A11 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 21h de conversação'
            ],

            // iPhone XS - 2018
            [
                'model' => 'iPhone XS',
                'year' => 2018,
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Space Gray', 'Silver', 'Gold'],
                'base_price' => 4299.00,
                'b2b_discount' => 200.00,
                'cost_price' => 3500.00,
                'screen' => '5.8" Super Retina HD',
                'processor' => 'A12 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone XS Max - 2018
            [
                'model' => 'iPhone XS Max',
                'year' => 2018,
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Space Gray', 'Silver', 'Gold'],
                'base_price' => 4699.00,
                'b2b_discount' => 200.00,
                'cost_price' => 3800.00,
                'screen' => '6.5" Super Retina HD',
                'processor' => 'A12 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 25h de conversação'
            ],

            // iPhone XR - 2018
            [
                'model' => 'iPhone XR',
                'year' => 2018,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Black', 'White', 'Blue', 'Yellow', 'Coral', 'Red'],
                'base_price' => 3499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 2800.00,
                'screen' => '6.1" Liquid Retina HD',
                'processor' => 'A12 Bionic',
                'camera' => '12MP',
                'battery' => 'Até 25h de conversação'
            ],

            // iPhone 11 - 2019
            [
                'model' => 'iPhone 11',
                'year' => 2019,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Black', 'White', 'Green', 'Yellow', 'Purple', 'Red'],
                'base_price' => 3799.00,
                'b2b_discount' => 200.00,
                'cost_price' => 3100.00,
                'screen' => '6.1" Liquid Retina HD',
                'processor' => 'A13 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 17h de conversação'
            ],

            // iPhone 11 Pro - 2019
            [
                'model' => 'iPhone 11 Pro',
                'year' => 2019,
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Space Gray', 'Silver', 'Gold', 'Midnight Green'],
                'base_price' => 5299.00,
                'b2b_discount' => 300.00,
                'cost_price' => 4300.00,
                'screen' => '5.8" Super Retina XDR',
                'processor' => 'A13 Bionic',
                'camera' => '12MP + 12MP + 12MP',
                'battery' => 'Até 18h de conversação'
            ],

            // iPhone 11 Pro Max - 2019
            [
                'model' => 'iPhone 11 Pro Max',
                'year' => 2019,
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Space Gray', 'Silver', 'Gold', 'Midnight Green'],
                'base_price' => 5799.00,
                'b2b_discount' => 300.00,
                'cost_price' => 4800.00,
                'screen' => '6.5" Super Retina XDR',
                'processor' => 'A13 Bionic',
                'camera' => '12MP + 12MP + 12MP',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone 12 mini - 2020
            [
                'model' => 'iPhone 12 mini',
                'year' => 2020,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Black', 'White', 'Blue', 'Green', 'Purple', 'Red'],
                'base_price' => 4199.00,
                'b2b_discount' => 200.00,
                'cost_price' => 3500.00,
                'screen' => '5.4" Super Retina XDR',
                'processor' => 'A14 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 15h de conversação'
            ],

            // iPhone 12 - 2020
            [
                'model' => 'iPhone 12',
                'year' => 2020,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Black', 'White', 'Blue', 'Green', 'Purple', 'Red'],
                'base_price' => 4799.00,
                'b2b_discount' => 200.00,
                'cost_price' => 4000.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A14 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 17h de conversação'
            ],

            // iPhone 12 Pro - 2020
            [
                'model' => 'iPhone 12 Pro',
                'year' => 2020,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Graphite', 'Silver', 'Gold', 'Pacific Blue'],
                'base_price' => 6199.00,
                'b2b_discount' => 300.00,
                'cost_price' => 5200.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A14 Bionic',
                'camera' => '12MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 17h de conversação'
            ],

            // iPhone 12 Pro Max - 2020
            [
                'model' => 'iPhone 12 Pro Max',
                'year' => 2020,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Graphite', 'Silver', 'Gold', 'Pacific Blue'],
                'base_price' => 6699.00,
                'b2b_discount' => 300.00,
                'cost_price' => 5700.00,
                'screen' => '6.7" Super Retina XDR',
                'processor' => 'A14 Bionic',
                'camera' => '12MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone 13 mini - 2021
            [
                'model' => 'iPhone 13 mini',
                'year' => 2021,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Blue', 'Midnight', 'Starlight', 'Red'],
                'base_price' => 4499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 3800.00,
                'screen' => '5.4" Super Retina XDR',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 17h de conversação'
            ],

            // iPhone 13 - 2021
            [
                'model' => 'iPhone 13',
                'year' => 2021,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Blue', 'Midnight', 'Starlight', 'Red'],
                'base_price' => 5099.00,
                'b2b_discount' => 200.00,
                'cost_price' => 4300.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 19h de conversação'
            ],

            // iPhone 13 Pro - 2021
            [
                'model' => 'iPhone 13 Pro',
                'year' => 2021,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Graphite', 'Gold', 'Silver', 'Alpine Green', 'Sierra Blue'],
                'base_price' => 6499.00,
                'b2b_discount' => 300.00,
                'cost_price' => 5500.00,
                'screen' => '6.1" Super Retina XDR com ProMotion',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 22h de conversação'
            ],

            // iPhone 13 Pro Max - 2021
            [
                'model' => 'iPhone 13 Pro Max',
                'year' => 2021,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Graphite', 'Gold', 'Silver', 'Alpine Green', 'Sierra Blue'],
                'base_price' => 6999.00,
                'b2b_discount' => 300.00,
                'cost_price' => 6000.00,
                'screen' => '6.7" Super Retina XDR com ProMotion',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 28h de conversação'
            ],

            // iPhone 14 - 2022
            [
                'model' => 'iPhone 14',
                'year' => 2022,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Blue', 'Purple', 'Midnight', 'Starlight', 'Red', 'Yellow'],
                'base_price' => 5499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 4700.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone 14 Plus - 2022
            [
                'model' => 'iPhone 14 Plus',
                'year' => 2022,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Blue', 'Purple', 'Midnight', 'Starlight', 'Red', 'Yellow'],
                'base_price' => 5999.00,
                'b2b_discount' => 200.00,
                'cost_price' => 5200.00,
                'screen' => '6.7" Super Retina XDR',
                'processor' => 'A15 Bionic',
                'camera' => '12MP + 12MP',
                'battery' => 'Até 26h de conversação'
            ],

            // iPhone 14 Pro - 2022
            [
                'model' => 'iPhone 14 Pro',
                'year' => 2022,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Deep Purple', 'Gold', 'Silver', 'Space Black'],
                'base_price' => 6999.00,
                'b2b_discount' => 300.00,
                'cost_price' => 6000.00,
                'screen' => '6.1" Super Retina XDR com ProMotion',
                'processor' => 'A16 Bionic',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 23h de conversação'
            ],

            // iPhone 14 Pro Max - 2022
            [
                'model' => 'iPhone 14 Pro Max',
                'year' => 2022,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Deep Purple', 'Gold', 'Silver', 'Space Black'],
                'base_price' => 7499.00,
                'b2b_discount' => 300.00,
                'cost_price' => 6500.00,
                'screen' => '6.7" Super Retina XDR com ProMotion',
                'processor' => 'A16 Bionic',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 29h de conversação'
            ],

            // iPhone 15 - 2023
            [
                'model' => 'iPhone 15',
                'year' => 2023,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 5999.00,
                'b2b_discount' => 200.00,
                'cost_price' => 5200.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A16 Bionic',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone 15 Plus - 2023
            [
                'model' => 'iPhone 15 Plus',
                'year' => 2023,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 6499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 5700.00,
                'screen' => '6.7" Super Retina XDR',
                'processor' => 'A16 Bionic',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 26h de conversação'
            ],

            // iPhone 15 Pro - 2023
            [
                'model' => 'iPhone 15 Pro',
                'year' => 2023,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium'],
                'base_price' => 7499.00,
                'b2b_discount' => 300.00,
                'cost_price' => 6500.00,
                'screen' => '6.1" Super Retina XDR com ProMotion',
                'processor' => 'A17 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 23h de conversação'
            ],

            // iPhone 15 Pro Max - 2023
            [
                'model' => 'iPhone 15 Pro Max',
                'year' => 2023,
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium'],
                'base_price' => 7999.00,
                'b2b_discount' => 300.00,
                'cost_price' => 7000.00,
                'screen' => '6.7" Super Retina XDR com ProMotion',
                'processor' => 'A17 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 29h de conversação'
            ],

            // iPhone 16 - 2024
            [
                'model' => 'iPhone 16',
                'year' => 2024,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 6499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 5700.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A18',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 20h de conversação'
            ],

            // iPhone 16 Plus - 2024
            [
                'model' => 'iPhone 16 Plus',
                'year' => 2024,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 6999.00,
                'b2b_discount' => 200.00,
                'cost_price' => 6200.00,
                'screen' => '6.7" Super Retina XDR',
                'processor' => 'A18',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 26h de conversação'
            ],

            // iPhone 16 Pro - 2024
            [
                'model' => 'iPhone 16 Pro',
                'year' => 2024,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'White Titanium', 'Black Titanium', 'Rose Titanium'],
                'base_price' => 7999.00,
                'b2b_discount' => 300.00,
                'cost_price' => 7000.00,
                'screen' => '6.1" Super Retina XDR com ProMotion',
                'processor' => 'A18 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 23h de conversação'
            ],

            // iPhone 16 Pro Max - 2024
            [
                'model' => 'iPhone 16 Pro Max',
                'year' => 2024,
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'White Titanium', 'Black Titanium', 'Rose Titanium'],
                'base_price' => 8499.00,
                'b2b_discount' => 300.00,
                'cost_price' => 7500.00,
                'screen' => '6.7" Super Retina XDR com ProMotion',
                'processor' => 'A18 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 29h de conversação'
            ],

            // iPhone 17 - 2025 (Futuro - preços estimados)
            [
                'model' => 'iPhone 17',
                'year' => 2025,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 6999.00,
                'b2b_discount' => 200.00,
                'cost_price' => 6200.00,
                'screen' => '6.1" Super Retina XDR',
                'processor' => 'A19',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 21h de conversação'
            ],

            // iPhone 17 Plus - 2025
            [
                'model' => 'iPhone 17 Plus',
                'year' => 2025,
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Pink', 'Yellow', 'Green', 'Blue', 'Black'],
                'base_price' => 7499.00,
                'b2b_discount' => 200.00,
                'cost_price' => 6700.00,
                'screen' => '6.7" Super Retina XDR',
                'processor' => 'A19',
                'camera' => '48MP + 12MP',
                'battery' => 'Até 27h de conversação'
            ],

            // iPhone 17 Pro - 2025
            [
                'model' => 'iPhone 17 Pro',
                'year' => 2025,
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'White Titanium', 'Black Titanium', 'Rose Titanium'],
                'base_price' => 8499.00,
                'b2b_discount' => 300.00,
                'cost_price' => 7500.00,
                'screen' => '6.1" Super Retina XDR com ProMotion',
                'processor' => 'A19 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 24h de conversação'
            ],

            // iPhone 17 Pro Max - 2025
            [
                'model' => 'iPhone 17 Pro Max',
                'year' => 2025,
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'White Titanium', 'Black Titanium', 'Rose Titanium'],
                'base_price' => 8999.00,
                'b2b_discount' => 300.00,
                'cost_price' => 8000.00,
                'screen' => '6.7" Super Retina XDR com ProMotion',
                'processor' => 'A19 Pro',
                'camera' => '48MP + 12MP + 12MP + LiDAR',
                'battery' => 'Até 30h de conversação'
            ],
        ];

        $productCount = 0;

        // Criar produtos para cada modelo
        foreach ($iphones as $iphone) {
            foreach ($iphone['storages'] as $storage) {
                foreach ($iphone['colors'] as $color) {
                    // Calcular preços baseados no armazenamento
                    $storageMultiplier = match($storage) {
                        '64GB' => 1.0,
                        '128GB' => 1.1,
                        '256GB' => 1.25,
                        '512GB' => 1.5,
                        '1TB' => 1.8,
                        default => 1.0
                    };

                    $finalPrice = $iphone['base_price'] * $storageMultiplier;
                    $finalB2BPrice = $finalPrice - $iphone['b2b_discount'];
                    $finalCostPrice = $iphone['cost_price'] * $storageMultiplier;

                    // Gerar SKU único
                    $colorCode = match($color) {
                        'Space Gray', 'Midnight', 'Black', 'Black Titanium' => 'BK',
                        'Silver', 'Starlight', 'White', 'White Titanium' => 'WT',
                        'Gold' => 'GD',
                        'Blue', 'Pacific Blue', 'Blue Titanium' => 'BL',
                        'Green', 'Alpine Green' => 'GR',
                        'Purple', 'Deep Purple' => 'PR',
                        'Pink' => 'PK',
                        'Yellow' => 'YL',
                        'Red' => 'RD',
                        'Coral' => 'CR',
                        'Midnight Green' => 'MG',
                        'Sierra Blue' => 'SB',
                        'Rose Titanium' => 'RS',
                        'Natural Titanium' => 'NT',
                        default => 'XX'
                    };

                    $storageCode = str_replace('GB', '', $storage);
                    $modelCode = str_replace(['iPhone ', ' ', '+'], ['', '', ''], $iphone['model']);
                    $sku = $modelCode . '-' . $storageCode . '-' . $colorCode;

                    $productName = $iphone['model'] . ' ' . $storage . ' ' . $color;

                    Product::create([
                        'name' => $productName,
                        'slug' => Str::slug($productName),
                        'description' => $iphone['model'] . ' (' . $iphone['year'] . ') com ' . $iphone['screen'] . ', chip ' . $iphone['processor'] . ' e sistema de câmera ' . $iphone['camera'] . '. Bateria com ' . $iphone['battery'] . '.',
                        'sku' => $sku,
                        'price' => round($finalPrice, 2),
                        'b2b_price' => round($finalB2BPrice, 2),
                        'cost_price' => round($finalCostPrice, 2),
                        'stock_quantity' => rand(5, 30),
                        'min_stock' => 3,
                        'manage_stock' => true,
                        'in_stock' => true,
                        'is_active' => true,
                        'is_featured' => in_array($iphone['model'], ['iPhone 16 Pro', 'iPhone 16 Pro Max', 'iPhone 17 Pro', 'iPhone 17 Pro Max']),
                        'brand' => 'Apple',
                        'model' => $iphone['model'],
                        'specifications' => [
                            'Ano de Lançamento' => $iphone['year'],
                            'Tela' => $iphone['screen'],
                            'Processador' => $iphone['processor'],
                            'Armazenamento' => $storage,
                            'Câmera' => $iphone['camera'],
                            'Bateria' => $iphone['battery'],
                            'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.3',
                            'Sistema Operacional' => 'iOS 18',
                            'Peso' => 'Aproximadamente 174g',
                            'Dimensões' => 'Varia conforme modelo'
                        ],
                        'weight' => 0.174,
                        'sort_order' => $iphone['year'] - 2010, // Para ordenar por ano
                    ])->categories()->attach($smartphoneCategory->id);

                    $productCount++;
                }
            }
        }

        $this->command->info("✅ {$productCount} iPhones cadastrados com sucesso!");
        $this->command->info("📱 Modelos: iPhone X ao iPhone 17");
        $this->command->info("🎨 Todas as cores e capacidades disponíveis");
        $this->command->info("💰 Preços B2B e B2C configurados");
    }
}
