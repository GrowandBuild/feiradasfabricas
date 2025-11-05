<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'stock_quantity',
        'min_stock',
        'manage_stock',
        'in_stock',
        'is_active',
        'is_featured',
        'brand',
        'model',
        'images',
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
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'images' => 'array',
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
            // Se for um caminho relativo, adicionar o asset()
            if (strpos($firstImage, 'http') !== 0) {
                return asset('storage/' . $firstImage);
            }
            return $firstImage;
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
                // Se for um caminho relativo, adicionar o asset()
                if (strpos($image, 'http') !== 0) {
                    return asset('storage/' . $image);
                }
                return $image;
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
}
