<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class MissingiPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar categoria de smartphones
        $smartphoneCategory = Category::where('slug', 'smartphones')->first();

        if (!$smartphoneCategory) {
            $this->command->error('Categoria Smartphones não encontrada!');
            return;
        }

        // Modelos que faltaram
        $missingModels = [
            // iPhone SE (2ª geração) - 2020
            [
                'model' => 'iPhone SE (2ª geração)',
                'year' => 2020,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Black', 'White', 'Red'],
                'base_price' => 2799.00,
                'b2b_discount' => 150.00,
                'cost_price' => 2300.00,
                'screen' => '4.7" Retina HD',
                'processor' => 'A13 Bionic',
                'camera' => '12MP',
                'battery' => 'Até 13h de conversação'
            ],

            // iPhone SE (3ª geração) - 2022
            [
                'model' => 'iPhone SE (3ª geração)',
                'year' => 2022,
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Midnight', 'Starlight', 'Red'],
                'base_price' => 3199.00,
                'b2b_discount' => 150.00,
                'cost_price' => 2700.00,
                'screen' => '4.7" Retina HD',
                'processor' => 'A15 Bionic',
                'camera' => '12MP',
                'battery' => 'Até 15h de conversação'
            ],
        ];

        $productCount = 0;

        // Criar produtos para cada modelo
        foreach ($missingModels as $iphone) {
            foreach ($iphone['storages'] as $storage) {
                foreach ($iphone['colors'] as $color) {
                    // Calcular preços baseados no armazenamento
                    $storageMultiplier = match($storage) {
                        '64GB' => 1.0,
                        '128GB' => 1.1,
                        '256GB' => 1.25,
                        default => 1.0
                    };

                    $finalPrice = $iphone['base_price'] * $storageMultiplier;
                    $finalB2BPrice = $finalPrice - $iphone['b2b_discount'];
                    $finalCostPrice = $iphone['cost_price'] * $storageMultiplier;

                    // Gerar SKU único
                    $colorCode = match($color) {
                        'Black', 'Midnight' => 'BK',
                        'White', 'Starlight' => 'WT',
                        'Red' => 'RD',
                        default => 'XX'
                    };

                    $storageCode = str_replace('GB', '', $storage);
                    $modelCode = match($iphone['model']) {
                        'iPhone SE (2ª geração)' => 'SE2',
                        'iPhone SE (3ª geração)' => 'SE3',
                        default => 'XX'
                    };
                    
                    $sku = $modelCode . '-' . $storageCode . '-' . $colorCode;
                    $productName = $iphone['model'] . ' ' . $storage . ' ' . $color;

                    Product::create([
                        'name' => $productName,
                        'slug' => Str::slug($productName),
                        'description' => $iphone['model'] . ' (' . $iphone['year'] . ') com ' . $iphone['screen'] . ', chip ' . $iphone['processor'] . ' e câmera ' . $iphone['camera'] . '. Bateria com ' . $iphone['battery'] . '.',
                        'sku' => $sku,
                        'price' => round($finalPrice, 2),
                        'b2b_price' => round($finalB2BPrice, 2),
                        'cost_price' => round($finalCostPrice, 2),
                        'stock_quantity' => rand(5, 25),
                        'min_stock' => 3,
                        'manage_stock' => true,
                        'in_stock' => true,
                        'is_active' => true,
                        'is_featured' => false,
                        'brand' => 'Apple',
                        'model' => $iphone['model'],
                        'specifications' => [
                            'Ano de Lançamento' => $iphone['year'],
                            'Tela' => $iphone['screen'],
                            'Processador' => $iphone['processor'],
                            'Armazenamento' => $storage,
                            'Câmera' => $iphone['camera'],
                            'Bateria' => $iphone['battery'],
                            'Conectividade' => '4G LTE, Wi-Fi, Bluetooth',
                            'Sistema Operacional' => 'iOS 15+',
                            'Peso' => 'Aproximadamente 148g',
                            'Dimensões' => '138.4 x 67.3 x 7.3 mm'
                        ],
                        'weight' => 0.148,
                        'sort_order' => $iphone['year'] - 2010,
                    ])->categories()->attach($smartphoneCategory->id);

                    $productCount++;
                }
            }
        }

        $this->command->info("✅ {$productCount} iPhones SE adicionados com sucesso!");
        $this->command->info("📱 Modelos: iPhone SE (2ª e 3ª geração)");
        $this->command->info("🎨 Todas as cores e capacidades disponíveis");
    }
}
