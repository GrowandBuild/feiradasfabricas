<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class RealProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Primeiro, vamos garantir que as categorias existam
        $this->createCategories();
        
        $products = [
            // SMARTPHONES
            [
                'name' => 'iPhone 14 Pro - 128GB',
                'slug' => 'iphone-14-pro-128gb',
                'description' => 'O iPhone 14 Pro com chip A16 Bionic, câmera principal de 48MP, tela Super Retina XDR de 6.1" e sistema de câmera Pro. Disponível em várias cores.',
                'short_description' => 'iPhone 14 Pro com chip A16 Bionic e câmera de 48MP.',
                'sku' => 'IPH14-PRO-128',
                'price' => 8999.00,
                'b2b_price' => 8499.00,
                'stock_quantity' => 0,
                'in_stock' => false,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPhone 14 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1592750475339-74b7b21085ab?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.1" Super Retina XDR',
                    'Chip' => 'A16 Bionic',
                    'Câmera' => '48MP Principal',
                    'Armazenamento' => '128GB',
                    'Sistema' => 'iOS 16'
                ],
                'weight' => 0.206,
                'sort_order' => 1,
                'category' => 'smartphones'
            ],
            [
                'name' => 'iPhone 15 Pro Max - 256GB',
                'slug' => 'iphone-15-pro-max-256gb',
                'description' => 'O iPhone 15 Pro Max com chip A17 Pro, câmera principal de 48MP com zoom óptico de 5x, tela Super Retina XDR de 6.7" e design em titânio.',
                'short_description' => 'iPhone 15 Pro Max com chip A17 Pro e câmera de 48MP.',
                'sku' => 'IPH15-PROM-256',
                'price' => 9999.00,
                'b2b_price' => 9499.00,
                'stock_quantity' => 15,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro Max',
                'images' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1695048133143-2b9a20484d257?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" Super Retina XDR',
                    'Chip' => 'A17 Pro',
                    'Câmera' => '48MP Principal + 12MP Ultra Wide + 12MP Telephoto',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'iOS 17',
                    'Material' => 'Titânio'
                ],
                'weight' => 0.221,
                'sort_order' => 1,
                'category' => 'smartphones'
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra - 512GB',
                'slug' => 'samsung-galaxy-s24-ultra-512gb',
                'description' => 'Smartphone Samsung Galaxy S24 Ultra com tela Dynamic AMOLED 2X de 6.8", chip Snapdragon 8 Gen 3, câmera de 200MP e S Pen incluído.',
                'short_description' => 'Galaxy S24 Ultra com S Pen e câmera de 200MP.',
                'sku' => 'SAMS-S24U-512',
                'price' => 7999.00,
                'b2b_price' => 7599.00,
                'stock_quantity' => 20,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Samsung',
                'model' => 'Galaxy S24 Ultra',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1511707171635-5f897ff02aa9?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.8" Dynamic AMOLED 2X',
                    'Chip' => 'Snapdragon 8 Gen 3',
                    'Câmera' => '200MP Principal + 50MP Periscope + 10MP Telephoto + 12MP Ultra Wide',
                    'Armazenamento' => '512GB',
                    'Sistema' => 'Android 14',
                    'S Pen' => 'Incluído'
                ],
                'weight' => 0.232,
                'sort_order' => 2,
                'category' => 'smartphones'
            ],
            [
                'name' => 'Xiaomi 14 Pro - 256GB',
                'slug' => 'xiaomi-14-pro-256gb',
                'description' => 'Smartphone Xiaomi 14 Pro com tela LTPO AMOLED de 6.73", chip Snapdragon 8 Gen 3, câmera Leica de 50MP e carregamento rápido de 120W.',
                'short_description' => 'Xiaomi 14 Pro com câmera Leica e carregamento de 120W.',
                'sku' => 'XIAOMI-14P-256',
                'price' => 4599.00,
                'b2b_price' => 4299.00,
                'stock_quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Xiaomi',
                'model' => '14 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1592899677978-9c10ca588bbd?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.73" LTPO AMOLED',
                    'Chip' => 'Snapdragon 8 Gen 3',
                    'Câmera' => '50MP Leica Principal + 50MP Ultra Wide + 50MP Telephoto',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'HyperOS',
                    'Carregamento' => '120W'
                ],
                'weight' => 0.210,
                'sort_order' => 3,
                'category' => 'smartphones'
            ],
            [
                'name' => 'Motorola Edge 50 Pro - 256GB',
                'slug' => 'motorola-edge-50-pro-256gb',
                'description' => 'Smartphone Motorola Edge 50 Pro com tela pOLED de 6.7", chip Snapdragon 7 Gen 3, câmera de 50MP e carregamento rápido de 125W.',
                'short_description' => 'Motorola Edge 50 Pro com carregamento ultrarrápido de 125W.',
                'sku' => 'MOTO-E50P-256',
                'price' => 2999.00,
                'b2b_price' => 2799.00,
                'stock_quantity' => 18,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Motorola',
                'model' => 'Edge 50 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1511707171635-5f897ff02aa9?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.7" pOLED 144Hz',
                    'Chip' => 'Snapdragon 7 Gen 3',
                    'Câmera' => '50MP Principal + 13MP Ultra Wide + 10MP Telephoto',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'Android 14',
                    'Carregamento' => '125W'
                ],
                'weight' => 0.186,
                'sort_order' => 4,
                'category' => 'smartphones'
            ],
            [
                'name' => 'Tecno Camon 30 Pro - 256GB',
                'slug' => 'tecno-camon-30-pro-256gb',
                'description' => 'Smartphone Tecno Camon 30 Pro com tela AMOLED de 6.78", chip MediaTek Dimensity 8200 Ultimate, câmera de 50MP e bateria de 5000mAh.',
                'short_description' => 'Tecno Camon 30 Pro com câmera de 50MP e tela AMOLED.',
                'sku' => 'TECNO-C30P-256',
                'price' => 1599.00,
                'b2b_price' => 1499.00,
                'stock_quantity' => 30,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Tecno',
                'model' => 'Camon 30 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1592899677978-9c10ca588bbd?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '6.78" AMOLED 120Hz',
                    'Chip' => 'MediaTek Dimensity 8200 Ultimate',
                    'Câmera' => '50MP Principal + 50MP Ultra Wide + 2MP Macro',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'Android 14',
                    'Bateria' => '5000mAh'
                ],
                'weight' => 0.195,
                'sort_order' => 5,
                'category' => 'smartphones'
            ],

            // ÁUDIO
            [
                'name' => 'JBL Charge 5 - Caixa de Som Bluetooth',
                'slug' => 'jbl-charge-5-caixa-som-bluetooth',
                'description' => 'A JBL Charge 5 é uma caixa de som Bluetooth com bateria de longa duração, resistente à água IP67 e som potente com graves profundos.',
                'short_description' => 'Caixa de som Bluetooth resistente à água com som potente.',
                'sku' => 'JBL-CH5-001',
                'price' => 699.90,
                'b2b_price' => 599.90,
                'stock_quantity' => 40,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'JBL',
                'model' => 'Charge 5',
                'images' => [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Bluetooth' => '5.1',
                    'Bateria' => '20 horas',
                    'Resistência' => 'IP67',
                    'Peso' => '960g',
                    'Dimensões' => '223 x 87 x 94 mm',
                    'Potência' => '40W'
                ],
                'weight' => 0.960,
                'sort_order' => 6,
                'category' => 'audio'
            ],
            [
                'name' => 'Sony WH-1000XM5 - Fone de Ouvido',
                'slug' => 'sony-wh-1000xm5-fone-ouvido',
                'description' => 'Fone de ouvido Sony WH-1000XM5 com cancelamento de ruído líder da indústria, qualidade de som excepcional e bateria de 30 horas.',
                'short_description' => 'Fone de ouvido com cancelamento de ruído e bateria de 30h.',
                'sku' => 'SONY-WH1000XM5',
                'price' => 1899.00,
                'b2b_price' => 1699.00,
                'stock_quantity' => 15,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Sony',
                'model' => 'WH-1000XM5',
                'images' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Bluetooth' => '5.2',
                    'Bateria' => '30 horas',
                    'Cancelamento de Ruído' => 'Sim',
                    'Peso' => '250g',
                    'Drivers' => '30mm',
                    'Resposta de Frequência' => '4Hz-40kHz'
                ],
                'weight' => 0.250,
                'sort_order' => 7,
                'category' => 'audio'
            ],
            [
                'name' => 'Apple AirPods Pro (2ª geração)',
                'slug' => 'apple-airpods-pro-2gen',
                'description' => 'Apple AirPods Pro com cancelamento ativo de ruído, áudio espacial e carregamento sem fio. Chip H2 para melhor qualidade de som.',
                'short_description' => 'AirPods Pro com cancelamento de ruído e áudio espacial.',
                'sku' => 'APPLE-APP2-001',
                'price' => 2299.00,
                'b2b_price' => 2099.00,
                'stock_quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'AirPods Pro 2',
                'images' => [
                    'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Chip' => 'H2',
                    'Bateria' => '6h + 24h (case)',
                    'Cancelamento de Ruído' => 'Sim',
                    'Áudio Espacial' => 'Sim',
                    'Resistência' => 'IPX4',
                    'Carregamento' => 'Sem fio + Lightning'
                ],
                'weight' => 0.056,
                'sort_order' => 8,
                'category' => 'audio'
            ],

            // ACESSÓRIOS
            [
                'name' => 'Capa Apple iPhone 15 Pro - Silicone',
                'slug' => 'capa-apple-iphone-15-pro-silicone',
                'description' => 'Capa oficial Apple em silicone para iPhone 15 Pro. Proteção premium com acabamento macio e suave ao toque.',
                'short_description' => 'Capa oficial Apple em silicone para iPhone 15 Pro.',
                'sku' => 'APPLE-CAP-IP15P',
                'price' => 399.00,
                'b2b_price' => 349.00,
                'stock_quantity' => 50,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Apple',
                'model' => 'Capa Silicone',
                'images' => [
                    'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1556656794-08538906a9f8?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Material' => 'Silicone',
                    'Compatibilidade' => 'iPhone 15 Pro',
                    'Cores' => 'Diversas',
                    'Proteção' => 'Traseira e laterais',
                    'MagSafe' => 'Compatível',
                    'Garantia' => '1 ano'
                ],
                'weight' => 0.030,
                'sort_order' => 9,
                'category' => 'acessorios'
            ],
            [
                'name' => 'Carregador Samsung 25W Super Fast',
                'slug' => 'carregador-samsung-25w-super-fast',
                'description' => 'Carregador Samsung 25W Super Fast para carregamento ultrarrápido. Compatível com Galaxy S24, S23 e outros dispositivos Samsung.',
                'short_description' => 'Carregador Samsung 25W Super Fast para Galaxy.',
                'sku' => 'SAMS-CAR-25W',
                'price' => 199.00,
                'b2b_price' => 169.00,
                'stock_quantity' => 60,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Samsung',
                'model' => '25W Super Fast',
                'images' => [
                    'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1558618048-3c8c76ca7d13?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Potência' => '25W',
                    'Compatibilidade' => 'Galaxy S24, S23, A54, A34',
                    'Cabo' => 'USB-C para USB-C',
                    'Proteções' => 'Sobrecarga, curto-circuito',
                    'Certificação' => 'Samsung Original',
                    'Garantia' => '1 ano'
                ],
                'weight' => 0.080,
                'sort_order' => 10,
                'category' => 'acessorios'
            ],

            // TABLETS
            [
                'name' => 'iPad Air (5ª geração) - 256GB',
                'slug' => 'ipad-air-5gen-256gb',
                'description' => 'iPad Air com chip M1, tela Liquid Retina de 10.9", compatível com Apple Pencil (2ª geração) e Magic Keyboard.',
                'short_description' => 'iPad Air com chip M1 e tela Liquid Retina 10.9".',
                'sku' => 'APPLE-IPA5-256',
                'price' => 4999.00,
                'b2b_price' => 4699.00,
                'stock_quantity' => 12,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPad Air 5',
                'images' => [
                    'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1544244016-0df4b3ffc6b0?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '10.9" Liquid Retina',
                    'Chip' => 'M1',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'iPadOS 17',
                    'Apple Pencil' => 'Compatível (2ª geração)',
                    'Magic Keyboard' => 'Compatível'
                ],
                'weight' => 0.461,
                'sort_order' => 11,
                'category' => 'tablets'
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 - 128GB',
                'slug' => 'samsung-galaxy-tab-s9-128gb',
                'description' => 'Tablet Samsung Galaxy Tab S9 com tela Dynamic AMOLED 2X de 11", chip Snapdragon 8 Gen 2, S Pen incluído e resistente à água.',
                'short_description' => 'Galaxy Tab S9 com S Pen e tela Dynamic AMOLED.',
                'sku' => 'SAMS-TABS9-128',
                'price' => 3999.00,
                'b2b_price' => 3699.00,
                'stock_quantity' => 8,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Samsung',
                'model' => 'Galaxy Tab S9',
                'images' => [
                    'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1544244016-0df4b3ffc6b0?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '11" Dynamic AMOLED 2X',
                    'Chip' => 'Snapdragon 8 Gen 2',
                    'Armazenamento' => '128GB',
                    'Sistema' => 'Android 13',
                    'S Pen' => 'Incluído',
                    'Resistência' => 'IP68'
                ],
                'weight' => 0.498,
                'sort_order' => 12,
                'category' => 'tablets'
            ],

            // NOTEBOOKS
            [
                'name' => 'MacBook Air M2 - 256GB',
                'slug' => 'macbook-air-m2-256gb',
                'description' => 'MacBook Air com chip M2, tela Liquid Retina de 13.6", 8GB RAM e design ultrafino. Perfeito para produtividade e criatividade.',
                'short_description' => 'MacBook Air com chip M2 e tela Liquid Retina 13.6".',
                'sku' => 'APPLE-MBA-M2-256',
                'price' => 8999.00,
                'b2b_price' => 8499.00,
                'stock_quantity' => 10,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'MacBook Air M2',
                'images' => [
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1541807085-5c52b6b3adef?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '13.6" Liquid Retina',
                    'Chip' => 'M2',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB SSD',
                    'Sistema' => 'macOS Ventura',
                    'Bateria' => 'Até 18 horas'
                ],
                'weight' => 1.24,
                'sort_order' => 13,
                'category' => 'notebooks'
            ],
            [
                'name' => 'Samsung Galaxy Book3 Pro - 512GB',
                'slug' => 'samsung-galaxy-book3-pro-512gb',
                'description' => 'Notebook Samsung Galaxy Book3 Pro com tela AMOLED de 14", chip Intel Core i7, 16GB RAM e design premium.',
                'short_description' => 'Galaxy Book3 Pro com tela AMOLED e chip Intel i7.',
                'sku' => 'SAMS-GB3P-512',
                'price' => 6999.00,
                'b2b_price' => 6599.00,
                'stock_quantity' => 6,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Samsung',
                'model' => 'Galaxy Book3 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1541807085-5c52b6b3adef?w=500&h=500&fit=crop'
                ],
                'specifications' => [
                    'Tela' => '14" AMOLED 2880x1800',
                    'Chip' => 'Intel Core i7-1360P',
                    'RAM' => '16GB',
                    'Armazenamento' => '512GB SSD',
                    'Sistema' => 'Windows 11',
                    'Placa de Vídeo' => 'Intel Iris Xe'
                ],
                'weight' => 1.17,
                'sort_order' => 14,
                'category' => 'notebooks'
            ]
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);
            
            $product = Product::create($productData);
            
            // Associar produto à categoria
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $product->categories()->attach($category->id);
            }
        }
    }

    private function createCategories()
    {
        $categories = [
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Os melhores smartphones do mercado',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Áudio',
                'slug' => 'audio',
                'description' => 'Fones, caixas de som e equipamentos de áudio',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Acessórios',
                'slug' => 'acessorios',
                'description' => 'Acessórios para seus dispositivos',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tablets',
                'slug' => 'tablets',
                'description' => 'Tablets para trabalho e entretenimento',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Notebooks',
                'slug' => 'notebooks',
                'description' => 'Notebooks para todas as necessidades',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
}
