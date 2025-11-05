<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
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

        // Busca principal com otimizações para tempo real
        $searchQuery = $this->buildSearchQuery($query, $filters, $sort);
        
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

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'trending' => $this->getTrendingSearches(),
                'recent' => $this->getRecentSearches($request),
            ]);
        }

        $suggestions = [
            'products' => $this->getProductSuggestions($query),
            'categories' => $this->getCategorySuggestions($query),
            'brands' => $this->getBrandSuggestions($query),
            'related_searches' => $this->getRelatedSearchSuggestions($query),
        ];

        return response()->json($suggestions);
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

    private function buildSearchQuery($query, $filters, $sort)
    {
        $searchQuery = Product::query();

        // Busca textual
        if (!empty($query)) {
            $searchQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
                
                // Busca por especificações
                $q->orWhereJsonContains('specifications', $query);
            });
        }

        // Aplicar filtros
        $this->applyFilters($searchQuery, $filters);

        // Aplicar ordenação
        $this->applySorting($searchQuery, $sort);

        return $searchQuery->with(['categories']);
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
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'popularity':
                $query->orderBy('sales_count', 'desc');
                break;
            default: // relevance
                $query->orderBy('name', 'asc');
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
        return Product::where('name', 'like', "%{$query}%")
                     ->orWhere('brand', 'like', "%{$query}%")
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
}
