<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Str;

/**
 * Seeder de Produção - Produtos Principais
 * 
 * Este seeder contém todos os produtos principais para produção.
 * As imagens podem ser facilmente substituídas:
 * - URLs: 'https://example.com/image.jpg'
 * - Locais: 'products/iphone-14-pro.jpg' (será salvo em storage/app/public/)
 * 
 * Para adicionar novos produtos, basta adicionar ao array $products
 */
class ProductionProductsSeeder extends Seeder
{
    /**
     * Mapeamento de imagens por modelo e cor
     * 
     * FÁCIL SUBSTITUIÇÃO:
     * - URLs: Use URLs completas (https://...)
     * - Locais: Use caminhos relativos (serão salvos em storage/app/public/)
     * - Exemplo local: 'products/iphone-14-pro-space-gray.jpg'
     * 
     * Estrutura: ['modelo' => ['cor' => [array_de_imagens]]]
     */
    private function getProductImages()
    {
        return [
            // iPhone 14 Pro
            'iPhone 14 Pro' => [
                'Deep Purple' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1592750475339-74b7b21085ab?w=800&h=800&fit=crop'
                ],
                'Gold' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop'
                ],
                'Silver' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop'
                ],
                'Space Black' => [
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=800&fit=crop'
                ],
            ],
            // iPhone 15 Pro Max
            'iPhone 15 Pro Max' => [
                'Natural Titanium' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1695048133143-2b9a20484d257?w=800&h=800&fit=crop'
                ],
                'Blue Titanium' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&h=800&fit=crop'
                ],
                'White Titanium' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&h=800&fit=crop'
                ],
                'Black Titanium' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&h=800&fit=crop'
                ],
            ],
            // AirPods Pro
            'AirPods Pro 2' => [
                'default' => [
                    'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&h=800&fit=crop'
                ],
            ],
            // Capa iPhone
            'Capa Silicone' => [
                'default' => [
                    'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1556656794-08538906a9f8?w=800&h=800&fit=crop'
                ],
            ],
            // iPad Air
            'iPad Air 5' => [
                'default' => [
                    'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1544244016-0df4b3ffc6b0?w=800&h=800&fit=crop'
                ],
            ],
            // MacBook Air
            'MacBook Air M2' => [
                'default' => [
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1541807085-5c52b6b3adef?w=800&h=800&fit=crop'
                ],
            ],
            // Infinix - Série ZERO
            'Infinix ZERO' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Infinix - Série NOTE
            'Infinix NOTE' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Infinix - Série HOT
            'Infinix HOT' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Infinix - Série SMART
            'Infinix SMART' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Infinix - Série GT
            'Infinix GT' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Oppo - Série Find X
            'Oppo Find X' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Oppo - Série Reno
            'Oppo Reno' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Oppo - Série A
            'Oppo A' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Oppo - Série F
            'Oppo F' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Oppo - Série K
            'Oppo K' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Samsung - Série Galaxy S
            'Samsung Galaxy S' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Samsung - Série Galaxy Note
            'Samsung Galaxy Note' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Samsung - Série Galaxy A
            'Samsung Galaxy A' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Samsung - Série Galaxy M
            'Samsung Galaxy M' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            // Samsung - Série Galaxy Z
            'Samsung Galaxy Z' => [
                'default' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
        ];
    }

    /**
     * Obter imagens para um produto específico
     * 
     * Suporta:
     * - Modelos específicos (ex: 'iPhone 14 Pro')
     * - Séries (ex: 'Infinix ZERO', 'Infinix NOTE')
     * - Fallback para 'default'
     * 
     * @param string $model
     * @param string $color
     * @return array
     */
    private function getImagesForProduct($model, $color = 'default')
    {
        $images = $this->getProductImages();
        
        // Tentar modelo exato com cor
        if (isset($images[$model][$color])) {
            return $images[$model][$color];
        }
        
        // Tentar modelo exato com default
        if (isset($images[$model]['default'])) {
            return $images[$model]['default'];
        }
        
        // Para Infinix, tentar por série (ex: "Zero 2" -> "Infinix ZERO")
        if (strpos($model, 'Zero') === 0 || strpos($model, 'ZERO') === 0) {
            if (isset($images['Infinix ZERO']['default'])) {
                return $images['Infinix ZERO']['default'];
            }
        }
        if (strpos($model, 'Note') === 0 || strpos($model, 'NOTE') === 0) {
            if (isset($images['Infinix NOTE']['default'])) {
                return $images['Infinix NOTE']['default'];
            }
        }
        if (strpos($model, 'Hot') === 0 || strpos($model, 'HOT') === 0) {
            if (isset($images['Infinix HOT']['default'])) {
                return $images['Infinix HOT']['default'];
            }
        }
        if (strpos($model, 'Smart') === 0 || strpos($model, 'SMART') === 0) {
            if (isset($images['Infinix SMART']['default'])) {
                return $images['Infinix SMART']['default'];
            }
        }
        if (strpos($model, 'GT') === 0) {
            if (isset($images['Infinix GT']['default'])) {
                return $images['Infinix GT']['default'];
            }
        }
        
        // Para Oppo, tentar por série (ex: "Find X5" -> "Oppo Find X")
        if (strpos($model, 'Find') === 0 || strpos($model, 'FIND') === 0) {
            if (isset($images['Oppo Find X']['default'])) {
                return $images['Oppo Find X']['default'];
            }
        }
        if (strpos($model, 'Reno') === 0 || strpos($model, 'RENO') === 0) {
            if (isset($images['Oppo Reno']['default'])) {
                return $images['Oppo Reno']['default'];
            }
        }
        // Para Oppo série A (A1, A2, A3, etc.)
        if (preg_match('/^A\d+/', $model)) {
            if (isset($images['Oppo A']['default'])) {
                return $images['Oppo A']['default'];
            }
        }
        // Para Oppo série F (F1, F3, F5, etc.)
        if (preg_match('/^F\d+/', $model)) {
            if (isset($images['Oppo F']['default'])) {
                return $images['Oppo F']['default'];
            }
        }
        // Para Oppo série K (K1, K3, K5, etc.)
        if (preg_match('/^K\d+/', $model)) {
            if (isset($images['Oppo K']['default'])) {
                return $images['Oppo K']['default'];
            }
        }
        
        // Para Samsung, tentar por série (ex: "Galaxy S23" -> "Samsung Galaxy S")
        if (preg_match('/^Galaxy\s+S\d+/i', $model) || preg_match('/^S\d+/', $model)) {
            if (isset($images['Samsung Galaxy S']['default'])) {
                return $images['Samsung Galaxy S']['default'];
            }
        }
        if (preg_match('/^Galaxy\s+Note\s+\d+/i', $model) || preg_match('/^Note\s+\d+/i', $model)) {
            if (isset($images['Samsung Galaxy Note']['default'])) {
                return $images['Samsung Galaxy Note']['default'];
            }
        }
        if (preg_match('/^Galaxy\s+A\d+/i', $model) || preg_match('/^A\d+/', $model)) {
            if (isset($images['Samsung Galaxy A']['default'])) {
                return $images['Samsung Galaxy A']['default'];
            }
        }
        if (preg_match('/^Galaxy\s+M\d+/i', $model) || preg_match('/^M\d+/', $model)) {
            if (isset($images['Samsung Galaxy M']['default'])) {
                return $images['Samsung Galaxy M']['default'];
            }
        }
        if (preg_match('/^Galaxy\s+Z\d+/i', $model) || preg_match('/^Z\d+/', $model)) {
            if (isset($images['Samsung Galaxy Z']['default'])) {
                return $images['Samsung Galaxy Z']['default'];
            }
        }
        
        // Imagem padrão se não encontrar
        return ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop'];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar departamento de eletrônicos
        $department = Department::where('slug', 'eletronicos')->first();
        
        if (!$department) {
            $this->command->error('Departamento de eletrônicos não encontrado!');
            return;
        }

        // Garantir que as categorias existam
        $this->ensureCategories();

        // Buscar categorias
        $smartphoneCategory = Category::where('slug', 'smartphones')->first();
        $audioCategory = Category::where('slug', 'audio')->first();
        $acessorioCategory = Category::where('slug', 'acessorios')->first();
        $tabletCategory = Category::where('slug', 'tablets')->first();
        $notebookCategory = Category::where('slug', 'notebooks')->first();

        // Dados completos dos iPhones (do CompleteiPhoneSeeder)
        $iphones = $this->getIphoneModels();

        $productCount = 0;
        $updatedCount = 0;

        // Criar produtos iPhone base e variações
        foreach ($iphones as $iphone) {
            // Criar produto base único para este modelo
            $baseSlug = Str::slug($iphone['model']);
            $baseProduct = Product::where('slug', $baseSlug)
                ->where('brand', 'Apple')
                ->where('model', $iphone['model'])
                ->first();

            // Obter imagens do primeiro modelo/cor disponível
            $firstColor = $iphone['colors'][0] ?? 'default';
            $images = $this->getImagesForProduct($iphone['model'], $firstColor);

            if (!$baseProduct) {
                // Criar produto base
                $baseProduct = Product::create([
                    'name' => $iphone['model'],
                    'slug' => $baseSlug,
                    'description' => $iphone['model'] . ' (' . $iphone['year'] . ') com ' . $iphone['screen'] . ', chip ' . $iphone['processor'] . ' e sistema de câmera ' . $iphone['camera'] . '. Bateria com ' . $iphone['battery'] . '.',
                    'short_description' => $iphone['model'] . ' com ' . $iphone['screen'] . ' e chip ' . $iphone['processor'] . '.',
                    'sku' => 'BASE-' . str_replace(['iPhone ', ' ', '+'], ['', '-', ''], $iphone['model']),
                    'price' => round($iphone['base_price'], 2), // Preço base (será atualizado pela primeira variação)
                    'b2b_price' => round($iphone['base_price'] - $iphone['b2b_discount'], 2),
                    'cost_price' => round($iphone['cost_price'], 2),
                    'stock_quantity' => 0, // Estoque será gerenciado pelas variações
                    'min_stock' => 3,
                    'manage_stock' => false, // Produto base não gerencia estoque
                    'in_stock' => true,
                    'is_active' => true,
                    'is_featured' => in_array($iphone['model'], ['iPhone 16 Pro', 'iPhone 16 Pro Max', 'iPhone 17 Pro', 'iPhone 17 Pro Max']),
                    'brand' => 'Apple',
                    'model' => $iphone['model'],
                    'department_id' => $department->id,
                    'images' => $images,
                    'specifications' => [
                        'Ano de Lançamento' => $iphone['year'],
                        'Tela' => $iphone['screen'],
                        'Processador' => $iphone['processor'],
                        'Câmera' => $iphone['camera'],
                        'Bateria' => $iphone['battery'],
                        'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.3',
                        'Sistema Operacional' => 'iOS 18',
                    ],
                    'weight' => 0.174,
                    'sort_order' => $iphone['year'] - 2010,
                ]);

                // Associar categoria
                if ($smartphoneCategory) {
                    $baseProduct->categories()->attach($smartphoneCategory->id);
                }

                $productCount++;
            } else {
                $updatedCount++;
            }

            // Criar variações para cada combinação de storage e cor
            $variationSortOrder = 0;
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

                    // Gerar SKU único para variação
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
                        'Graphite' => 'GR',
                        'Space Black' => 'BK',
                        default => 'XX'
                    };

                    $storageCode = str_replace('GB', '', $storage);
                    $modelCode = str_replace(['iPhone ', ' ', '+'], ['', '', ''], $iphone['model']);
                    $variationSku = $modelCode . '-' . $storageCode . '-' . $colorCode;

                    // Verificar se variação já existe
                    $existingVariation = ProductVariation::where('sku', $variationSku)->first();

                    if (!$existingVariation) {
                        ProductVariation::create([
                            'product_id' => $baseProduct->id,
                            'storage' => $storage,
                            'color' => $color,
                            'sku' => $variationSku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(5, 30),
                            'in_stock' => true,
                            'is_active' => true,
                            'sort_order' => $variationSortOrder++,
                        ]);
                    }

                    // Atualizar preço base do produto para o menor preço disponível
                    if ($variationSortOrder === 1 || $baseProduct->price > $finalPrice) {
                        $baseProduct->update(['price' => round($finalPrice, 2)]);
                    }
                }
            }
        }

        // Adicionar outros produtos Apple (do RealProductsSeeder)
        $otherAppleProducts = $this->getOtherAppleProducts($department);
        
        foreach ($otherAppleProducts as $originalProductData) {
            // Extrair categoria antes de criar/atualizar produto
            $categorySlug = $originalProductData['category'] ?? null;
            
            // Remover 'category' do array antes de passar para create/update
            unset($originalProductData['category']);
            
            // Criar novo array apenas com campos permitidos usando fillable do model
            $product = new Product();
            $fillableFields = $product->getFillable();
            $productData = array_intersect_key($originalProductData, array_flip($fillableFields));
            
            $existingProduct = Product::where('sku', $productData['sku'])->first();
            
            if ($existingProduct) {
                // Usar fill() para garantir que apenas campos fillable sejam atualizados
                $existingProduct->fill($productData);
                $existingProduct->save();
                
                // Atualizar categoria se necessário
                if ($categorySlug) {
                    $category = Category::where('slug', $categorySlug)->first();
                    if ($category && !$existingProduct->categories()->where('categories.id', $category->id)->exists()) {
                        $existingProduct->categories()->attach($category->id);
                    }
                }
                $updatedCount++;
            } else {
                // Usar fill() e save() em vez de create() para garantir que apenas campos fillable sejam usados
                $newProduct = new Product();
                $newProduct->fill($productData);
                $newProduct->save();
                
                // Associar categoria
                if ($categorySlug) {
                    $category = Category::where('slug', $categorySlug)->first();
                    if ($category) {
                        $newProduct->categories()->attach($category->id);
                    }
                }
                
                $productCount++;
            }
        }

        // Adicionar produtos Samsung (todas as séries) com variações
        $this->createSamsungProductsWithVariations($department, $smartphoneCategory, $productCount, $updatedCount);
        
        // Adicionar produtos Infinix (todas as séries) com variações
        $this->createInfinixProductsWithVariations($department, $smartphoneCategory, $productCount, $updatedCount);
        
        // Adicionar produtos Oppo (todas as séries) com variações
        $this->createOppoProductsWithVariations($department, $smartphoneCategory, $productCount, $updatedCount);
        
        // Os valores de $productCount e $updatedCount são atualizados por referência na função

        $this->command->info("✅ Seeder de produtos de produção concluído!");
        $this->command->info("📦 {$productCount} novos produtos criados");
        $this->command->info("🔄 {$updatedCount} produtos atualizados");
        $this->command->info("📱 Total de produtos processados: " . ($productCount + $updatedCount));
    }

    /**
     * Obter modelos de iPhone (do CompleteiPhoneSeeder)
     */
    private function getIphoneModels()
    {
        return [
            // iPhone X (2017)
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
            // iPhone XS (2018)
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
            // iPhone XS Max (2018)
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
            // iPhone XR (2018)
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
            // iPhone 11 (2019)
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
            // iPhone 11 Pro (2019)
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
            // iPhone 11 Pro Max (2019)
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
            // iPhone 12 mini (2020)
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
            // iPhone 12 (2020)
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
            // iPhone 12 Pro (2020)
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
            // iPhone 12 Pro Max (2020)
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
            // iPhone 13 mini (2021)
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
            // iPhone 13 (2021)
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
            // iPhone 13 Pro (2021)
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
            // iPhone 13 Pro Max (2021)
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
            // iPhone 14 (2022)
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
            // iPhone 14 Plus (2022)
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
            // iPhone 14 Pro (2022)
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
            // iPhone 14 Pro Max (2022)
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
            // iPhone 15 (2023)
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
            // iPhone 15 Plus (2023)
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
            // iPhone 15 Pro (2023)
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
            // iPhone 15 Pro Max (2023)
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
            // iPhone 16 (2024)
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
            // iPhone 16 Plus (2024)
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
            // iPhone 16 Pro (2024)
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
            // iPhone 16 Pro Max (2024)
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
            // iPhone 17 (2025)
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
            // iPhone 17 Plus (2025)
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
            // iPhone 17 Pro (2025)
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
            // iPhone 17 Pro Max (2025)
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
    }

    /**
     * Obter outros produtos Apple (AirPods, iPad, MacBook, etc.)
     */
    private function getOtherAppleProducts($department)
    {
        $images = $this->getProductImages();
        
        return [
            [
                'name' => 'Apple AirPods Pro (2ª geração)',
                'slug' => 'apple-airpods-pro-2gen',
                'description' => 'Apple AirPods Pro com cancelamento ativo de ruído, áudio espacial e carregamento sem fio. Chip H2 para melhor qualidade de som.',
                'short_description' => 'AirPods Pro com cancelamento de ruído e áudio espacial.',
                'sku' => 'APPLE-APP2-001',
                'price' => 2299.00,
                'b2b_price' => 2099.00,
                'cost_price' => 1800.00,
                'stock_quantity' => 25,
                'min_stock' => 10,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'AirPods Pro 2',
                'department_id' => $department->id,
                'images' => $this->getImagesForProduct('AirPods Pro 2'),
                'specifications' => [
                    'Chip' => 'H2',
                    'Bateria' => '6h + 24h (case)',
                    'Cancelamento de Ruído' => 'Sim',
                    'Áudio Espacial' => 'Sim',
                    'Resistência' => 'IPX4',
                    'Carregamento' => 'Sem fio + Lightning'
                ],
                'weight' => 0.056,
                'sort_order' => 3,
                'category' => 'audio'
            ],
            [
                'name' => 'Capa Apple iPhone 15 Pro - Silicone',
                'slug' => 'capa-apple-iphone-15-pro-silicone',
                'description' => 'Capa oficial Apple em silicone para iPhone 15 Pro. Proteção premium com acabamento macio e suave ao toque.',
                'short_description' => 'Capa oficial Apple em silicone para iPhone 15 Pro.',
                'sku' => 'APPLE-CAP-IP15P',
                'price' => 399.00,
                'b2b_price' => 349.00,
                'cost_price' => 250.00,
                'stock_quantity' => 50,
                'min_stock' => 20,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'brand' => 'Apple',
                'model' => 'Capa Silicone',
                'department_id' => $department->id,
                'images' => $this->getImagesForProduct('Capa Silicone'),
                'specifications' => [
                    'Material' => 'Silicone',
                    'Compatibilidade' => 'iPhone 15 Pro',
                    'Cores' => 'Diversas',
                    'Proteção' => 'Traseira e laterais',
                    'MagSafe' => 'Compatível',
                    'Garantia' => '1 ano'
                ],
                'weight' => 0.030,
                'sort_order' => 4,
                'category' => 'acessorios'
            ],
            [
                'name' => 'iPad Air (5ª geração) - 256GB',
                'slug' => 'ipad-air-5gen-256gb',
                'description' => 'iPad Air com chip M1, tela Liquid Retina de 10.9", compatível com Apple Pencil (2ª geração) e Magic Keyboard.',
                'short_description' => 'iPad Air com chip M1 e tela Liquid Retina 10.9".',
                'sku' => 'APPLE-IPA5-256',
                'price' => 4999.00,
                'b2b_price' => 4699.00,
                'cost_price' => 4000.00,
                'stock_quantity' => 12,
                'min_stock' => 5,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'iPad Air 5',
                'department_id' => $department->id,
                'images' => $this->getImagesForProduct('iPad Air 5'),
                'specifications' => [
                    'Tela' => '10.9" Liquid Retina',
                    'Chip' => 'M1',
                    'Armazenamento' => '256GB',
                    'Sistema' => 'iPadOS 17',
                    'Apple Pencil' => 'Compatível (2ª geração)',
                    'Magic Keyboard' => 'Compatível'
                ],
                'weight' => 0.461,
                'sort_order' => 5,
                'category' => 'tablets'
            ],
            [
                'name' => 'MacBook Air M2 - 256GB',
                'slug' => 'macbook-air-m2-256gb',
                'description' => 'MacBook Air com chip M2, tela Liquid Retina de 13.6", 8GB RAM e design ultrafino. Perfeito para produtividade e criatividade.',
                'short_description' => 'MacBook Air com chip M2 e tela Liquid Retina 13.6".',
                'sku' => 'APPLE-MBA-M2-256',
                'price' => 8999.00,
                'b2b_price' => 8499.00,
                'cost_price' => 7500.00,
                'stock_quantity' => 10,
                'min_stock' => 3,
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'brand' => 'Apple',
                'model' => 'MacBook Air M2',
                'department_id' => $department->id,
                'images' => $this->getImagesForProduct('MacBook Air M2'),
                'specifications' => [
                    'Tela' => '13.6" Liquid Retina',
                    'Chip' => 'M2',
                    'RAM' => '8GB',
                    'Armazenamento' => '256GB SSD',
                    'Sistema' => 'macOS Ventura',
                    'Bateria' => 'Até 18 horas'
                ],
                'weight' => 1.24,
                'sort_order' => 6,
                'category' => 'notebooks'
            ],
        ];
    }

    /**
     * Obter todos os modelos Infinix organizados por série
     * 
     * Estrutura permite fácil filtro por:
     * - Série (ZERO, NOTE, HOT, SMART, GT)
     * - RAM (2GB, 3GB, 4GB, 6GB, 8GB, 12GB)
     * - Armazenamento (32GB, 64GB, 128GB, 256GB, 512GB)
     * - Variação (Pro, Plus, Lite, Play, i, etc.)
     * 
     * @param Department $department
     * @param Category $category
     * @return array
     */
    private function getInfinixModels($department, $category)
    {
        $products = [];
        $images = $this->getProductImages();
        
        // SÉRIE ZERO - Premium (8GB-12GB RAM, 128GB-512GB)
        $zeroModels = [
            ['name' => '2', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1999.00],
            ['name' => '3', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 2199.00],
            ['name' => '4', 'variations' => ['', 'Plus'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2399.00],
            ['name' => '5', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2599.00],
            ['name' => '6', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2799.00],
            ['name' => '8', 'variations' => ['', '8i'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2999.00],
            ['name' => 'X', 'variations' => ['', 'Pro', 'Neo'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3199.00],
            ['name' => '5G', 'variations' => ['', '5G (2023)'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3399.00],
            ['name' => '20', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3599.00],
            ['name' => 'Ultra', 'variations' => [''], 'ram' => ['12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3799.00],
            ['name' => '30', 'variations' => ['', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3999.00],
            ['name' => '40', 'variations' => ['', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 4199.00],
            ['name' => 'Flip', 'variations' => [''], 'ram' => ['12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 4499.00],
        ];

        // SÉRIE NOTE - Intermediária Premium (6GB-12GB RAM, 128GB-512GB)
        $noteModels = [
            ['name' => '2', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 899.00],
            ['name' => '3', 'variations' => ['', 'Pro'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 999.00],
            ['name' => '4', 'variations' => ['', 'Pro'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1099.00],
            ['name' => '5', 'variations' => ['', 'Stylus'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1199.00],
            ['name' => '6', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1299.00],
            ['name' => '7', 'variations' => ['', 'Lite'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1399.00],
            ['name' => '8', 'variations' => ['', '8i'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1499.00],
            ['name' => '10', 'variations' => ['', 'Pro'], 'ram' => ['6GB', '8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1599.00],
            ['name' => '11', 'variations' => ['', 'Pro', 'i', 'S'], 'ram' => ['6GB', '8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1699.00],
            ['name' => '12', 'variations' => ['', 'Pro', 'VIP'], 'ram' => ['6GB', '8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 1799.00],
            ['name' => '30', 'variations' => ['', 'Pro', 'VIP', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 1999.00],
            ['name' => '40', 'variations' => ['', 'Pro', 'Pro+', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2199.00],
            ['name' => '50', 'variations' => ['', 'Pro', 'Pro+', 'X 5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2399.00],
        ];

        // SÉRIE HOT - Popular Acessível (4GB-8GB RAM, 64GB-256GB)
        $hotModels = [
            ['name' => '1', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 499.00],
            ['name' => '2', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 549.00],
            ['name' => '3', 'variations' => ['', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 599.00],
            ['name' => '4', 'variations' => ['', 'Pro', 'Lite'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 649.00],
            ['name' => '5', 'variations' => ['', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 699.00],
            ['name' => '6', 'variations' => ['', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 749.00],
            ['name' => '7', 'variations' => ['', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 799.00],
            ['name' => '8', 'variations' => ['', 'Lite'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 849.00],
            ['name' => '9', 'variations' => ['', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 899.00],
            ['name' => '10', 'variations' => ['', 'Lite', 'Play', 'i', 'Pro'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 949.00],
            ['name' => '11', 'variations' => ['', 'Play', 'S', '2022'], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 999.00],
            ['name' => '12', 'variations' => ['', 'Play', 'Pro', 'i'], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1099.00],
            ['name' => '20', 'variations' => ['', 'Play', 'S', '5G', 'i'], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1199.00],
            ['name' => '30', 'variations' => ['', 'i', 'Play', '5G', 'i NFC'], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1299.00],
            ['name' => '40', 'variations' => ['', 'i', 'Pro'], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1399.00],
            ['name' => '50', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1499.00],
            ['name' => '60', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1699.00],
        ];

        // SÉRIE SMART - Básica Econômica (2GB-4GB RAM, 32GB-128GB)
        $smartModels = [
            ['name' => '2', 'variations' => ['', 'HD', 'Pro'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 299.00],
            ['name' => '3 Plus', 'variations' => [''], 'ram' => ['2GB', '3GB'], 'storage' => ['32GB', '64GB'], 'base_price' => 349.00],
            ['name' => '4', 'variations' => ['', 'C'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB'], 'base_price' => 399.00],
            ['name' => '5', 'variations' => [''], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 449.00],
            ['name' => '6', 'variations' => ['', 'HD', 'Plus'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 499.00],
            ['name' => '7', 'variations' => ['', 'HD'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 549.00],
            ['name' => '8', 'variations' => ['', 'HD', 'Pro', 'Plus'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 599.00],
            ['name' => '9', 'variations' => ['', 'HD'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 649.00],
            ['name' => '10', 'variations' => [''], 'ram' => ['3GB', '4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 699.00],
        ];

        // SÉRIE GT - Gamer (8GB-12GB RAM, 256GB-512GB)
        $gtModels = [
            ['name' => '10 Pro', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2499.00],
            ['name' => '20 Pro', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2799.00],
            ['name' => '30', 'variations' => ['', 'Pro', '5G+'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2999.00],
        ];

        // Processar Série ZERO
        foreach ($zeroModels as $model) {
            foreach ($model['variations'] as $variation) {
                foreach ($model['ram'] as $ram) {
                    foreach ($model['storage'] as $storage) {
                        // Construir nome do modelo: "Zero 2", "Zero 3 Pro", etc.
                        $modelName = 'Zero ' . $model['name'];
                        if ($variation) {
                            $modelName .= ' ' . $variation;
                        }
                        
                        $storageMultiplier = match($storage) {
                            '128GB' => 1.0,
                            '256GB' => 1.25,
                            '512GB' => 1.5,
                            default => 1.0
                        };
                        
                        $ramMultiplier = match($ram) {
                            '8GB' => 1.0,
                            '12GB' => 1.15,
                            default => 1.0
                        };
                        
                        $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                        $finalB2BPrice = $finalPrice * 0.90;
                        $finalCostPrice = $finalPrice * 0.65;
                        
                        // Gerar SKU: INF-ZERO-2-8-128, INF-ZERO-3-Pro-12-256, etc.
                        $skuBase = str_replace(['Zero ', ' ', '+', '(', ')'], ['', '-', '', '', ''], $modelName);
                        $sku = 'INF-ZERO-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);
                        $productName = 'Smartphone Infinix ' . $modelName . ' ' . $ram . ' ' . $storage;
                        
                        $products[] = [
                            'name' => $productName,
                            'slug' => Str::slug($productName),
                            'description' => 'Smartphone Infinix ' . $modelName . ' com ' . $ram . ' de RAM e ' . $storage . ' de armazenamento. Linha premium com tela AMOLED, câmeras até 200MP e carregamento ultrarrápido até 180W.',
                            'short_description' => 'Infinix ' . $modelName . ' - Linha Premium, ' . $ram . ' RAM, ' . $storage,
                            'sku' => $sku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(5, 25),
                            'min_stock' => 3,
                            'manage_stock' => true,
                            'in_stock' => true,
                            'is_active' => true,
                            'is_featured' => in_array($model['name'], ['40', '30', 'Ultra']),
                            'brand' => 'Infinix',
                            'model' => $modelName,
                            'department_id' => $department->id,
                            'images' => $this->getImagesForProduct($modelName),
                            'specifications' => [
                                'Série' => 'ZERO',
                                'RAM' => $ram,
                                'Armazenamento' => $storage,
                                'Tela' => 'AMOLED 6.7"-6.9"',
                                'Câmera' => 'Até 200MP',
                                'Carregamento' => 'Até 180W',
                                'Bateria' => '5000-6000mAh',
                                'Sistema' => 'Android (XOS)',
                                'Foco' => 'Premium - Desempenho e acabamento superior',
                            ],
                            'weight' => 0.190,
                            'sort_order' => 100,
                        ];
                    }
                }
            }
        }

        // Processar Série NOTE (similar ao ZERO, mas com preços menores)
        foreach ($noteModels as $model) {
            foreach ($model['variations'] as $variation) {
                foreach ($model['ram'] as $ram) {
                    foreach ($model['storage'] as $storage) {
                        // Construir nome do modelo: "Note 2", "Note 3 Pro", etc.
                        $modelName = 'Note ' . $model['name'];
                        if ($variation) {
                            $modelName .= ' ' . $variation;
                        }
                        
                        $storageMultiplier = match($storage) {
                            '128GB' => 1.0,
                            '256GB' => 1.25,
                            '512GB' => 1.5,
                            default => 1.0
                        };
                        
                        $ramMultiplier = match($ram) {
                            '6GB' => 1.0,
                            '8GB' => 1.15,
                            '12GB' => 1.30,
                            default => 1.0
                        };
                        
                        $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                        $finalB2BPrice = $finalPrice * 0.90;
                        $finalCostPrice = $finalPrice * 0.65;
                        
                        // Gerar SKU: INF-NOTE-2-6-128, INF-NOTE-3-Pro-8-256, etc.
                        $skuBase = str_replace(['Note ', ' ', '+', '(', ')'], ['', '-', '', '', ''], $modelName);
                        $sku = 'INF-NOTE-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);
                        $productName = 'Smartphone Infinix ' . $modelName . ' ' . $ram . ' ' . $storage;
                        
                        $products[] = [
                            'name' => $productName,
                            'slug' => Str::slug($productName),
                            'description' => 'Smartphone Infinix ' . $modelName . ' com ' . $ram . ' de RAM e ' . $storage . ' de armazenamento. Linha intermediária premium com foco em bateria (5000-6000mAh), som JBL e telas grandes (6.7"-6.9").',
                            'short_description' => 'Infinix ' . $modelName . ' - Intermediário Premium, ' . $ram . ' RAM, ' . $storage,
                            'sku' => $sku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(5, 30),
                            'min_stock' => 3,
                            'manage_stock' => true,
                            'in_stock' => true,
                            'is_active' => true,
                            'is_featured' => in_array($model['name'], ['50', '40', '30']),
                            'brand' => 'Infinix',
                            'model' => $modelName,
                            'department_id' => $department->id,
                            'images' => $this->getImagesForProduct($modelName),
                            'specifications' => [
                                'Série' => 'NOTE',
                                'RAM' => $ram,
                                'Armazenamento' => $storage,
                                'Tela' => '6.7"-6.9"',
                                'Bateria' => '5000-6000mAh',
                                'Som' => 'JBL',
                                'Carregamento' => 'Rápido ou sem fio (VIP)',
                                'Sistema' => 'Android (XOS)',
                                'Foco' => 'Intermediário Premium - Equilíbrio preço/performance',
                            ],
                            'weight' => 0.200,
                            'sort_order' => 200,
                        ];
                    }
                }
            }
        }

        // Processar Série HOT (similar, mas com preços menores)
        foreach ($hotModels as $model) {
            foreach ($model['variations'] as $variation) {
                foreach ($model['ram'] as $ram) {
                    foreach ($model['storage'] as $storage) {
                        // Construir nome do modelo: "Hot 1", "Hot 2 Pro", etc.
                        $modelName = 'Hot ' . $model['name'];
                        if ($variation) {
                            $modelName .= ' ' . $variation;
                        }
                        
                        $storageMultiplier = match($storage) {
                            '64GB' => 1.0,
                            '128GB' => 1.15,
                            '256GB' => 1.35,
                            default => 1.0
                        };
                        
                        $ramMultiplier = match($ram) {
                            '4GB' => 1.0,
                            '6GB' => 1.15,
                            '8GB' => 1.30,
                            default => 1.0
                        };
                        
                        $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                        $finalB2BPrice = $finalPrice * 0.90;
                        $finalCostPrice = $finalPrice * 0.65;
                        
                        // Gerar SKU: INF-HOT-1-4-64, INF-HOT-2-Pro-6-128, etc.
                        $skuBase = str_replace(['Hot ', ' ', '+', '(', ')'], ['', '-', '', '', ''], $modelName);
                        $sku = 'INF-HOT-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);
                        $productName = 'Smartphone Infinix ' . $modelName . ' ' . $ram . ' ' . $storage;
                        
                        $products[] = [
                            'name' => $productName,
                            'slug' => Str::slug($productName),
                            'description' => 'Smartphone Infinix ' . $modelName . ' com ' . $ram . ' de RAM e ' . $storage . ' de armazenamento. Linha popular e acessível com grande autonomia (5000-6000mAh), telas 90Hz ou 120Hz e excelente custo-benefício.',
                            'short_description' => 'Infinix ' . $modelName . ' - Popular, ' . $ram . ' RAM, ' . $storage,
                            'sku' => $sku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(10, 40),
                            'min_stock' => 5,
                            'manage_stock' => true,
                            'in_stock' => true,
                            'is_active' => true,
                            'is_featured' => in_array($model['name'], ['60', '50', '40']),
                            'brand' => 'Infinix',
                            'model' => $modelName,
                            'department_id' => $department->id,
                            'images' => $this->getImagesForProduct($modelName),
                            'specifications' => [
                                'Série' => 'HOT',
                                'RAM' => $ram,
                                'Armazenamento' => $storage,
                                'Tela' => '90Hz ou 120Hz',
                                'Bateria' => '5000-6000mAh',
                                'Sistema' => 'Android (XOS)',
                                'Foco' => 'Popular - Custo-benefício',
                            ],
                            'weight' => 0.195,
                            'sort_order' => 300,
                        ];
                    }
                }
            }
        }

        // Processar Série SMART (similar, mas com preços menores)
        foreach ($smartModels as $model) {
            foreach ($model['variations'] as $variation) {
                foreach ($model['ram'] as $ram) {
                    foreach ($model['storage'] as $storage) {
                        // Construir nome do modelo: "Smart 2", "Smart 3 HD", etc.
                        $modelName = 'Smart ' . $model['name'];
                        if ($variation) {
                            $modelName .= ' ' . $variation;
                        }
                        
                        $storageMultiplier = match($storage) {
                            '32GB' => 1.0,
                            '64GB' => 1.15,
                            '128GB' => 1.35,
                            default => 1.0
                        };
                        
                        $ramMultiplier = match($ram) {
                            '2GB' => 1.0,
                            '3GB' => 1.10,
                            '4GB' => 1.25,
                            default => 1.0
                        };
                        
                        $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                        $finalB2BPrice = $finalPrice * 0.90;
                        $finalCostPrice = $finalPrice * 0.65;
                        
                        // Gerar SKU: INF-SMART-2-2-32, INF-SMART-3-HD-3-64, etc.
                        $skuBase = str_replace(['Smart ', ' ', '+', '(', ')'], ['', '-', '', '', ''], $modelName);
                        $sku = 'INF-SMART-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);
                        $productName = 'Smartphone Infinix ' . $modelName . ' ' . $ram . ' ' . $storage;
                        
                        $products[] = [
                            'name' => $productName,
                            'slug' => Str::slug($productName),
                            'description' => 'Smartphone Infinix ' . $modelName . ' com ' . $ram . ' de RAM e ' . $storage . ' de armazenamento. Linha básica e econômica, voltada para uso leve e primeiros smartphones.',
                            'short_description' => 'Infinix ' . $modelName . ' - Básico, ' . $ram . ' RAM, ' . $storage,
                            'sku' => $sku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(15, 50),
                            'min_stock' => 5,
                            'manage_stock' => true,
                            'in_stock' => true,
                            'is_active' => true,
                            'is_featured' => false,
                            'brand' => 'Infinix',
                            'model' => $modelName,
                            'department_id' => $department->id,
                            'images' => $this->getImagesForProduct($modelName),
                            'specifications' => [
                                'Série' => 'SMART',
                                'RAM' => $ram,
                                'Armazenamento' => $storage,
                                'Bateria' => 'Boa autonomia',
                                'Sistema' => 'Android (XOS)',
                                'Foco' => 'Básico - Uso leve',
                            ],
                            'weight' => 0.180,
                            'sort_order' => 400,
                        ];
                    }
                }
            }
        }

        // Processar Série GT (similar ao ZERO, mas foco em gaming)
        foreach ($gtModels as $model) {
            foreach ($model['variations'] as $variation) {
                foreach ($model['ram'] as $ram) {
                    foreach ($model['storage'] as $storage) {
                        // Construir nome do modelo: "GT 10 Pro", "GT 20 Pro", etc.
                        $modelName = 'GT ' . $model['name'];
                        if ($variation) {
                            $modelName .= ' ' . $variation;
                        }
                        
                        $storageMultiplier = match($storage) {
                            '256GB' => 1.0,
                            '512GB' => 1.5,
                            default => 1.0
                        };
                        
                        $ramMultiplier = match($ram) {
                            '8GB' => 1.0,
                            '12GB' => 1.20,
                            default => 1.0
                        };
                        
                        $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                        $finalB2BPrice = $finalPrice * 0.90;
                        $finalCostPrice = $finalPrice * 0.65;
                        
                        // Gerar SKU: INF-GT-10Pro-8-256, INF-GT-20Pro-12-512, etc.
                        $skuBase = str_replace(['GT ', ' ', '+', '(', ')'], ['', '-', '', '', ''], $modelName);
                        $sku = 'INF-GT-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);
                        $productName = 'Smartphone Infinix ' . $modelName . ' ' . $ram . ' ' . $storage;
                        
                        $products[] = [
                            'name' => $productName,
                            'slug' => Str::slug($productName),
                            'description' => 'Smartphone Infinix ' . $modelName . ' com ' . $ram . ' de RAM e ' . $storage . ' de armazenamento. Linha gamer com chipsets MediaTek Dimensity (8050/8200 Ultra), design Cyber Mecha, refrigeração líquida e tela AMOLED 120Hz.',
                            'short_description' => 'Infinix ' . $modelName . ' - Gamer, ' . $ram . ' RAM, ' . $storage,
                            'sku' => $sku,
                            'price' => round($finalPrice, 2),
                            'b2b_price' => round($finalB2BPrice, 2),
                            'cost_price' => round($finalCostPrice, 2),
                            'stock_quantity' => rand(5, 20),
                            'min_stock' => 3,
                            'manage_stock' => true,
                            'in_stock' => true,
                            'is_active' => true,
                            'is_featured' => true,
                            'brand' => 'Infinix',
                            'model' => $modelName,
                            'department_id' => $department->id,
                            'images' => $this->getImagesForProduct($modelName),
                            'specifications' => [
                                'Série' => 'GT',
                                'RAM' => $ram,
                                'Armazenamento' => $storage,
                                'Tela' => 'AMOLED 120Hz',
                                'Processador' => 'MediaTek Dimensity 8050/8200 Ultra',
                                'Design' => 'Cyber Mecha',
                                'Refrigeração' => 'Líquida',
                                'Sistema' => 'Android (XOS)',
                                'Foco' => 'Gamer - Jogos e multitarefa pesada',
                            ],
                            'weight' => 0.205,
                            'sort_order' => 500,
                        ];
                    }
                }
            }
        }

        return $products;
    }

    /**
     * Criar produtos Infinix base com variações (RAM + Armazenamento)
     */
    private function createInfinixProductsWithVariations($department, $category, &$productCount, &$updatedCount)
    {
        // Definir séries e modelos (mesma estrutura do getInfinixModels, mas simplificada)
        $series = [
            'ZERO' => [
                'models' => [
                    ['name' => '2', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1999.00, 'is_featured' => false],
                    ['name' => '3', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 2099.00, 'is_featured' => false],
                    ['name' => '4', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2199.00, 'is_featured' => false],
                    ['name' => '5', 'variations' => ['', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2299.00, 'is_featured' => false],
                    ['name' => '6', 'variations' => ['', 'Pro', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2399.00, 'is_featured' => false],
                    ['name' => '8', 'variations' => ['', 'Pro', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2499.00, 'is_featured' => false],
                    ['name' => '10', 'variations' => ['', 'Pro', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2599.00, 'is_featured' => false],
                    ['name' => '20', 'variations' => ['', 'Pro', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2499.00, 'is_featured' => false],
                    ['name' => '30', 'variations' => ['', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2499.00, 'is_featured' => true],
                    ['name' => '40', 'variations' => ['', 'Ultra'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2699.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'ZERO', 'Tela' => 'AMOLED 6.7"-6.9"', 'Câmera' => 'Até 200MP', 'Carregamento' => 'Até 180W', 'Bateria' => '5000-6000mAh', 'Sistema' => 'Android (XOS)', 'Foco' => 'Premium - Desempenho e acabamento superior'],
                'weight' => 0.190,
                'sort_order' => 100,
            ],
            'NOTE' => [
                'models' => [
                    ['name' => '2', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 899.00, 'is_featured' => false],
                    ['name' => '3', 'variations' => ['', 'Pro'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 999.00, 'is_featured' => false],
                    ['name' => '30', 'variations' => ['', 'Pro', 'VIP', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 1999.00, 'is_featured' => true],
                    ['name' => '40', 'variations' => ['', 'Pro', 'Pro+', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2199.00, 'is_featured' => true],
                    ['name' => '50', 'variations' => ['', 'Pro', 'Pro+', 'X 5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2399.00, 'is_featured' => true],
                    ['name' => '60', 'variations' => ['', 'Pro', 'Pro+', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2499.00, 'is_featured' => true],
                    ['name' => '70', 'variations' => ['', 'Pro', 'Pro+', '5G'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2699.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'NOTE', 'Tela' => '6.7"-6.9"', 'Bateria' => '5000-6000mAh', 'Som' => 'JBL', 'Carregamento' => 'Rápido ou sem fio (VIP)', 'Sistema' => 'Android (XOS)', 'Foco' => 'Intermediário Premium - Equilíbrio preço/performance'],
                'weight' => 0.200,
                'sort_order' => 200,
            ],
            'HOT' => [
                'models' => [
                    ['name' => '1', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 499.00, 'is_featured' => false],
                    ['name' => '2', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 549.00, 'is_featured' => false],
                    ['name' => '40', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1499.00, 'is_featured' => false],
                    ['name' => '50', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1599.00, 'is_featured' => false],
                    ['name' => '60', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1699.00, 'is_featured' => true],
                    ['name' => '70', 'variations' => ['', 'i', 'Pro', 'Pro+'], 'ram' => ['8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1799.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'HOT', 'Tela' => '90Hz ou 120Hz', 'Bateria' => '5000-6000mAh', 'Sistema' => 'Android (XOS)', 'Foco' => 'Popular - Custo-benefício'],
                'weight' => 0.195,
                'sort_order' => 300,
            ],
            'SMART' => [
                'models' => [
                    ['name' => '2', 'variations' => ['', 'HD', 'Pro'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 299.00, 'is_featured' => false],
                    ['name' => '5', 'variations' => [''], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 449.00, 'is_featured' => false],
                    ['name' => '6', 'variations' => ['', 'HD', 'Pro'], 'ram' => ['2GB', '3GB', '4GB'], 'storage' => ['32GB', '64GB', '128GB'], 'base_price' => 499.00, 'is_featured' => false],
                    ['name' => '7', 'variations' => ['', 'HD', 'Pro'], 'ram' => ['3GB', '4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 549.00, 'is_featured' => false],
                    ['name' => '8', 'variations' => ['', 'HD', 'Pro'], 'ram' => ['3GB', '4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 599.00, 'is_featured' => false],
                ],
                'specs' => ['Série' => 'SMART', 'Bateria' => 'Boa autonomia', 'Sistema' => 'Android (XOS)', 'Foco' => 'Básico - Uso leve'],
                'weight' => 0.180,
                'sort_order' => 400,
            ],
            'GT' => [
                'models' => [
                    ['name' => '10 Pro', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2499.00, 'is_featured' => true],
                    ['name' => '20 Pro', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2799.00, 'is_featured' => true],
                    ['name' => '30', 'variations' => ['', 'Pro', '5G+'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 2999.00, 'is_featured' => true],
                    ['name' => '40', 'variations' => ['', 'Pro', '5G+'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 3199.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'GT', 'Tela' => 'AMOLED 120Hz', 'Processador' => 'MediaTek Dimensity 8050/8200 Ultra', 'Design' => 'Cyber Mecha', 'Refrigeração' => 'Líquida', 'Sistema' => 'Android (XOS)', 'Foco' => 'Gamer - Jogos e multitarefa pesada'],
                'weight' => 0.205,
                'sort_order' => 500,
            ],
        ];

        foreach ($series as $seriesName => $seriesData) {
            foreach ($seriesData['models'] as $model) {
                // Criar produto base único para este modelo (sem variações)
                // O nome base do modelo é apenas a série + número (ex: "NOTE 40")
                $baseModelName = $seriesName . ' ' . $model['name'];
                $baseSlug = Str::slug('Infinix ' . $baseModelName);
                
                $images = $this->getImagesForProduct($baseModelName);

                // Gerar SKU único para o produto base
                $baseSku = 'BASE-INF-' . str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $baseModelName);
                
                // Verificar se produto base já existe por SKU ou slug
                $baseProduct = Product::where(function($q) use ($baseSku, $baseSlug) {
                        $q->where('sku', $baseSku)
                          ->orWhere('slug', $baseSlug);
                    })
                    ->where('brand', 'Infinix')
                    ->where('model', $baseModelName)
                    ->first();
                
                if (!$baseProduct) {
                    // Criar produto base (apenas uma vez por modelo)
                    $baseProduct = Product::create([
                        'name' => 'Smartphone Infinix ' . $baseModelName,
                        'slug' => $baseSlug,
                        'description' => 'Smartphone Infinix ' . $baseModelName . '. Linha ' . strtolower($seriesName) . ' com ' . ($seriesData['specs']['Tela'] ?? 'tela') . ' e sistema ' . ($seriesData['specs']['Sistema'] ?? 'Android'),
                        'short_description' => 'Infinix ' . $baseModelName . ' - ' . ($seriesData['specs']['Foco'] ?? ''),
                        'sku' => $baseSku,
                        'price' => round($model['base_price'], 2),
                        'b2b_price' => round($model['base_price'] * 0.90, 2),
                        'cost_price' => round($model['base_price'] * 0.65, 2),
                        'stock_quantity' => 0,
                        'min_stock' => 3,
                        'manage_stock' => false,
                        'in_stock' => true,
                        'is_active' => true,
                        'is_featured' => $model['is_featured'] ?? false,
                        'brand' => 'Infinix',
                        'model' => $baseModelName,
                        'department_id' => $department->id,
                        'images' => $images,
                        'specifications' => $seriesData['specs'],
                        'weight' => $seriesData['weight'],
                        'sort_order' => $seriesData['sort_order'],
                    ]);

                    // Associar categoria
                    if ($category) {
                        $baseProduct->categories()->attach($category->id);
                    }

                    $productCount++;
                } else {
                    $updatedCount++;
                }

                // Criar variações de produto para cada variação de nome (Pro, Pro+, etc.) e combinação de RAM e armazenamento
                foreach ($model['variations'] as $variation) {
                    // Construir nome completo do modelo com variação: "NOTE 40 Pro", "NOTE 40 Pro+", etc.
                    $fullModelName = $baseModelName;
                    if ($variation) {
                        $fullModelName .= ' ' . $variation;
                    }

                    // Se houver variação de nome (Pro, Pro+, etc.), criar produto base separado para ela
                    if ($variation) {
                        // Tratar o "+" antes de gerar o slug para evitar duplicatas
                        // Ex: "Pro+" vira "pro-plus" em vez de "pro"
                        $slugSafeVariation = str_replace(['+', '(', ')'], ['-plus', '', ''], $variation);
                        $slugSafeModelName = $baseModelName . ' ' . $slugSafeVariation;
                        $fullModelSlug = Str::slug('Infinix ' . $slugSafeModelName);
                        
                        // Gerar SKU único para o produto com variação
                        $fullModelSku = 'BASE-INF-' . str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $fullModelName);
                        
                        // Verificar se produto já existe por SKU ou slug
                        $existingFullModelProduct = Product::where(function($q) use ($fullModelSku, $fullModelSlug) {
                                $q->where('sku', $fullModelSku)
                                  ->orWhere('slug', $fullModelSlug);
                            })
                            ->first();
                        
                        if (!$existingFullModelProduct) {
                            // Criar produto com variação
                            $fullModelProduct = Product::create([
                                'name' => 'Smartphone Infinix ' . $fullModelName,
                                'slug' => $fullModelSlug,
                                'description' => 'Smartphone Infinix ' . $fullModelName . '. Linha ' . strtolower($seriesName) . ' com ' . ($seriesData['specs']['Tela'] ?? 'tela') . ' e sistema ' . ($seriesData['specs']['Sistema'] ?? 'Android'),
                                'short_description' => 'Infinix ' . $fullModelName . ' - ' . ($seriesData['specs']['Foco'] ?? ''),
                                'sku' => $fullModelSku,
                                'price' => round($model['base_price'], 2),
                                'b2b_price' => round($model['base_price'] * 0.90, 2),
                                'cost_price' => round($model['base_price'] * 0.65, 2),
                                'stock_quantity' => 0,
                                'min_stock' => 3,
                                'manage_stock' => false,
                                'in_stock' => true,
                                'is_active' => true,
                                'is_featured' => $model['is_featured'] ?? false,
                                'brand' => 'Infinix',
                                'model' => $fullModelName,
                                'department_id' => $department->id,
                                'images' => $this->getImagesForProduct($fullModelName),
                                'specifications' => $seriesData['specs'],
                                'weight' => $seriesData['weight'],
                                'sort_order' => $seriesData['sort_order'],
                            ]);
                            
                            // Associar categoria
                            if ($category) {
                                $fullModelProduct->categories()->attach($category->id);
                            }
                            
                            $productCount++;
                            $productForVariations = $fullModelProduct;
                        } else {
                            // Produto já existe, usar o existente
                            $fullModelProduct = $existingFullModelProduct;
                            $updatedCount++;
                            $productForVariations = $fullModelProduct;
                        }
                    } else {
                        // Se não houver variação de nome, usar o produto base
                        $productForVariations = $baseProduct;
                    }
                    
                    $variationSortOrder = 0;
                    foreach ($model['ram'] as $ram) {
                        foreach ($model['storage'] as $storage) {
                            // Calcular preços
                            $storageMultiplier = match($storage) {
                                '32GB' => 1.0,
                                '64GB' => 1.15,
                                '128GB' => 1.0,
                                '256GB' => 1.25,
                                '512GB' => 1.5,
                                default => 1.0
                            };

                            $ramMultiplier = match($ram) {
                                '2GB' => 1.0,
                                '3GB' => 1.10,
                                '4GB' => 1.0,
                                '6GB' => 1.0,
                                '8GB' => 1.0,
                                '12GB' => 1.15,
                                default => 1.0
                            };

                            $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                            $finalB2BPrice = $finalPrice * 0.90;
                            $finalCostPrice = $finalPrice * 0.65;

                            // Gerar SKU para variação (usar fullModelName se houver variação, senão baseModelName)
                            $modelNameForSku = $variation ? $fullModelName : $baseModelName;
                            $skuBase = str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $modelNameForSku);
                            $variationSku = 'INF-' . $seriesName . '-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);

                            // Verificar se variação já existe
                            $existingVariation = ProductVariation::where('sku', $variationSku)->first();

                            if (!$existingVariation) {
                                ProductVariation::create([
                                    'product_id' => $productForVariations->id,
                                    'ram' => $ram,
                                    'storage' => $storage,
                                    'sku' => $variationSku,
                                    'price' => round($finalPrice, 2),
                                    'b2b_price' => round($finalB2BPrice, 2),
                                    'cost_price' => round($finalCostPrice, 2),
                                    'stock_quantity' => rand(5, 40),
                                    'in_stock' => true,
                                    'is_active' => true,
                                    'sort_order' => $variationSortOrder++,
                                ]);
                            }

                            // Atualizar preço base do produto para o menor preço disponível
                            if ($variationSortOrder === 1 || $productForVariations->price > $finalPrice) {
                                $productForVariations->update(['price' => round($finalPrice, 2)]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Criar produtos Oppo base com variações (RAM + Armazenamento)
     * Segue a mesma metodologia do Infinix
     */
    private function createOppoProductsWithVariations($department, $category, &$productCount, &$updatedCount)
    {
        // Definir séries e modelos Oppo
        $series = [
            'Find X' => [
                'models' => [
                    ['name' => 'X', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3999.00, 'is_featured' => true],
                    ['name' => 'X2', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 4299.00, 'is_featured' => true],
                    ['name' => 'X3', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 4599.00, 'is_featured' => true],
                    ['name' => 'X5', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 4999.00, 'is_featured' => true],
                    ['name' => 'X6', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 5299.00, 'is_featured' => true],
                    ['name' => 'X7', 'variations' => ['', 'Pro', 'Ultra'], 'ram' => ['12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 5599.00, 'is_featured' => true],
                    ['name' => 'X8', 'variations' => ['', 'Pro'], 'ram' => ['12GB'], 'storage' => ['256GB', '512GB'], 'base_price' => 5999.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'Find X', 'Tela' => 'AMOLED 6.7"-6.9"', 'Câmera' => 'Até 200MP', 'Carregamento' => 'Até 80W SuperVOOC', 'Bateria' => '4500-5000mAh', 'Sistema' => 'Android (ColorOS)', 'Foco' => 'Premium - Inovação e design superior'],
                'weight' => 0.200,
                'sort_order' => 100,
            ],
            'Reno' => [
                'models' => [
                    ['name' => '2', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1999.00, 'is_featured' => false],
                    ['name' => '3', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 2199.00, 'is_featured' => false],
                    ['name' => '4', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 2399.00, 'is_featured' => false],
                    ['name' => '5', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2599.00, 'is_featured' => true],
                    ['name' => '6', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2799.00, 'is_featured' => true],
                    ['name' => '7', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 2999.00, 'is_featured' => true],
                    ['name' => '8', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3199.00, 'is_featured' => true],
                    ['name' => '9', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3399.00, 'is_featured' => true],
                    ['name' => '10', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3599.00, 'is_featured' => true],
                    ['name' => '11', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3799.00, 'is_featured' => true],
                    ['name' => '12', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 3999.00, 'is_featured' => true],
                    ['name' => '13', 'variations' => ['', 'Pro'], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB', '512GB'], 'base_price' => 4199.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'Reno', 'Tela' => 'AMOLED 6.4"-6.7"', 'Câmera' => 'Até 100MP', 'Carregamento' => 'Até 67W SuperVOOC', 'Bateria' => '4500-5000mAh', 'Sistema' => 'Android (ColorOS)', 'Foco' => 'Médio-Alto - Fotografia e design'],
                'weight' => 0.195,
                'sort_order' => 200,
            ],
            'A' => [
                'models' => [
                    ['name' => '15', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 799.00, 'is_featured' => false],
                    ['name' => '17', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 899.00, 'is_featured' => false],
                    ['name' => '18', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 999.00, 'is_featured' => false],
                    ['name' => '19', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 1099.00, 'is_featured' => false],
                    ['name' => '31', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 1199.00, 'is_featured' => false],
                    ['name' => '54', 'variations' => [''], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1299.00, 'is_featured' => false],
                    ['name' => '57', 'variations' => [''], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1399.00, 'is_featured' => false],
                    ['name' => '58', 'variations' => [''], 'ram' => ['4GB', '6GB', '8GB'], 'storage' => ['64GB', '128GB', '256GB'], 'base_price' => 1499.00, 'is_featured' => false],
                    ['name' => '74', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1599.00, 'is_featured' => false],
                    ['name' => '77', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1699.00, 'is_featured' => false],
                    ['name' => '78', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1799.00, 'is_featured' => true],
                    ['name' => '79', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1899.00, 'is_featured' => true],
                    ['name' => '80', 'variations' => [''], 'ram' => ['8GB', '12GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1999.00, 'is_featured' => true],
                    ['name' => '96', 'variations' => [''], 'ram' => ['8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1999.00, 'is_featured' => true],
                    ['name' => '98', 'variations' => [''], 'ram' => ['8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 2099.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'A', 'Tela' => '6.1"-6.7"', 'Bateria' => '4000-5000mAh', 'Carregamento' => 'Até 33W', 'Sistema' => 'Android (ColorOS)', 'Foco' => 'Médio - Custo-benefício'],
                'weight' => 0.190,
                'sort_order' => 300,
            ],
            'F' => [
                'models' => [
                    ['name' => '1', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 599.00, 'is_featured' => false],
                    ['name' => '3', 'variations' => [''], 'ram' => ['4GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 699.00, 'is_featured' => false],
                    ['name' => '5', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 799.00, 'is_featured' => false],
                    ['name' => '7', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 899.00, 'is_featured' => false],
                    ['name' => '9', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 999.00, 'is_featured' => false],
                    ['name' => '11', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 1099.00, 'is_featured' => false],
                    ['name' => '15', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 1199.00, 'is_featured' => false],
                    ['name' => '17', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 1299.00, 'is_featured' => false],
                    ['name' => '19', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1399.00, 'is_featured' => false],
                    ['name' => '21', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1499.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'F', 'Tela' => '6.1"-6.4"', 'Bateria' => '4000-4500mAh', 'Carregamento' => 'Até 33W', 'Sistema' => 'Android (ColorOS)', 'Foco' => 'Entrada - Acessível e funcional'],
                'weight' => 0.185,
                'sort_order' => 400,
            ],
            'K' => [
                'models' => [
                    ['name' => '1', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 699.00, 'is_featured' => false],
                    ['name' => '3', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 799.00, 'is_featured' => false],
                    ['name' => '5', 'variations' => [''], 'ram' => ['4GB', '6GB'], 'storage' => ['64GB', '128GB'], 'base_price' => 899.00, 'is_featured' => false],
                    ['name' => '9', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1099.00, 'is_featured' => false],
                    ['name' => '10', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1199.00, 'is_featured' => false],
                    ['name' => '11', 'variations' => [''], 'ram' => ['6GB', '8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1299.00, 'is_featured' => true],
                    ['name' => '12', 'variations' => ['', 'Plus'], 'ram' => ['8GB'], 'storage' => ['128GB', '256GB'], 'base_price' => 1399.00, 'is_featured' => true],
                ],
                'specs' => ['Série' => 'K', 'Tela' => '6.1"-6.7"', 'Bateria' => '4000-5000mAh', 'Carregamento' => 'Até 33W', 'Sistema' => 'Android (ColorOS)', 'Foco' => 'Acessível - Boa performance'],
                'weight' => 0.187,
                'sort_order' => 500,
            ],
        ];

        foreach ($series as $seriesName => $seriesData) {
            foreach ($seriesData['models'] as $model) {
                // Criar produto base único para este modelo
                $baseModelName = $seriesName . ' ' . $model['name'];
                $baseSlug = Str::slug('Oppo ' . $baseModelName);
                
                $images = $this->getImagesForProduct($baseModelName);

                // Gerar SKU único para o produto base
                $baseSku = 'BASE-OPPO-' . str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $baseModelName);
                
                // Verificar se produto base já existe por SKU ou slug
                $baseProduct = Product::where(function($q) use ($baseSku, $baseSlug) {
                        $q->where('sku', $baseSku)
                          ->orWhere('slug', $baseSlug);
                    })
                    ->where('brand', 'Oppo')
                    ->where('model', $baseModelName)
                    ->first();
                
                if (!$baseProduct) {
                    // Criar produto base (apenas uma vez por modelo)
                    $baseProduct = Product::create([
                        'name' => 'Smartphone Oppo ' . $baseModelName,
                        'slug' => $baseSlug,
                        'description' => 'Smartphone Oppo ' . $baseModelName . '. Linha ' . strtolower($seriesName) . ' com ' . ($seriesData['specs']['Tela'] ?? 'tela') . ' e sistema ' . ($seriesData['specs']['Sistema'] ?? 'Android'),
                        'short_description' => 'Oppo ' . $baseModelName . ' - ' . ($seriesData['specs']['Foco'] ?? ''),
                        'sku' => $baseSku,
                        'price' => round($model['base_price'], 2),
                        'b2b_price' => round($model['base_price'] * 0.90, 2),
                        'cost_price' => round($model['base_price'] * 0.65, 2),
                        'stock_quantity' => 0,
                        'min_stock' => 3,
                        'manage_stock' => false,
                        'in_stock' => true,
                        'is_active' => true,
                        'is_featured' => $model['is_featured'] ?? false,
                        'brand' => 'Oppo',
                        'model' => $baseModelName,
                        'department_id' => $department->id,
                        'images' => $images,
                        'specifications' => $seriesData['specs'],
                        'weight' => $seriesData['weight'],
                        'sort_order' => $seriesData['sort_order'],
                    ]);

                    // Associar categoria
                    if ($category) {
                        $baseProduct->categories()->attach($category->id);
                    }

                    $productCount++;
                } else {
                    $updatedCount++;
                }

                // Criar variações de produto para cada variação de nome (Pro, etc.) e combinação de RAM e armazenamento
                foreach ($model['variations'] as $variation) {
                    // Construir nome completo do modelo com variação: "Find X5 Pro", "Reno 9 Pro", etc.
                    $fullModelName = $baseModelName;
                    if ($variation) {
                        $fullModelName .= ' ' . $variation;
                    }

                    // Se houver variação de nome (Pro, etc.), criar produto base separado para ela
                    if ($variation) {
                        $slugSafeVariation = str_replace(['+', '(', ')'], ['-plus', '', ''], $variation);
                        $slugSafeModelName = $baseModelName . ' ' . $slugSafeVariation;
                        $fullModelSlug = Str::slug('Oppo ' . $slugSafeModelName);
                        
                        // Gerar SKU único para o produto com variação
                        $fullModelSku = 'BASE-OPPO-' . str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $fullModelName);
                        
                        // Verificar se produto já existe por SKU ou slug
                        $existingFullModelProduct = Product::where(function($q) use ($fullModelSku, $fullModelSlug) {
                                $q->where('sku', $fullModelSku)
                                  ->orWhere('slug', $fullModelSlug);
                            })
                            ->first();
                        
                        if (!$existingFullModelProduct) {
                            // Criar produto com variação
                            $fullModelProduct = Product::create([
                                'name' => 'Smartphone Oppo ' . $fullModelName,
                                'slug' => $fullModelSlug,
                                'description' => 'Smartphone Oppo ' . $fullModelName . '. Linha ' . strtolower($seriesName) . ' com ' . ($seriesData['specs']['Tela'] ?? 'tela') . ' e sistema ' . ($seriesData['specs']['Sistema'] ?? 'Android'),
                                'short_description' => 'Oppo ' . $fullModelName . ' - ' . ($seriesData['specs']['Foco'] ?? ''),
                                'sku' => $fullModelSku,
                                'price' => round($model['base_price'], 2),
                                'b2b_price' => round($model['base_price'] * 0.90, 2),
                                'cost_price' => round($model['base_price'] * 0.65, 2),
                                'stock_quantity' => 0,
                                'min_stock' => 3,
                                'manage_stock' => false,
                                'in_stock' => true,
                                'is_active' => true,
                                'is_featured' => $model['is_featured'] ?? false,
                                'brand' => 'Oppo',
                                'model' => $fullModelName,
                                'department_id' => $department->id,
                                'images' => $this->getImagesForProduct($fullModelName),
                                'specifications' => $seriesData['specs'],
                                'weight' => $seriesData['weight'],
                                'sort_order' => $seriesData['sort_order'],
                            ]);
                            
                            // Associar categoria
                            if ($category) {
                                $fullModelProduct->categories()->attach($category->id);
                            }
                            
                            $productCount++;
                            $productForVariations = $fullModelProduct;
                        } else {
                            // Produto já existe, usar o existente
                            $fullModelProduct = $existingFullModelProduct;
                            $updatedCount++;
                            $productForVariations = $fullModelProduct;
                        }
                    } else {
                        // Se não houver variação de nome, usar o produto base
                        $productForVariations = $baseProduct;
                    }
                    
                    $variationSortOrder = 0;
                    foreach ($model['ram'] as $ram) {
                        foreach ($model['storage'] as $storage) {
                            // Calcular preços
                            $storageMultiplier = match($storage) {
                                '32GB' => 1.0,
                                '64GB' => 1.15,
                                '128GB' => 1.0,
                                '256GB' => 1.25,
                                '512GB' => 1.5,
                                default => 1.0
                            };

                            $ramMultiplier = match($ram) {
                                '2GB' => 1.0,
                                '3GB' => 1.10,
                                '4GB' => 1.0,
                                '6GB' => 1.0,
                                '8GB' => 1.0,
                                '12GB' => 1.15,
                                default => 1.0
                            };

                            $finalPrice = $model['base_price'] * $storageMultiplier * $ramMultiplier;
                            $finalB2BPrice = $finalPrice * 0.90;
                            $finalCostPrice = $finalPrice * 0.65;

                            // Gerar SKU para variação (usar fullModelName se houver variação, senão baseModelName)
                            $modelNameForSku = $variation ? $fullModelName : $baseModelName;
                            $skuBase = str_replace([' ', '+', '(', ')'], ['-', 'Plus', '', ''], $modelNameForSku);
                            $variationSku = 'OPPO-' . str_replace(' ', '-', $seriesName) . '-' . $skuBase . '-' . str_replace('GB', '', $ram) . '-' . str_replace('GB', '', $storage);

                            // Verificar se variação já existe
                            $existingVariation = ProductVariation::where('sku', $variationSku)->first();

                            if (!$existingVariation) {
                                ProductVariation::create([
                                    'product_id' => $productForVariations->id,
                                    'ram' => $ram,
                                    'storage' => $storage,
                                    'sku' => $variationSku,
                                    'price' => round($finalPrice, 2),
                                    'b2b_price' => round($finalB2BPrice, 2),
                                    'cost_price' => round($finalCostPrice, 2),
                                    'stock_quantity' => rand(5, 40),
                                    'in_stock' => true,
                                    'is_active' => true,
                                    'sort_order' => $variationSortOrder++,
                                ]);
                            }

                            // Atualizar preço base do produto para o menor preço disponível
                            if ($variationSortOrder === 1 || $productForVariations->price > $finalPrice) {
                                $productForVariations->update(['price' => round($finalPrice, 2)]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Garantir que as categorias necessárias existam
     */
    private function ensureCategories()
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

