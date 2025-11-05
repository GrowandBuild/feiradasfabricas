<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Str;

class InfinixProductsSeeder extends Seeder
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
                'name' => 'Smartphone Infinix Note 50s 5G',
                'slug' => Str::slug('Smartphone Infinix Note 50s 5G'),
                'description' => 'Smartphone Infinix Note 50s 5G com tela curva 3D de 6.78", taxa de atualização de 144Hz para experiência visual fluida. Equipado com 8GB de RAM e 256GB de armazenamento interno. Bateria de 5200mAh com carregamento rápido de 45W. Processador de última geração para alta performance.',
                'short_description' => 'Infinix Note 50s 5G - Tela curva 144Hz, 8GB RAM, 256GB, 5200mAh',
                'sku' => 'INF-NOTE50S-5G',
                'price' => 1499.00,
                'b2b_price' => 1299.00,
                'cost_price' => 900.00,
                'stock_quantity' => 15,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Note 50s 5G',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" Curved 144Hz',
                    'Processador' => 'MediaTek Dimensity 720',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Bateria' => '5200mAh com 45W',
                    'Câmera Principal' => '108MP',
                    'Sistema' => 'Android 14 (XOS)',
                    'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.2',
                    'Cor' => 'Cinza'
                ],
                'weight' => 0.195,
                'length' => 16.4,
                'width' => 7.6,
                'height' => 0.8,
                'sort_order' => 1,
            ],
            [
                'name' => 'Smartphone Infinix Note 50 Pro NFC',
                'slug' => Str::slug('Smartphone Infinix Note 50 Pro NFC'),
                'description' => 'Infinix Note 50 Pro NFC com 12GB de RAM e 256GB de armazenamento, oferecendo máxima performance. Bateria de 5200mAh com carregamento ultrarrápido de 90W. Tela AMOLED de 6.78". NFC para pagamentos contactless. Sistema XOS 13 baseado em Android 13.',
                'short_description' => 'Note 50 Pro NFC - 12GB RAM, 256GB, carregamento 90W, NFC',
                'sku' => 'INF-NOTE50P-NFC',
                'price' => 1899.00,
                'b2b_price' => 1649.00,
                'cost_price' => 1100.00,
                'stock_quantity' => 12,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Note 50 Pro NFC',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" AMOLED 120Hz',
                    'Processador' => 'MediaTek Helio G99',
                    'RAM' => '12GB (extensível até 24GB)',
                    'Armazenamento' => '256GB',
                    'Bateria' => '5200mAh com 90W',
                    'Câmera Principal' => '108MP AI Triple',
                    'Sistema' => 'XOS 13 (Android 13)',
                    'Conectividade' => 'NFC, Wi-Fi, Bluetooth 5.3',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.210,
                'length' => 16.5,
                'width' => 7.6,
                'height' => 0.85,
                'sort_order' => 2,
            ],
            [
                'name' => 'Smartphone Infinix Hot 60 Pro+',
                'slug' => Str::slug('Smartphone Infinix Hot 60 Pro+'),
                'description' => 'Infinix Hot 60 Pro+ com tela curva 3D AMOLED de 6.78", resolução 1.5K e taxa de atualização de 144Hz. 8GB de RAM e 256GB de armazenamento. Bateria de 5160mAh com carregamento rápido de 45W. Ideal para gamers e usuários que buscam alta performance.',
                'short_description' => 'Hot 60 Pro+ - AMOLED 1.5K 144Hz, 8GB RAM, 256GB',
                'sku' => 'INF-HOT60PP',
                'price' => 1699.00,
                'b2b_price' => 1449.00,
                'cost_price' => 1000.00,
                'stock_quantity' => 10,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Hot 60 Pro+',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" AMOLED Curved 1.5K 144Hz',
                    'Processador' => 'MediaTek Helio G99',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Bateria' => '5160mAh com 45W',
                    'Câmera Principal' => '108MP',
                    'Sistema' => 'XOS 13 (Android 13)',
                    'Conectividade' => 'Wi-Fi, Bluetooth 5.2',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.205,
                'length' => 16.5,
                'width' => 7.6,
                'height' => 0.82,
                'sort_order' => 3,
            ],
            [
                'name' => 'Smartphone Infinix GT 30 Pro 5G',
                'slug' => Str::slug('Smartphone Infinix GT 30 Pro 5G'),
                'description' => 'Infinix GT 30 Pro 5G, smartphone gamer com 12GB de RAM e 512GB de armazenamento. Kit gamer incluído. Design e performance otimizados para jogos. Tela de alta taxa de atualização. Bateria de longa duração.',
                'short_description' => 'GT 30 Pro 5G - 12GB RAM, 512GB, kit gamer incluído',
                'sku' => 'INF-GT30P-5G',
                'price' => 2199.00,
                'b2b_price' => 1899.00,
                'cost_price' => 1300.00,
                'stock_quantity' => 8,
                'min_stock' => 2,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'GT 30 Pro 5G',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" AMOLED 120Hz',
                    'Processador' => 'MediaTek Dimensity 8050',
                    'RAM' => '12GB',
                    'Armazenamento' => '512GB',
                    'Bateria' => '5000mAh com 67W',
                    'Câmera Principal' => '108MP',
                    'Sistema' => 'XOS 13 (Android 13)',
                    'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.2',
                    'Acessórios' => 'Kit gamer incluído',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.215,
                'length' => 16.5,
                'width' => 7.6,
                'height' => 0.88,
                'sort_order' => 4,
            ],
            [
                'name' => 'Smartphone Infinix HOT 50 Pro Free Fire',
                'slug' => Str::slug('Smartphone Infinix HOT 50 Pro Free Fire'),
                'description' => 'Smartphone oficial do Free Fire. Infinix HOT 50 Pro com edição especial Free Fire, otimizado para jogos. Melhorias em performance, design e tecnologia para gamers. Edição especial com design temático do jogo.',
                'short_description' => 'HOT 50 Pro Free Fire - Edição especial, otimizado para jogos',
                'sku' => 'INF-HOT50P-FF',
                'price' => 1399.00,
                'b2b_price' => 1199.00,
                'cost_price' => 850.00,
                'stock_quantity' => 18,
                'min_stock' => 4,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'HOT 50 Pro Free Fire',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" LCD 120Hz',
                    'Processador' => 'MediaTek Helio G99',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Bateria' => '5000mAh com 33W',
                    'Câmera Principal' => '108MP',
                    'Sistema' => 'XOS 13 (Android 13)',
                    'Edição' => 'Free Fire Special Edition',
                    'Acessórios' => 'Inclui case Free Fire',
                    'Cor' => 'Azul Free Fire'
                ],
                'weight' => 0.202,
                'length' => 16.3,
                'width' => 7.5,
                'height' => 0.8,
                'sort_order' => 5,
            ],
            [
                'name' => 'Smartphone Infinix Smart 8 Pro',
                'slug' => Str::slug('Smartphone Infinix Smart 8 Pro'),
                'description' => 'Infinix Smart 8 Pro, o melhor custo-benefício. Desempenho sólido, tela fluida, câmeras com inteligência artificial e amplo armazenamento. Ideal para quem busca qualidade a preço acessível.',
                'short_description' => 'Smart 8 Pro - Melhor custo-benefício, câmeras AI',
                'sku' => 'INF-SMART8P',
                'price' => 899.00,
                'b2b_price' => 749.00,
                'cost_price' => 550.00,
                'stock_quantity' => 25,
                'min_stock' => 5,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Smart 8 Pro',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.6" HD+',
                    'Processador' => 'Unisoc Tiger T606',
                    'RAM' => '4GB',
                    'Armazenamento' => '128GB',
                    'Bateria' => '5000mAh',
                    'Câmera Principal' => '50MP AI',
                    'Sistema' => 'XOS 13 (Android 13)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Azul'
                ],
                'weight' => 0.185,
                'length' => 16.2,
                'width' => 7.5,
                'height' => 0.78,
                'sort_order' => 6,
            ],
            [
                'name' => 'Smartphone Infinix Note 40 5G',
                'slug' => Str::slug('Smartphone Infinix Note 40 5G'),
                'description' => 'Infinix Note 40 5G com tela AMOLED de 6.78", taxa de atualização de 120Hz. Processador Dimensity 7020. Destaque para o carregamento sem fio MagCharge de 15W. 2 anos de atualizações Android e 36 meses de patches de segurança.',
                'short_description' => 'Note 40 5G - AMOLED 120Hz, MagCharge 15W wireless',
                'sku' => 'INF-NOTE40-5G',
                'price' => 1299.00,
                'b2b_price' => 1099.00,
                'cost_price' => 800.00,
                'stock_quantity' => 14,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Note 40 5G',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" AMOLED 120Hz',
                    'Processador' => 'MediaTek Dimensity 7020',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Bateria' => '5000mAh com 45W',
                    'Carregamento Wireless' => 'MagCharge 15W',
                    'Câmera Principal' => '108MP OIS',
                    'Sistema' => 'XOS 14 (Android 14)',
                    'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.2',
                    'Suporte' => '2 anos Android + 36 meses patches',
                    'Cor' => 'Verde'
                ],
                'weight' => 0.200,
                'length' => 16.4,
                'width' => 7.6,
                'height' => 0.8,
                'sort_order' => 7,
            ],
            [
                'name' => 'Smartphone Infinix Smart 9',
                'slug' => Str::slug('Smartphone Infinix Smart 9'),
                'description' => 'Infinix Smart 9 com excelente custo-benefício. Boa tela HD+, bateria de longa duração de 5000mAh e preço acessível. Ideal para quem busca smartphone básico, funcional e comportável.',
                'short_description' => 'Smart 9 - Custo-benefício, bateria longa duração',
                'sku' => 'INF-SMART9',
                'price' => 699.00,
                'b2b_price' => 579.00,
                'cost_price' => 450.00,
                'stock_quantity' => 30,
                'min_stock' => 5,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Infinix',
                'model' => 'Smart 9',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.6" HD+',
                    'Processador' => 'MediaTek Helio G25',
                    'RAM' => '3GB',
                    'Armazenamento' => '64GB',
                    'Bateria' => '5000mAh',
                    'Câmera Principal' => '13MP AI',
                    'Sistema' => 'XOS 12 (Android 12)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.180,
                'length' => 16.0,
                'width' => 7.5,
                'height' => 0.75,
                'sort_order' => 8,
            ],
            [
                'name' => 'Smartphone Infinix HOT 11s',
                'slug' => Str::slug('Smartphone Infinix HOT 11s'),
                'description' => 'Infinix HOT 11s com tela IPS LCD de 6.78" Full HD+ e taxa de atualização de 90Hz para fluidez visual. Processador MediaTek Helio G88, 6GB de RAM e 128GB de armazenamento. Câmera principal de 50MP e bateria de 5000mAh com carregamento rápido de 18W.',
                'short_description' => 'HOT 11s - Tela Full HD+ 90Hz, Helio G88, 6GB RAM',
                'sku' => 'INF-HOT11S',
                'price' => 949.00,
                'b2b_price' => 799.00,
                'cost_price' => 600.00,
                'stock_quantity' => 22,
                'min_stock' => 4,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'HOT 11s',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e cadmium7cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" IPS LCD Full HD+ 90Hz',
                    'Processador' => 'MediaTek Helio G88',
                    'RAM' => '6GB',
                    'Armazenamento' => '128GB (expansível)',
                    'Bateria' => '5000mAh com 18W',
                    'Câmera Principal' => '50MP f/1.6',
                    'Câmera Frontal' => '8MP',
                    'Sistema' => 'XOS 10 (Android 11)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Azul'
                ],
                'weight' => 0.198,
                'length' => 16.4,
                'width' => 7.5,
                'height' => 0.82,
                'sort_order' => 9,
            ],
            [
                'name' => 'Smartphone Infinix Note 10 Pro',
                'slug' => Str::slug('Smartphone Infinix Note 10 Pro'),
                'description' => 'Infinix Note 10 Pro com tela de 6.95" e taxa de atualização de 90Hz. Processador MediaTek Helio G95, 8GB de RAM e 128GB de armazenamento. Câmera principal de 64MP e bateria de longa duração.',
                'short_description' => 'Note 10 Pro - Tela 6.95", Helio G95, 8GB RAM',
                'sku' => 'INF-NOTE10P',
                'price' => 1199.00,
                'b2b_price' => 999.00,
                'cost_price' => 700.00,
                'stock_quantity' => 16,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Note 10 Pro',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.95" IPS LCD 90Hz',
                    'Processador' => 'MediaTek Helio G95',
                    'RAM' => '8GB',
                    'Armazenamento' => '128GB',
                    'Bateria' => '5000mAh com 33W',
                    'Câmera Principal' => '64MP',
                    'Sistema' => 'XOS 7.6 (Android 11)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Preto'
                ],
                'weight' => 0.205,
                'length' => 17.0,
                'width' => 7.7,
                'height' => 0.85,
                'sort_order' => 10,
            ],
            [
                'name' => 'Smartphone Infinix Zero X Pro',
                'slug' => Str::slug('Smartphone Infinix Zero X Pro'),
                'description' => 'Infinix Zero X Pro com tela AMOLED de 6.67" e taxa de atualização de 120Hz. Processador MediaTek Helio G95, 8GB de RAM e 128GB de armazenamento. Câmera principal de 108MP para fotos incríveis.',
                'short_description' => 'Zero X Pro - AMOLED 120Hz, câmera 108MP',
                'sku' => 'INF-ZEROXP',
                'price' => 1599.00,
                'b2b_price' => 1349.00,
                'cost_price' => 950.00,
                'stock_quantity' => 10,
                'min_stock' => 2,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Infinix',
                'model' => 'Zero X Pro',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-160178455144uzzles-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.67" AMOLED 120Hz',
                    'Processador' => 'MediaTek Helio G95',
                    'RAM' => '8GB',
                    'Armazenamento' => '128GB',
                    'Bateria' => '4500mAh com 45W',
                    'Câmera Principal' => '108MP',
                    'Sistema' => 'XOS 7.6 (Android 11)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.1',
                    'Cor' => 'Prata'
                ],
                'weight' => 0.193,
                'length' => 16.4,
                'width' => 7.5,
                'height' => 0.82,
                'sort_order' => 11,
            ],
            [
                'name' => 'Smartphone Infinix HOT 10S',
                'slug' => Str::slug('Smartphone Infinix HOT 10S'),
                'description' => 'Infinix HOT 10S com tela de 6.82" e taxa de atualização de 90Hz. Processador MediaTek Helio G85, 6GB de RAM e 128GB de armazenamento. Câmera principal de 48MP e bateria duradoura.',
                'short_description' => 'HOT 10S - Tela 90Hz, Helio G85, 6GB RAM',
                'sku' => 'INF-HOT10S',
                'price' => 849.00,
                'b2b_price' => 699.00,
                'cost_price' => 550.00,
                'stock_quantity' => 20,
                'min_stock' => 4,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Infinix',
                'model' => 'HOT 10S',
                'department_id' => $department->id,
                'images' => [
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.82" IPS LCD 90Hz',
                    'Processador' => 'MediaTek Helio G85',
                    'RAM' => '6GB',
                    'Armazenamento' => '128GB',
                    'Bateria' => '5000mAh com 18W',
                    'Câmera Principal' => '48MP',
                    'Sistema' => 'XOS 7.6 (Android 10)',
                    'Conectividade' => '4G, Wi-Fi, Bluetooth 5.0',
                    'Cor' => 'Verde'
                ],
                'weight' => 0.195,
                'length' => 16.4,
                'width' => 7.5,
                'height' => 0.80,
                'sort_order' => 12,
            ],
        ];

        $count = 0;
        foreach ($products as $productData) {
            $product = Product::create($productData);
            $product->categories()->attach($category->id);
            $count++;
        }

        $this->command->info("✅ {$count} produtos Infinix cadastrados com sucesso!");
    }
}

