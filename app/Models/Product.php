<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'b2b_price',
        'cost_price',
        'profit_margin_b2b',
        'profit_margin_b2c',
        'stock_quantity',
        'min_stock',
        'manage_stock',
        'in_stock',
        'is_active',
        'is_unavailable',
        'is_featured',
        'has_variations',
        'brand',
        'brand_id',
        'model',
        'supplier',
        'images',
        'specifications',
        'weight',
        'length',
        'width',
        'height',
        'sort_order',
        'department_id',
        'homepage_section_ids',
        'product_type',
        'sell_b2b',
        'sell_b2c',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'product_type' => 'physical',
    ];

    protected $casts = [
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_unavailable' => 'boolean',
        'is_featured' => 'boolean',
        'has_variations' => 'boolean',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'profit_margin_b2b' => 'decimal:2',
        'profit_margin_b2c' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'images' => 'array',
        'specifications' => 'array',
        'homepage_section_ids' => 'array',
        'sell_b2b' => 'boolean',
        'sell_b2c' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     * Permite usar slug nas rotas com model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relacionamento com departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relacionamento com marca (opcional).
     * Mantemos a coluna string `brand` por compatibilidade; a referência nova fica em `brand_id`.
     */
    public function brandModel()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Nome da marca — prefere o relacionamento `brandModel`, cai back para a coluna `brand`.
     */
    public function getBrandNameAttribute()
    {
        if ($this->relationLoaded('brandModel') && $this->brandModel) {
            return $this->brandModel->name;
        }

        if ($this->brandModel) {
            return $this->brandModel->name;
        }

        return $this->attributes['brand'] ?? null;
    }

    /**
     * Relacionamento com categorias
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Relacionamento com itens do carrinho
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relacionamento com itens de pedidos
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relacionamento com variações do produto
     */
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    /**
     * Relacionamento com variação padrão
     */
    public function defaultVariation()
    {
        return $this->hasOne(ProductVariation::class)->where('is_default', true);
    }

    /**
     * Obtém atributos usados nas variações deste produto (CORRIGIDO: tratamento de erros)
     */
    public function attributeSets()
    {
        // Verificar se produto tem variações antes de buscar atributos
        // CORRIGIDO: Retornar Eloquent Collection vazia, não Collection simples
        if (!$this->has_variations || $this->variations()->count() === 0) {
            return \App\Models\ProductAttribute::whereIn('id', [])->get(); // Eloquent Collection vazia
        }

        try {
            $attributeIds = \App\Models\ProductVariationAttribute::whereHas('variation', function($query) {
                $query->where('product_id', $this->id);
            })->distinct()->pluck('attribute_id');

            // CORRIGIDO: Se não houver atributos, retornar Eloquent Collection vazia
            if ($attributeIds->isEmpty()) {
                return \App\Models\ProductAttribute::whereIn('id', [])->get(); // Eloquent Collection vazia
            }

            $attributes = \App\Models\ProductAttribute::whereIn('id', $attributeIds)
                                  ->where('is_active', true)
                                  ->with(['values' => function($query) {
                                      $query->where('is_active', true)->orderBy('sort_order');
                                  }])
                                  ->orderBy('sort_order')
                                  ->get();

            // CORRIGIDO: Sempre retornar Eloquent Collection, nunca Collection simples
            // O componente espera Eloquent Collection para acessar métodos do Model
            return $attributes instanceof \Illuminate\Database\Eloquent\Collection 
                ? $attributes 
                : \App\Models\ProductAttribute::whereIn('id', [])->get(); // Eloquent Collection vazia
        } catch (\Exception $e) {
            // Em caso de erro, retornar Eloquent Collection vazia (não Collection simples!)
            \Log::warning("Erro ao carregar attributeSets do produto {$this->id}: " . $e->getMessage());
            return \App\Models\ProductAttribute::whereIn('id', [])->get(); // Eloquent Collection vazia
        }
    }

    /**
     * Verifica se produto tem variações
     */
    public function hasVariations()
    {
        return $this->has_variations && $this->variations()->count() > 0;
    }

    /**
     * Obtém preço mínimo das variações (ou preço do produto se não tiver variações)
     */
    public function getMinPriceAttribute()
    {
        if ($this->has_variations) {
            $minPrice = $this->variations()->min('price');
            return $minPrice ?: $this->price;
        }
        return $this->price;
    }

    /**
     * Obtém preço máximo das variações (ou preço do produto se não tiver variações)
     */
    public function getMaxPriceAttribute()
    {
        if ($this->has_variations) {
            $maxPrice = $this->variations()->max('price');
            return $maxPrice ?: $this->price;
        }
        return $this->price;
    }

    /**
     * Verifica se alguma variação está em estoque
     */
    public function hasVariationInStock()
    {
        if (!$this->has_variations) {
            return $this->in_stock;
        }
        return $this->variations()->where('in_stock', true)->where('stock_quantity', '>', 0)->exists();
    }


    /**
     * Scope para produtos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para produtos em destaque
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para produtos em estoque
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Verifica se o produto está em estoque
     */
    public function hasStock($quantity = 1)
    {
        if (!$this->manage_stock) {
            return $this->in_stock;
        }

        return $this->stock_quantity >= $quantity;
    }

    /**
     * Obtém o preço baseado no tipo de cliente
     */
    public function getPriceForCustomer($customerType = 'b2c')
    {
        if ($customerType === 'b2b' && $this->b2b_price) {
            return $this->b2b_price;
        }

        return $this->price;
    }

    /**
     * Obtém a URL da primeira imagem
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images;
        if (is_array($images) && !empty($images)) {
            $firstImage = $images[0];
            
            // Se for uma URL completa, retornar diretamente
            if (strpos($firstImage, 'http') === 0 || strpos($firstImage, 'https') === 0) {
                return $firstImage;
            }
            
            // Se for um caminho relativo, retornar caminho relativo absoluto para usar o mesmo host/porta da requisição atual
            return '/storage/' . ltrim($firstImage, '/');
        }
        return asset('images/no-image.svg'); // Imagem padrão
    }

    /**
     * Obtém todas as imagens do produto
     */
    public function getAllImagesAttribute()
    {
        $images = $this->images ?? [];
        if (is_array($images)) {
            return array_map(function($image) {
                // Se for uma URL completa, retornar diretamente
                if (strpos($image, 'http') === 0 || strpos($image, 'https') === 0) {
                    return $image;
                }
                
                // Se for um caminho relativo, retornar caminho relativo absoluto
                return '/storage/' . ltrim($image, '/');
            }, $images);
        }
        return [];
    }


    /**
     * Verifica se o produto tem múltiplas imagens
     */
    public function hasMultipleImages()
    {
        return is_array($this->images) && count($this->images) > 1;
    }

    /**
     * Obtém o número total de imagens
     */
    public function getImageCount()
    {
        return is_array($this->images) ? count($this->images) : 0;
    }

    /**
     * Relacionamento com favoritos (lista de desejos)
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Relacionamento com feedbacks
     */
    public function feedbacks()
    {
        return $this->hasMany(ProductFeedback::class);
    }

    /**
     * Relacionamento com feedbacks aprovados
     */
    public function approvedFeedbacks()
    {
        return $this->hasMany(ProductFeedback::class)->approved();
    }

    /**
     * Verifica se o produto está nos favoritos de um cliente
     */
    public function isFavoriteFor($customerId)
    {
        return $this->favorites()->where('customer_id', $customerId)->exists();
    }

    /**
     * Relacionamento com logs de estoque
     */
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Obtém o estoque atual baseado nos logs
     */
    public function getCurrentStockAttribute()
    {
        $lastLog = $this->inventoryLogs()->latest()->first();
        return $lastLog ? $lastLog->quantity_after : $this->stock_quantity;
    }

    /**
     * Verifica se está em estoque
     */
    public function isInStock()
    {
        return $this->current_stock > 0;
    }

    /**
     * Verifica se está com estoque baixo
     */
    public function isLowStock($threshold = 10)
    {
        return $this->current_stock <= $threshold;
    }

    /**
     * Verifica se o produto está indisponível
     */
    public function isUnavailable()
    {
        return $this->is_unavailable === true;
    }

    /**
     * Scope para produtos disponíveis
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_unavailable', false);
    }

    /**
     * Scope para produtos indisponíveis
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_unavailable', true);
    }

    /**
     * Verifica se o produto tem badge de lista (tem fornecedor definido)
     */
    public function hasListBadge()
    {
        return !empty($this->supplier);
    }

    /**
     * Obtém o texto do badge de lista
     */
    public function getListBadgeText()
    {
        if ($this->hasListBadge()) {
            return '📋 Lista';
        }
        return null;
    }

    /**
     * Relacionamento com HomepageSections
     */
    public function homepageSections()
    {
        return $this->belongsToMany(HomepageSection::class, 'product_homepage_section', 'product_id', 'homepage_section_id');
    }

    /**
     * Obtém as seções da homepage associadas a este produto
     */
    public function getHomepageSectionsAttribute()
    {
        if (!empty($this->homepage_section_ids)) {
            return HomepageSection::whereIn('id', $this->homepage_section_ids)->get();
        }
        return collect();
    }
}
