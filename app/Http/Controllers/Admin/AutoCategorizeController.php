<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AutoCategorizeController extends Controller
{
    private $categoryRules = [
        'smartphones' => [
            'keywords' => ['iphone', 'samsung', 'xiaomi', 'motorola', 'smartphone', 'celular', 'mobile'],
            'brands' => ['Apple', 'Samsung', 'Xiaomi', 'Motorola', 'LG', 'OnePlus']
        ],
        'tablets' => [
            'keywords' => ['ipad', 'tablet', 'galaxy tab'],
            'brands' => ['Apple', 'Samsung', 'Amazon']
        ],
        'acessorios' => [
            'keywords' => ['capa', 'case', 'carregador', 'fone', 'headphone', 'cabo', 'película'],
            'brands' => []
        ],
        'smartwatch' => [
            'keywords' => ['apple watch', 'galaxy watch', 'smartwatch', 'relógio inteligente'],
            'brands' => ['Apple', 'Samsung']
        ],
        'notebooks' => [
            'keywords' => ['macbook', 'laptop', 'notebook', 'ultrabook'],
            'brands' => ['Apple', 'Dell', 'HP', 'Lenovo', 'Asus']
        ]
    ];

    public function autoCategorize(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'confidence_threshold' => 'nullable|integer|min:1|max:100',
        ]);

        $threshold = $request->confidence_threshold ?? 70;
        $categorized = 0;
        $errors = [];

        foreach ($request->products as $productId) {
            try {
                $product = Product::find($productId);
                $suggestions = $this->getCategorySuggestions($product);
                
                if (!empty($suggestions) && $suggestions[0]['confidence'] >= $threshold) {
                    $bestCategory = Category::where('slug', $suggestions[0]['category'])->first();
                    
                    if ($bestCategory) {
                        $product->categories()->sync([$bestCategory->id]);
                        $categorized++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Produto {$product->name}: " . $e->getMessage();
            }
        }

        $response = redirect()->back()->with('success', "{$categorized} produtos categorizados automaticamente!");
        
        if (!empty($errors)) {
            $response->with('errors', $errors);
        }

        return $response;
    }

    public function getSuggestions(Product $product)
    {
        $suggestions = $this->getCategorySuggestions($product);
        
        return view('admin.products.category-suggestions', compact('product', 'suggestions'));
    }

    public function bulkAutoCategorize(Request $request)
    {
        $request->validate([
            'brand' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'uncategorized_only' => 'nullable|boolean',
            'confidence_threshold' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Product::query();

        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->uncategorized_only) {
            $query->whereDoesntHave('categories');
        }

        $products = $query->get();
        $threshold = $request->confidence_threshold ?? 70;
        $categorized = 0;

        foreach ($products as $product) {
            $suggestions = $this->getCategorySuggestions($product);
            
            if (!empty($suggestions) && $suggestions[0]['confidence'] >= $threshold) {
                $bestCategory = Category::where('slug', $suggestions[0]['category'])->first();
                
                if ($bestCategory) {
                    $product->categories()->sync([$bestCategory->id]);
                    $categorized++;
                }
            }
        }

        return redirect()->back()
                        ->with('success', "{$categorized} produtos categorizados automaticamente!");
    }

    public function createCategoryRule(Request $request)
    {
        $request->validate([
            'category_slug' => 'required|string',
            'keywords' => 'required|array',
            'keywords.*' => 'string',
            'brands' => 'nullable|array',
            'brands.*' => 'string',
        ]);

        $category = Category::where('slug', $request->category_slug)->first();
        
        if (!$category) {
            return redirect()->back()->with('error', 'Categoria não encontrada!');
        }

        $rule = [
            'keywords' => $request->keywords,
            'brands' => $request->brands ?? [],
        ];

        // Salvar regra (pode ser em cache, banco ou arquivo)
        $this->saveCategoryRule($request->category_slug, $rule);

        return redirect()->back()->with('success', 'Regra de categorização criada com sucesso!');
    }

    private function getCategorySuggestions(Product $product)
    {
        $suggestions = [];
        
        foreach ($this->categoryRules as $categorySlug => $rule) {
            $confidence = $this->calculateConfidence($product, $rule);
            
            if ($confidence > 0) {
                $suggestions[] = [
                    'category' => $categorySlug,
                    'confidence' => $confidence,
                    'reasons' => $this->getConfidenceReasons($product, $rule)
                ];
            }
        }

        // Ordenar por confiança
        usort($suggestions, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return $suggestions;
    }

    private function calculateConfidence(Product $product, $rule)
    {
        $confidence = 0;
        $text = strtolower($product->name . ' ' . $product->description . ' ' . $product->brand . ' ' . $product->model);
        
        // Verificar palavras-chave
        foreach ($rule['keywords'] as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $confidence += 30;
            }
        }
        
        // Verificar marca
        foreach ($rule['brands'] as $brand) {
            if (strpos(strtolower($product->brand), strtolower($brand)) !== false) {
                $confidence += 40;
            }
        }

        // Verificar modelo
        foreach ($rule['keywords'] as $keyword) {
            if (strpos(strtolower($product->model), strtolower($keyword)) !== false) {
                $confidence += 20;
            }
        }

        return min($confidence, 100);
    }

    private function getConfidenceReasons(Product $product, $rule)
    {
        $reasons = [];
        $text = strtolower($product->name . ' ' . $product->description . ' ' . $product->brand . ' ' . $product->model);
        
        foreach ($rule['keywords'] as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $reasons[] = "Palavra-chave: {$keyword}";
            }
        }
        
        foreach ($rule['brands'] as $brand) {
            if (strpos(strtolower($product->brand), strtolower($brand)) !== false) {
                $reasons[] = "Marca: {$brand}";
            }
        }

        return $reasons;
    }

    private function saveCategoryRule($categorySlug, $rule)
    {
        // Implementar salvamento da regra
        // Pode ser em cache, banco de dados ou arquivo
        $this->categoryRules[$categorySlug] = $rule;
    }

    public function getUncategorizedProducts()
    {
        $uncategorized = Product::whereDoesntHave('categories')
                               ->orderBy('created_at', 'desc')
                               ->paginate(20);

        return view('admin.products.uncategorized', compact('uncategorized'));
    }
}
