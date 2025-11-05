<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductGallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar categoria de smartphones
        $smartphoneCategory = Category::where('slug', 'smartphones')->first();
        
        if (!$smartphoneCategory) {
            $smartphoneCategory = Category::create([
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Smartphones e acessórios',
                'is_active' => true,
                'sort_order' => 1
            ]);
        }

        // Produtos com múltiplas imagens para teste da galeria
        $products = [
            [
                'name' => 'Samsung Galaxy S24 Ultra - 512GB - Galeria',
                'slug' => 'samsung-galaxy-s24-ultra-512gb-galeria',
                'description' => 'Smartphone Samsung Galaxy S24 Ultra com tela Dynamic AMOLED 2X de 6.8", chip Snapdragon 8 Gen 3, câmera de 200MP e S Pen incluído.',
                'short_description' => 'O mais avançado smartphone Samsung com S Pen e câmera de 200MP',
                'sku' => 'SAMS-S24U-512-GAL',
                'price' => 7999.00,
                'b2b_price' => 6999.00,
                'stock_quantity' => 20,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Samsung',
                'model' => 'Galaxy S24 Ultra',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=800&h=800&fit=crop&crop=center'
                ],
                'specifications' => [
                    'Tela' => '6.8" Dynamic AMOLED 2X',
                    'Processador' => 'Snapdragon 8 Gen 3',
                    'RAM' => '12GB',
                    'Armazenamento' => '512GB',
                    'Câmera Principal' => '200MP',
                    'Bateria' => '5000mAh'
                ]
            ],
            [
                'name' => 'iPhone 15 Pro Max - 256GB - Galeria',
                'slug' => 'iphone-15-pro-max-256gb-galeria',
                'description' => 'iPhone 15 Pro Max com chip A17 Pro, câmera de 48MP, tela Super Retina XDR de 6.7" e sistema de câmera Pro.',
                'short_description' => 'O iPhone mais avançado com chip A17 Pro e câmera Pro',
                'sku' => 'APLE-15PM-256-GAL',
                'price' => 8999.00,
                'b2b_price' => 7999.00,
                'stock_quantity' => 15,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro Max',
                'images' => [
                    'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&h=800&fit=crop&crop=center'
                ],
                'specifications' => [
                    'Tela' => '6.7" Super Retina XDR',
                    'Processador' => 'A17 Pro',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '48MP',
                    'Bateria' => '4422mAh'
                ]
            ],
            [
                'name' => 'Xiaomi 14 Pro - 512GB - Galeria',
                'slug' => 'xiaomi-14-pro-512gb-galeria',
                'description' => 'Xiaomi 14 Pro com chip Snapdragon 8 Gen 3, câmera Leica de 50MP, tela LTPO AMOLED de 6.73" e carregamento rápido de 120W.',
                'short_description' => 'Flagship Xiaomi com câmera Leica e carregamento ultrarrápido',
                'sku' => 'XIAO-14P-512-GAL',
                'price' => 4999.00,
                'b2b_price' => 4299.00,
                'stock_quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Xiaomi',
                'model' => '14 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800&h=800&fit=crop&crop=center'
                ],
                'specifications' => [
                    'Tela' => '6.73" LTPO AMOLED',
                    'Processador' => 'Snapdragon 8 Gen 3',
                    'RAM' => '12GB',
                    'Armazenamento' => '512GB',
                    'Câmera Principal' => '50MP Leica',
                    'Carregamento' => '120W'
                ]
            ],
            [
                'name' => 'Motorola Edge 50 Pro - 256GB - Galeria',
                'slug' => 'motorola-edge-50-pro-256gb-galeria',
                'description' => 'Motorola Edge 50 Pro com câmera de 50MP com OIS, tela pOLED de 6.7", chip Snapdragon 7 Gen 3 e carregamento rápido de 125W.',
                'short_description' => 'Motorola Edge 50 Pro com câmera de 50MP e carregamento ultrarrápido',
                'sku' => 'MOTO-EDGE50-256-GAL',
                'price' => 3499.00,
                'b2b_price' => 2999.00,
                'stock_quantity' => 18,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Motorola',
                'model' => 'Edge 50 Pro',
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop&crop=center',
                    'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&h=800&fit=crop&crop=center'
                ],
                'specifications' => [
                    'Tela' => '6.7" pOLED',
                    'Processador' => 'Snapdragon 7 Gen 3',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB',
                    'Câmera Principal' => '50MP com OIS',
                    'Carregamento' => '125W'
                ]
            ]
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );

            // Associar com a categoria de smartphones
            if (!$product->categories()->where('category_id', $smartphoneCategory->id)->exists()) {
                $product->categories()->attach($smartphoneCategory->id);
            }
        }

        $this->command->info('Produtos com galeria de imagens criados com sucesso!');
    }
}
