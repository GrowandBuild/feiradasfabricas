<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Str;

class OppoProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $department = Department::where('slug', 'eletronicos')->first();
        $category = Category::where('slug', 'smartphones')->first();
        
        if (!$department) {
            $this->command->error('Departamento de eletrônicos não encontrado!');
            return;
        }
        
        if (!$category) {
            $this->command->error('Categoria de smartphones não encontrada!');
            return;
        }

        $products = [
            [
                'name' => 'Smartphone Oppo Find X9',
                'slug' => Str::slug('Smartphone Oppo Find X9'),
                'description' => 'Oppo Find X9 com tela OLED de 6.78" com tecnologia LTPO para economia de bateria. Processador MediaTek Dimensity 9500 de última geração. Sistema de câmeras triplas de 50MP para fotos profissionais. Carregamento rápido de 80W com fio e 50W sem fio.',
                'short_description' => 'Find X9 - OLED LTPO 6.78", Dimensity 9500, câmeras 50MP',
                'sku' => 'OPPO-FINDX9',
                'price' => 3499.00,
                'b2b_price' => 3099.00,
                'cost_price' => 2200.00,
                'stock_quantity' => 8,
                'min_stock' => 2,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Oppo',
                'model' => 'Find X9',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" OLED LTPO 120Hz',
                    'Processador' => 'MediaTek Dimensity 9500',
                    'RAM' => '12GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '50MP Triple Camera System',
                    'Câmera Frontal' => '32MP',
                    'Bateria' => '5000mAh com 80W',
                    'Carregamento Wireless' => '50W',
                    'Sistema' => 'ColorOS 14 (Android 14)',
                    'Conectividade' => '5G, Wi-Fi 7, Bluetooth 5.4',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.199,
                'length' => 16.5,
                'width' => 7.6,
                'height' => 0.83,
                'sort_order' => 1,
            ],
            [
                'name' => 'Smartphone Oppo Find X8 Ultra',
                'slug' => Str::slug('Smartphone Oppo Find X8 Ultra'),
                'description' => 'Oppo Find X8 Ultra flagship premium com Snapdragon 8 Elite, 16GB de RAM e até 1TB de armazenamento. Tela AMOLED LTPO de 6.82" com 120Hz. Sistema de câmeras triplas de 50MP com zoom periscópico. Bateria de 5500mAh com carregamento de 100W e wireless.',
                'short_description' => 'Find X8 Ultra - Snapdragon 8 Elite, 16GB RAM, 1TB, zoom periscópico',
                'sku' => 'OPPO-FINDX8U',
                'price' => 4999.00,
                'b2b_price' => 4499.00,
                'cost_price' => 3200.00,
                'stock_quantity' => 5,
                'min_stock' => 1,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Oppo',
                'model' => 'Find X8 Ultra',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => 'কর6.82" AMOLED LTPO 120Hz',
                    'Processador' => 'Snapdragon 8 Elite',
                    'RAM' => '16GB',
                    'Armazenamento' => '1TB',
                    'Câmera Principal' => '50MP Triple + Periscopic Zoom',
                    'Câmera Frontal' => '32MP',
                    'Bateria' => '5500mAh com 100W',
                    'Carregamento Wireless' => '50W',
                    'Sistema' => 'ColorOS 14 (Android 14)',
                    'Conectividade' => '5G, Wi-Fi 7, Bluetooth 5.4',
                    'Cor' => 'Prata Titanium'
                ],
                'weight' => 0.221,
                'length' => 16.8,
                'width' => 7.7,
                'height' => 0.92,
                'sort_order' => 2,
            ],
            [
                'name' => 'Smartphone Oppo Reno 11f',
                'slug' => Str::slug('Smartphone Oppo Reno 11f'),
                'description' => 'Oppo Reno 11f com tela AMOLED de 6.7" e taxa de atualização de 120Hz. Processador MediaTek Dimensity 7050, 8GB de RAM e 256GB de armazenamento. Câmera principal de 64MP e residentia de 5000mAh com carregamento rápido de 67W.',
                'short_description' => 'Reno 11f - AMOLED 120Hz, Dimensity 7050, 64MP, 67W',
                'sku' => 'OPPO-RENO11F',
                'price' => 1799.00,
                'b2b_price' => 1549.00,
                'cost_price' => 1100.00,
                'stock_quantity' => 14,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Oppo',
                'model' => 'Reno 11f',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" AMOLED 120Hz',
                    'Processador' => 'MediaTek Dimensity 7050',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '64MP',
                    'Câmera Frontal' => '32MP',
                    'Bateria' => '5000mAh com 67W',
                    'Sistema' => 'ColorOS 14 (Android 14)',
                    'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.3',
                    'Cor' => 'Rosa'
                ],
                'weight' => 0.195,
                'length' => 16.2,
                'width' => 7.4,
                'height' => 0.81,
                'sort_order' => 3,
            ],
            [
                'name' => 'Smartphone Oppo Reno 8 5G',
                'slug' => Str::slug('Smartphone Oppo Reno 8 5G'),
                'description' => 'Oppo Reno 8 5G com tela de 6.4" e tecnologia 5G. Sistema de câmeras tripla de 50MP + 8MP + 2MP. Câmera frontal de 32MP para selfies incríveis. Bateria de 4500mAh com carregamento ultrarrápido de 80W.',
                'short_description' => 'Reno 8 5G - Câmera tripla 50MP, 80W carregamento',
                'sku' => 'OPPO-RENO8-5G',
                'price' => 1499.00,
                'b2b_price' => 1299.00,
                'cost_price' => 900.00,
                'stock_quantity' => 16,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Oppo',
                'model' => 'Reno 8 5G',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.4" AMOLED 90Hz',
                    'Processador' => 'MediaTek Dimensity 1300',
                    'RAM' => '8GB',
                    'Armazenamento' => '128GB',
                    'Câmera Principal' => '50MP + 8MP + 2MP',
                    'Câmera Frontal' => '32MP',
                    'Bateria' => '4500mAh com 80W',
                    'Sistema' => 'ColorOS 12 (Android 12)',
                    'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.2',
                    'Cor' => 'Azul'
                ],
                'weight' => 0.183,
                'length' => 16.0,
                'width' => 7.3,
                'height' => 0.78,
                'sort_order' => 4,
            ],
            [
                'name' => 'Smartphone Oppo A60',
                'slug' => Str::slug('Smartphone Oppo A60'),
                'description' => 'Oppo A60 com tela LCD de 6.67" e taxa de atualização de 90Hz. Processador Snapdragon 6s Gen1, 8GB de RAM e 256GB de armazenamento. Câmera principal de 50MP e bateria de longa duração de 5100mAh com carregamento de 45W.',
                'short_description' => 'A60 - LCD 90Hz, Snapdragon 6s, 50MP, 5100mAh',
                'sku' => 'OPPO-A60',
                'price' => 999.00,
                'b2b_price' => 849.00,
                'cost_price' => 650.00,
                'stock_quantity' => 20,
                'min_stock' => 4,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Oppo',
                'model' => 'A60',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.67" LCD 90Hz',
                    'Processador' => 'Snapdragon 6s Gen1',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '50MP',
                    'Câmera Frontal' => '8MP',
                    'Bateria' => '5100mAh com 45W',
                    'Sistema' => 'ColorOS 14 (Android 14)',
                    'Conectividade' => '4G, Wi-Fi 5, Bluetooth 5.1',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.192,
                'length' => 16.5,
                'width' => 7.6,
                'height' => 0.81,
                'sort_order' => 5,
            ],
            [
                'name' => 'Smartphone Oppo A58',
                'slug' => Str::slug('Smartphone Oppo A58'),
                'description' => 'Oppo A58 com tela LCD FHD+ de 6.72" e taxa de atualização de 90Hz. Processador MediaTek Helio G85, 8GB de RAM e 256GB de armazenamento. Câmera principal de 50MP e bateria de 5000mAh com carregamento de 33W.',
                'short_description' => 'A58 - LCD FHD+ 90Hz, Helio G85, 50MP, 5000mAh',
                'sku' => 'OPPO-A58',
                'price' => 849.00,
                'b2b_price' => 699.00,
                'cost_price' => 550.00,
                'stock_quantity' => 22,
                'min_stock' => 4,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Oppo',
                'model' => 'A58',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.72" LCD FHD+ 90Hz',
                    'Processador' => 'MediaTek Helio G85',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '50MP',
                    'Câmera Frontal' => '8MP',
                    'Bateria' => '5000mAh com 33W',
                    'Sistema' => 'ColorOS 13 (Android 13)',
                    'Conectividade' => '4G, Wi-Fi 5, Bluetooth 5.1',
                    'Cor' => 'Verde'
                ],
                'weight' => 0.188,
                'length' => 16.4,
                'width' => 7.5,
                'height' => 0.80,
                'sort_order' => 6,
            ],
            [
                'name' => 'Smartphone Oppo A18',
                'slug' => Str::slug('Smartphone Oppo A18'),
                'description' => 'Oppo A18 econômico com tela de 6.56" e boa duração de bateria de 5000mAh. Processador MediaTek Helio G85, 4GB de RAM e 128GB de armazenamento. Câmera dupla de 8MP e ideal para uso básico.',
                'short_description' => 'A18 - Econômico, Helio G85, 5000mAh',
                'sku' => 'OPPO-A18',
                'price' => 649.00,
                'b2b_price' => 549.00,
                'cost_price' => 450.00,
                'stock_quantity' => 25,
                'min_stock' => 5,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Oppo',
                'model' => 'A18',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.56" LCD HD+',
                    'Processador' => 'MediaTek Helio G85',
                    'RAM' => '4GB',
                    'Armazenamento' => '128GB',
                    'Câmera Principal' => '8MP Dupla',
                    'Câmera Frontal' => '5MP',
                    'Bateria' => '5000mAh',
                    'Sistema' => 'ColorOS 13.1 (Android 13)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Azul'
                ],
                'weight' => 0.186,
                'length' => 16.3,
                'width' => 7.5,
                'height' => 0.81,
                'sort_order' => 7,
            ],
        ];

        $count = 0;
        foreach ($products as $productData) {
            $product = Product::create($productData);
            $product->categories()->attach($category->id);
            $count++;
        }

        $this->command->info("✅ {$count} produtos Oppo cadastrados com sucesso!");
    }
}

