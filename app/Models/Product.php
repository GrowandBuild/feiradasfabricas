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
        'brand',
        'model',
        'supplier',
        'images',
        'variation_images',
        'specifications',
        'weight',
        'length',
        'width',
        'height',
        'sort_order',
        'department_id',
    ];

    protected $casts = [
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_unavailable' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'profit_margin_b2b' => 'decimal:2',
        'profit_margin_b2c' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'images' => 'array',
        'variation_images' => 'array',
        'specifications' => 'array',
    ];

    /**
     * Relacionamento com departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
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
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order');
    }

    /**
     * Relacionamento com variações ativas do produto
     */
    public function activeVariations()
    {
        return $this->hasMany(ProductVariation::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Verifica se o produto tem variações
     */
    public function hasVariations()
    {
        return $this->variations()->count() > 0;
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
            
            // Se for um caminho relativo, usar Storage::url() que gera a URL correta
            // Isso funciona tanto com link simbólico quanto sem ele em produção
            try {
                return Storage::disk('public')->url($firstImage);
            } catch (\Exception $e) {
                // Fallback para asset() se houver algum problema
                return asset('storage/' . $firstImage);
            }
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
                
                // Se for um caminho relativo, usar Storage::url() que gera a URL correta
                try {
                    return Storage::disk('public')->url($image);
                } catch (\Exception $e) {
                    // Fallback para asset() se houver algum problema
                    return asset('storage/' . $image);
                }
            }, $images);
        }
        return [];
    }

    /**
     * Obtém o mapa de imagens por cor (URLs completas)
     */
    public function getVariationImagesUrlsAttribute(): array
    {
        if (!is_array($this->variation_images) || empty($this->variation_images)) {
            return [];
        }

        $map = [];

        foreach ($this->variation_images as $color => $images) {
            if (!is_array($images)) {
                continue;
            }

            $map[$color] = array_values(array_map(function ($image) {
                if (strpos($image, 'http') === 0 || strpos($image, 'https') === 0) {
                    return $image;
                }

                try {
                    return Storage::disk('public')->url($image);
                } catch (\Exception $e) {
                    return asset('storage/' . $image);
                }
            }, array_filter($images))); // remove valores vazios
        }

        return $map;
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
}
