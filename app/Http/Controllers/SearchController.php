<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Extrai atributos da busca (cores, armazenamento, RAM) e normaliza tokens
     */
    private function parseQueryAttributes(string $query): array
    {
        $q = mb_strtolower(trim($query));

        // Mapa de sinônimos e termos de cores; manter em único lugar para reutilização
        $colorSynonyms = [
            'preto' => ['preto','black','grafite','graphite','black titanium','titanium black'],
            'branco' => ['branco','white','starlight','luz das estrelas','white titanium'],
            'azul' => ['azul','blue','sierra blue','pacific blue','blue titanium'],
            'verde' => ['verde','green','alpine green'],
            'roxo' => ['roxo','purple','deep purple'],
            'rosa' => ['rosa','pink','rose','rose gold'],
            'dourado' => ['dourado','gold','champagne'],
            'prateado' => ['prateado','silver'],
            // Adicionar termo "space" isolado para capturar buscas como "space" e mapear para Space Gray / Space Black
            'cinza' => ['cinza','gray','grey','space gray','space grey','space','natural titanium','desert titanium'],
            'titanium' => ['titanium','titânio','natural titanium','desert titanium','black titanium','white titanium','blue titanium','rose titanium'],
        ];

        $colorsDetected = [];
        $detectedSynonymTerms = [];
        foreach ($colorSynonyms as $norm => $list) {
            foreach ($list as $term) {
                $termLower = mb_strtolower($term);
                if (mb_strpos($q, $termLower) !== false) {
                    $colorsDetected[$norm] = true;
                    // Agregar todos os sinônimos dessa cor para matching em JOIN e imagem
                    foreach ($list as $syn) {
                        $detectedSynonymTerms[] = mb_strtolower($syn);
                    }
                    break;
                }
            }
        }

        // Normalizar armazenamento: aceitar 128, 128g, 128gb, 1tb, 2tb etc.
        $storageDetected = [];
        if (preg_match_all('/\b(64|128|256|512)\s*(gb|g|giga)?\b/i', $query, $m)) {
            foreach ($m[1] as $val) { $storageDetected[] = intval($val).' GB'; }
        }
        if (preg_match_all('/\b(1|2)\s*(tb|tera)\b/i', $query, $m2)) {
            foreach ($m2[1] as $val) { $storageDetected[] = intval($val).' TB'; }
        }

        // RAM comum em celulares
        $ramDetected = [];
        if (preg_match_all('/\b(4|6|8|12|16)\s*(gb|g)\b/i', $query, $m3)) {
            foreach ($m3[1] as $val) { $ramDetected[] = intval($val).' GB'; }
        }

        return [
            'colors' => array_keys($colorsDetected), // lista normalizada (chave)
            'color_terms' => array_values(array_unique($detectedSynonymTerms)), // lista de sinônimos detectados
            'storage' => array_unique($storageDetected),
            'ram' => array_unique($ramDetected),
        ];
    }

    /**
     * Escolhe as imagens certas para um produto dado os atributos procurados
     */
    private function chooseImagesForProduct(Product $product, array $attrs): array
    {
        // 1) Tentar pelas imagens de variação por cor (variation_images)
        $map = $product->variation_images_urls ?? [];
        if (!empty($attrs['colors']) || !empty($attrs['color_terms'])) {
            // Usar sinônimos detectados se existirem, senão lista normalizada
            $searchColors = !empty($attrs['color_terms']) ? $attrs['color_terms'] : $attrs['colors'];
            foreach ($map as $colorKey => $imgs) {
                $ck = mb_strtolower($colorKey);
                foreach ($searchColors as $sc) {
                    if (mb_strpos($ck, mb_strtolower($sc)) !== false && !empty($imgs)) {
                        return $imgs;
                    }
                }
            }
        }

        // 2) Sem cor detectada ou sem imagens específicas, usar imagens do produto
        $images = [];
        if ($product->images) {
            $source = is_array($product->images) ? $product->images : (json_decode($product->images, true) ?: []);
            foreach ($source as $image) {
                if (empty($image)) continue;
                $lower = mb_strtolower((string)$image);
                // pular prováveis logos/ícones de marca/arquivos svg
                if (str_ends_with($lower, '.svg') || str_contains($lower, '/brand') || str_contains($lower, '/brands') || str_contains($lower, 'logo')) {
                    continue;
                }
                if (strpos($image, 'http') === 0) { $images[] = $image; continue; }
                if (strpos($image, '/') === 0) { $images[] = url(ltrim($image,'/')); continue; }
                $images[] = url('storage/'.$image);
            }
        }
        if (empty($images)) { $images[] = url('images/no-image.png'); }
        return $images;
    }
    public function search(Request $request)
    {
        $startTime = microtime(true);
        $query = $request->get('q', '');
        $filters = $this->parseFilters($request);
        $sort = $request->get('sort', 'relevance');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $isRealTime = $request->get('real_time', false);

        // Cache para busca em tempo real
        $cacheKey = 'search_' . md5($query . serialize($filters) . $sort . $page . $perPage);
        
        if ($isRealTime) {
            $cached = cache()->get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
        }

        // Extrair atributos (cor/armazenamento/RAM) para uma busca consciente de variação
        $attrs = $this->parseQueryAttributes($query);

        // Busca principal com otimizações para tempo real
        $searchQuery = $this->buildSearchQuery($query, $filters, $sort, $attrs);
        
        if ($isRealTime) {
            // Para busca em tempo real, limitar resultados e usar cache
            $products = $searchQuery->limit($perPage)->get();
            $totalResults = $searchQuery->count();
            
            // Simular paginação
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $totalResults,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        } else {
            $products = $searchQuery->paginate($perPage, ['*'], 'page', $page);
        }

        // Sugestões de busca (só se não for tempo real)
        $suggestions = $isRealTime ? [] : $this->getSearchSuggestions($query);
        
        // Filtros disponíveis (simplificado por enquanto)
        $availableFilters = [
            'categories' => [],
            'brands' => [],
            'price_ranges' => [],
            'colors' => [],
            'storage_options' => [],
            'screen_sizes' => [],
        ];
        
        // Produtos relacionados (simplificado)
        $relatedProducts = [];

        // Estatísticas de busca
        $searchTime = microtime(true) - $startTime;
        $searchStats = [
            'total_results' => $products->total(),
            'search_time' => $searchTime,
            'query' => $query,
            'filters_applied' => count(array_filter($filters)),
            'real_time' => $isRealTime,
        ];

        // Enriquecer produtos com imagem da variação, quando aplicável
        $products->setCollection(
            $products->getCollection()->map(function($p) use ($attrs) {
                $p->variant_images = $this->chooseImagesForProduct($p, $attrs);
                $p->cover_image = $p->variant_images[0] ?? $p->first_image ?? null;
                return $p;
            })
        );

        $response = [
            'products' => $products,
            'suggestions' => $suggestions,
            'filters' => $availableFilters,
            'related_products' => $relatedProducts,
            'stats' => $searchStats,
            'facets' => $this->getSearchFacets($products->items()),
        ];

        // Cache para busca em tempo real (5 minutos)
        if ($isRealTime) {
            cache()->put($cacheKey, $response, 300);
        }

        return response()->json($response);
    }

    public function voiceSearch(Request $request)
    {
        $audioFile = $request->file('audio');
        
        // Simular reconhecimento de voz (integrar com API real depois)
        $transcribedText = $this->simulateVoiceRecognition($audioFile);
        
        return response()->json([
            'transcribed_text' => $transcribedText,
            'search_results' => $this->search(new Request(['q' => $transcribedText])),
        ]);
    }

    public function imageSearch(Request $request)
    {
        $imageFile = $request->file('image');
        
        // Simular busca por imagem (integrar com API real depois)
        $detectedObjects = $this->simulateImageRecognition($imageFile);
        
        $searchResults = [];
        foreach ($detectedObjects as $object) {
            $results = $this->search(new Request(['q' => $object]));
            $searchResults[$object] = $results->getData();
        }

        return response()->json([
            'detected_objects' => $detectedObjects,
            'search_results' => $searchResults,
        ]);
    }

    public function barcodeSearch(Request $request)
    {
        $barcode = $request->get('barcode');
        
        $product = Product::where('sku', $barcode)
                         ->orWhere('barcode', $barcode)
                         ->first();

        if ($product) {
            return response()->json([
                'found' => true,
                'product' => $product,
                'related_products' => $this->getRelatedProducts($product->name, [$product]),
            ]);
        }

        return response()->json([
            'found' => false,
            'suggestions' => $this->getBarcodeSuggestions($barcode),
        ]);
    }

    public function getTrendingSearches()
    {
        // Simular buscas em alta
        return [
            'iPhone 15',
            'Samsung Galaxy',
            'AirPods',
            'MacBook Pro',
            'iPad',
        ];
    }

    public function getRecentSearches(Request $request)
    {
        // Implementar histórico de buscas do usuário
        return [
            'iPhone',
            'Samsung',
            'Carregador',
        ];
    }

    private function parseFilters(Request $request)
    {
        return [
            'category' => $request->get('category'),
            'brand' => $request->get('brand'),
            'price_min' => $request->get('price_min'),
            'price_max' => $request->get('price_max'),
            'rating' => $request->get('rating'),
            'availability' => $request->get('availability'),
            'condition' => $request->get('condition'),
            'color' => $request->get('color'),
            'storage' => $request->get('storage'),
            'screen_size' => $request->get('screen_size'),
        ];
    }

    private function buildSearchQuery($query, $filters, $sort, $attrs = [])
    {
    $searchQuery = Product::query();
        
        // Sempre filtrar apenas produtos disponíveis
    $searchQuery->where('products.is_unavailable', false);

        // Busca textual
        if (!empty($query)) {
                        $searchQuery->where(function($q) use ($query) {
                                $q->where('products.name', 'like', "%{$query}%")
                                    ->orWhere('products.description', 'like', "%{$query}%")
                                    ->orWhere('products.brand', 'like', "%{$query}%")
                                    ->orWhere('products.model', 'like', "%{$query}%")
                                    ->orWhere('products.sku', 'like', "%{$query}%");
                
                // Busca por especificações
                $q->orWhereJsonContains('products.specifications', $query);
            });
        }

        // Aplicar filtros
        $this->applyFilters($searchQuery, $filters);

        // Se a busca menciona cor/armazenamento, unir com variações para melhorar relevância
    $joinVariations = !empty($attrs['colors']) || !empty($attrs['color_terms']) || !empty($attrs['storage']) || !empty($attrs['ram']);
        if ($joinVariations) {
            $searchQuery->leftJoin('product_variations as pv', 'pv.product_id', '=', 'products.id');
            if (!empty($attrs['colors']) || !empty($attrs['color_terms'])) {
                $colorsLower = array_map('mb_strtolower', array_unique(array_merge(
                    (array) ($attrs['colors'] ?? []),
                    (array) ($attrs['color_terms'] ?? [])
                )));
                $searchQuery->where(function($q) use ($colorsLower) {
                    foreach ($colorsLower as $c) {
                        $q->orWhereRaw('LOWER(pv.color) LIKE ?', ["%{$c}%"]);
                    }
                });
            }
            if (!empty($attrs['storage'])) {
                $storages = (array) $attrs['storage'];
                $searchQuery->where(function($q) use ($storages) {
                    foreach ($storages as $s) {
                        // comparar removendo espaços/maiúsculas (ex.: 128 GB)
                        $comp = mb_strtolower(str_replace(' ', '', $s));
                        $q->orWhereRaw('REPLACE(LOWER(pv.storage), " ", "") LIKE ?', ["%{$comp}%"]);
                    }
                });
            }
            if (!empty($attrs['ram'])) {
                $rams = (array) $attrs['ram'];
                $searchQuery->where(function($q) use ($rams) {
                    foreach ($rams as $r) {
                        $comp = mb_strtolower(str_replace(' ', '', $r));
                        $q->orWhereRaw('REPLACE(LOWER(pv.ram), " ", "") LIKE ?', ["%{$comp}%"]);
                    }
                });
            }
            $searchQuery->select('products.*')->distinct();
        }

        // Aplicar ordenação
        $this->applySorting($searchQuery, $sort);

        // Eager loading necessário
        $withs = ['categories'];
        if ($joinVariations) { $withs[] = 'variations'; }
        return $searchQuery->with($withs);
    }

    private function applyFilters($query, $filters)
    {
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['brand'])) {
            $query->whereIn('brand', (array) $filters['brand']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (!empty($filters['availability'])) {
            if ($filters['availability'] === 'in_stock') {
                $query->where('in_stock', true);
            } elseif ($filters['availability'] === 'out_of_stock') {
                $query->where('in_stock', false);
            }
        }

        if (!empty($filters['color'])) {
            $query->whereJsonContains('specifications->Cor', $filters['color']);
        }

        if (!empty($filters['storage'])) {
            $query->whereJsonContains('specifications->Armazenamento', $filters['storage']);
        }

        if (!empty($filters['screen_size'])) {
            $query->whereJsonContains('specifications->Tela', 'like', "%{$filters['screen_size']}%");
        }
    }

    private function applySorting($query, $sort)
    {
      // Sempre priorizar produtos disponíveis primeiro (qualificar para evitar ambiguidade após JOIN)
      $query->orderBy('products.is_unavailable', 'asc') // Disponíveis primeiro
          ->orderBy('products.in_stock', 'desc')
          ->orderBy('products.is_active', 'desc');
        
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('products.price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('products.price', 'desc');
                break;
            case 'name':
                $query->orderBy('products.name', 'asc');
                break;
            case 'newest':
                $query->orderBy('products.created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('products.rating', 'desc');
                break;
            case 'popularity':
                $query->orderBy('products.sales_count', 'desc');
                break;
            default: // relevance
                $query->orderBy('products.name', 'asc');
                break;
        }
    }

    private function getSearchSuggestions($query)
    {
        if (strlen($query) < 2) {
            return [];
        }

        return [
            'did_you_mean' => $this->getDidYouMeanSuggestions($query),
            'popular_searches' => $this->getPopularSearches($query),
            'trending' => $this->getTrendingSearches(),
        ];
    }

    private function getAvailableFilters($query, $currentFilters, $isRealTime = false)
    {
        // Para busca em tempo real, usar cache e limitar resultados
        $cacheKey = 'filters_' . md5($query . serialize($currentFilters));
        
        if ($isRealTime) {
            $cached = cache()->get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }

        $baseQuery = Product::query();
        
        if (!empty($query)) {
            $baseQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%");
            });
        }

        $filters = [
            'categories' => $this->getFilterCategories($baseQuery, $isRealTime),
            'brands' => $this->getFilterBrands($baseQuery, $isRealTime),
            'price_ranges' => $this->getFilterPriceRanges($baseQuery),
            'colors' => $this->getFilterColors($baseQuery, $isRealTime),
            'storage_options' => $this->getFilterStorageOptions($baseQuery, $isRealTime),
            'screen_sizes' => $this->getFilterScreenSizes($baseQuery, $isRealTime),
        ];

        // Cache para tempo real (3 minutos)
        if ($isRealTime) {
            cache()->put($cacheKey, $filters, 180);
        }

        return $filters;
    }

    private function getRelatedProducts($query, $currentProducts)
    {
        $currentProductIds = collect($currentProducts)->pluck('id');
        
        return Product::where('id', '!=', $currentProductIds)
                     ->where(function($q) use ($query) {
                         $q->where('name', 'like', "%{$query}%")
                           ->orWhere('brand', 'like', "%{$query}%");
                     })
                     ->limit(8)
                     ->get();
    }

    private function getSearchFacets($products)
    {
        return [
            'total_products' => count($products),
            'price_range' => [
                'min' => collect($products)->min('price'),
                'max' => collect($products)->max('price'),
            ],
            'brands_count' => collect($products)->pluck('brand')->unique()->count(),
            'categories_count' => collect($products)->flatMap->categories->pluck('name')->unique()->count(),
        ];
    }

    private function getProductSuggestions($query)
    {
        return Product::where(function($q) use ($query) {
                         $q->where('name', 'like', "%{$query}%")
                           ->orWhere('brand', 'like', "%{$query}%");
                     })
                     ->orderBy('in_stock', 'desc')
                     ->orderBy('is_active', 'desc')
                     ->orderBy('name', 'asc')
                     ->limit(5)
                     ->get(['id', 'name', 'brand', 'price', 'images'])
                     ->map(function($product) {
                         return [
                             'type' => 'product',
                             'id' => $product->id,
                             'text' => $product->name,
                             'subtitle' => $product->brand,
                             'price' => $product->price,
                             'image' => $product->images[0] ?? null,
                         ];
                     });
    }

    private function getCategorySuggestions($query)
    {
        return Category::where('name', 'like', "%{$query}%")
                      ->limit(3)
                      ->get(['id', 'name', 'slug'])
                      ->map(function($category) {
                          return [
                              'type' => 'category',
                              'id' => $category->id,
                              'text' => $category->name,
                              'slug' => $category->slug,
                          ];
                      });
    }

    private function getBrandSuggestions($query)
    {
        return Product::where('brand', 'like', "%{$query}%")
                     ->distinct()
                     ->limit(3)
                     ->pluck('brand')
                     ->map(function($brand) {
                         return [
                             'type' => 'brand',
                             'text' => $brand,
                             'slug' => strtolower(str_replace(' ', '-', $brand)),
                         ];
                     });
    }

    private function getRelatedSearchSuggestions($query)
    {
        // Simular sugestões relacionadas
        $related = [
            'iPhone' => ['iPhone 15', 'iPhone 14', 'iPhone 13', 'iPhone SE'],
            'Samsung' => ['Samsung Galaxy', 'Samsung Galaxy S', 'Samsung Galaxy Note'],
            'MacBook' => ['MacBook Pro', 'MacBook Air', 'MacBook M1', 'MacBook M2'],
        ];

        foreach ($related as $key => $suggestions) {
            if (stripos($query, $key) !== false) {
                return array_slice($suggestions, 0, 3);
            }
        }

        return [];
    }

    private function simulateVoiceRecognition($audioFile)
    {
        // Simular reconhecimento de voz
        return "iPhone 15 Pro Max";
    }

    private function simulateImageRecognition($imageFile)
    {
        // Simular reconhecimento de imagem
        return ['iPhone', 'Smartphone', 'Apple'];
    }

    private function getBarcodeSuggestions($barcode)
    {
        return Product::where('sku', 'like', "%{$barcode}%")
                     ->orWhere('barcode', 'like', "%{$barcode}%")
                     ->limit(5)
                     ->get(['id', 'name', 'sku', 'brand']);
    }

    private function getDidYouMeanSuggestions($query)
    {
        // Implementar algoritmo de "Você quis dizer"
        $suggestions = [
            'iphone' => 'iPhone',
            'samsun' => 'Samsung',
            'macbok' => 'MacBook',
        ];

        return $suggestions[strtolower($query)] ?? null;
    }

    private function getPopularSearches($query)
    {
        return [
            $query . ' Pro',
            $query . ' Max',
            $query . ' Air',
        ];
    }

    private function getFilterCategories($baseQuery, $isRealTime = false)
    {
        $limit = $isRealTime ? 5 : 10; // Menos resultados para tempo real
        
        return Category::selectRaw('categories.*, COUNT(products.id) as product_count')
                      ->join('product_categories', 'categories.id', '=', 'product_categories.category_id')
                      ->join('products', 'products.id', '=', 'product_categories.product_id')
                      ->whereIn('products.id', $baseQuery->pluck('id'))
                      ->groupBy('categories.id')
                      ->orderBy('product_count', 'desc')
                      ->limit($limit)
                      ->get();
    }

    private function getFilterBrands($baseQuery, $isRealTime = false)
    {
        $limit = $isRealTime ? 5 : 10; // Menos resultados para tempo real
        
        return Product::selectRaw('brand, COUNT(*) as product_count')
                     ->whereIn('id', $baseQuery->pluck('id'))
                     ->whereNotNull('brand')
                     ->groupBy('brand')
                     ->orderBy('product_count', 'desc')
                     ->limit($limit)
                     ->get();
    }

    private function getFilterPriceRanges($baseQuery)
    {
        $prices = $baseQuery->pluck('price');
        
        return [
            ['min' => 0, 'max' => 500, 'label' => 'Até R$ 500'],
            ['min' => 500, 'max' => 1000, 'label' => 'R$ 500 - R$ 1.000'],
            ['min' => 1000, 'max' => 2000, 'label' => 'R$ 1.000 - R$ 2.000'],
            ['min' => 2000, 'max' => 5000, 'label' => 'R$ 2.000 - R$ 5.000'],
            ['min' => 5000, 'max' => null, 'label' => 'Acima de R$ 5.000'],
        ];
    }

    private function getFilterColors($baseQuery)
    {
        return Product::selectRaw('JSON_EXTRACT(specifications, "$.Cor") as color, COUNT(*) as product_count')
                     ->whereIn('id', $baseQuery->pluck('id'))
                     ->whereNotNull('specifications')
                     ->groupBy('color')
                     ->orderBy('product_count', 'desc')
                     ->limit(10)
                     ->get();
    }

    private function getFilterStorageOptions($baseQuery)
    {
        return Product::selectRaw('JSON_EXTRACT(specifications, "$.Armazenamento") as storage, COUNT(*) as product_count')
                     ->whereIn('id', $baseQuery->pluck('id'))
                     ->whereNotNull('specifications')
                     ->groupBy('storage')
                     ->orderBy('product_count', 'desc')
                     ->limit(10)
                     ->get();
    }

    private function getFilterScreenSizes($baseQuery)
    {
        return Product::selectRaw('JSON_EXTRACT(specifications, "$.Tela") as screen, COUNT(*) as product_count')
                     ->whereIn('id', $baseQuery->pluck('id'))
                     ->whereNotNull('specifications')
                     ->groupBy('screen')
                     ->orderBy('product_count', 'desc')
                     ->limit(10)
                     ->get();
    }

    /**
     * Autocomplete para a barra de busca (sugestões rápidas)
     */
    public function autocomplete(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        if ($query === '') {
            return response()->json([
                'results' => [],
                'related' => [],
            ]);
        }

        $products = $this->getProductSuggestions($query);
        $categories = $this->getCategorySuggestions($query);
        $brands = $this->getBrandSuggestions($query);
        $related = $this->getRelatedSearchSuggestions($query);

        $results = $products
            ->merge($categories)
            ->merge($brands)
            ->values();

        return response()->json([
            'results' => $results,
            'related' => $related,
        ]);
    }

    /**
     * Live search: resultados rápidos e ranqueados, conscientes de variação
     */
    public function liveSearch(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $limit = (int) $request->get('limit', 12);

        // Aceitar consultas de 1 caractere; se vazio, retornar lista vazia
        if ($query === '' || mb_strlen($query) < 1) {
            return response()->json([
                'count' => 0,
                'items' => [],
                'query' => $query,
            ]);
        }

        $lowerq = mb_strtolower($query);
        $tokens = preg_split('/\s+/', $lowerq) ?: [];

        $attrs = $this->parseQueryAttributes($query);

        $qb = Product::query()
            ->where('products.is_active', true);

        // Filtro textual amplo
        $qb->where(function($q) use ($lowerq) {
            $q->whereRaw('LOWER(products.name) LIKE ?', ["%{$lowerq}%"]) 
              ->orWhereRaw('LOWER(products.brand) LIKE ?', ["%{$lowerq}%"]) 
              ->orWhereRaw('LOWER(products.model) LIKE ?', ["%{$lowerq}%"]) 
              ->orWhereRaw('LOWER(products.sku) LIKE ?', ["%{$lowerq}%"]);
        });

        // Join com variações somente se for útil (cores/armazenamento/ram detectados)
        $joinVariations = !empty($attrs['colors']) || !empty($attrs['color_terms']) || !empty($attrs['storage']) || !empty($attrs['ram']);
        if ($joinVariations) {
            $qb->leftJoin('product_variations as pv', 'pv.product_id', '=', 'products.id');

            if (!empty($attrs['colors']) || !empty($attrs['color_terms'])) {
                $colorsLower = array_map('mb_strtolower', array_unique(array_merge(
                    (array) ($attrs['colors'] ?? []),
                    (array) ($attrs['color_terms'] ?? [])
                )));
                $qb->where(function($q) use ($colorsLower) {
                    foreach ($colorsLower as $c) {
                        $q->orWhereRaw('LOWER(pv.color) LIKE ?', ["%{$c}%"]);
                    }
                });
            }
            if (!empty($attrs['storage'])) {
                $storages = (array) $attrs['storage'];
                $qb->where(function($q) use ($storages) {
                    foreach ($storages as $s) {
                        $comp = mb_strtolower(str_replace(' ', '', $s));
                        $q->orWhereRaw('REPLACE(LOWER(pv.storage), " ", "") LIKE ?', ["%{$comp}%"]);
                    }
                });
            }
            if (!empty($attrs['ram'])) {
                $rams = (array) $attrs['ram'];
                $qb->where(function($q) use ($rams) {
                    foreach ($rams as $r) {
                        $comp = mb_strtolower(str_replace(' ', '', $r));
                        $q->orWhereRaw('REPLACE(LOWER(pv.ram), " ", "") LIKE ?', ["%{$comp}%"]);
                    }
                });
            }

            $qb->select('products.*')->distinct();
        }

        // Relevância: dar boost para matches fortes e casos especiais (ex.: "pro max", "iphone x")
        $scorePieces = [];
        $bindings = [];
        $scorePieces[] = 'CASE WHEN LOWER(products.name) = ? THEN 100 WHEN LOWER(products.name) LIKE ? THEN 90 WHEN LOWER(products.name) LIKE ? THEN 80 ELSE 0 END';
        $bindings[] = $lowerq;
        $bindings[] = "%{$lowerq}%";
        $bindings[] = "{$lowerq}%";
        $scorePieces[] = 'CASE WHEN LOWER(products.brand) LIKE ? THEN 10 ELSE 0 END';
        $bindings[] = "%{$lowerq}%";

        // Boosts contextuais
        if (str_contains($lowerq, 'pro') && str_contains($lowerq, 'max')) {
            $scorePieces[] = "CASE WHEN LOWER(products.name) LIKE '%pro max%' THEN 25 ELSE 0 END";
        }
        if (preg_match('/\\biphone\\b/i', $lowerq)) {
            $scorePieces[] = "CASE WHEN LOWER(products.brand) = 'apple' OR LOWER(products.name) LIKE '%iphone%' THEN 15 ELSE 0 END";
        }
        if (preg_match('/\\bx\\b/i', $lowerq)) {
            $scorePieces[] = "CASE WHEN LOWER(products.name) LIKE '%iphone x%' THEN 20 ELSE 0 END";
        }
        if ($lowerq === 'max') {
            $scorePieces[] = "CASE WHEN LOWER(products.name) LIKE '%pro max%' THEN 30 ELSE 0 END";
        }

    $scoreSql = implode(' + ', $scorePieces) . ' as relevance_score';
    $qb->selectRaw($scoreSql, $bindings)->addSelect('products.*');

        // Ordenação: relevância, depois disponibilidade, depois estoque
        $qb->orderByDesc('relevance_score')
           ->orderBy('products.is_unavailable', 'asc')
           ->orderBy('products.in_stock', 'desc')
           ->orderBy('products.name', 'asc');

        // Trazer variações para montar display_name/variant_url
        $qb->with('variations');

    $items = $qb->limit($limit)->get();

        // Mapear para payload enxuto da live search
    $mapped = $items->map(function(Product $p) use ($attrs) {
            // Escolher variação mais compatível (se houver)
            $bestVar = null;
            $bestScore = -1;
            foreach ($p->variations as $v) {
                $score = 0;
                if (!empty($attrs['color_terms'])) {
                    foreach ($attrs['color_terms'] as $term) {
                        if ($v->color && mb_stripos($v->color, $term) !== false) { $score += 2; break; }
                    }
                } elseif (!empty($attrs['colors'])) {
                    foreach ($attrs['colors'] as $term) {
                        if ($v->color && mb_stripos($v->color, $term) !== false) { $score += 2; break; }
                    }
                }
                if (!empty($attrs['storage'])) {
                    foreach ($attrs['storage'] as $s) {
                        $comp = mb_strtolower(str_replace(' ', '', $s));
                        if ($v->storage && mb_strtolower(str_replace(' ', '', $v->storage)) === $comp) { $score += 2; break; }
                    }
                }
                if (!empty($attrs['ram'])) {
                    foreach ($attrs['ram'] as $r) {
                        $comp = mb_strtolower(str_replace(' ', '', $r));
                        if ($v->ram && mb_strtolower(str_replace(' ', '', $v->ram)) === $comp) { $score += 1; break; }
                    }
                }
                if ($score > $bestScore) { $bestScore = $score; $bestVar = $v; }
            }

            $images = $this->chooseImagesForProduct($p, $attrs);

            $displayName = $p->name;
            $variantUrl = null;
            if ($bestVar) {
                $parts = [];
                if ($bestVar->storage) $parts[] = $bestVar->storage;
                if ($bestVar->ram) $parts[] = $bestVar->ram;
                if ($bestVar->color) $parts[] = $bestVar->color;
                if (!empty($parts)) {
                    $displayName .= ' — ' . implode(' / ', $parts);
                }
                // Em vez de enviar para a rota da variação, enviar para a página do produto com pré-seleção via querystring
                $params = [];
                if ($bestVar->color) { $params['color'] = $bestVar->color; }
                if ($bestVar->storage) { $params['storage'] = $bestVar->storage; }
                if ($bestVar->ram) { $params['ram'] = $bestVar->ram; }
                $qs = http_build_query($params);
                $variantUrl = url('/produto/' . $p->slug . ($qs ? ('?' . $qs) : ''));
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand,
                'price' => $p->price,
                'in_stock' => (bool) $p->in_stock,
                'is_unavailable' => (bool) $p->is_unavailable,
                'slug' => $p->slug,
                'image' => $images[0] ?? null,
                'images' => $images,
                'cover_image' => $images[0] ?? null,
                'short_description' => $p->short_description,
                'description' => $p->description,
                'display_name' => $displayName,
                'variant_url' => $variantUrl,
                'product_url' => url('/produto/' . $p->slug),
            ];
        });

        return response()->json([
            'count' => $mapped->count(),
            'products' => $mapped,
            'query' => $query,
        ]);
    }
}
