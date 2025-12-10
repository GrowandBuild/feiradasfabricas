<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'b2b_price',
        'stock_quantity',
        'in_stock',
        'is_default',
        'weight',
        'images',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'in_stock' => 'boolean',
        'is_default' => 'boolean',
        'weight' => 'decimal:2',
        'images' => 'array',
    ];

    /**
     * Relacionamento com produto pai
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento com atributos da variação (via pivot)
     */
    public function variationAttributes()
    {
        return $this->hasMany(ProductVariationAttribute::class, 'variation_id');
    }

    /**
     * Relacionamento com atributos da variação
     */
    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_variation_attributes', 'variation_id', 'attribute_id')
                    ->withPivot('attribute_value_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento com valores dos atributos da variação
     */
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variation_attributes', 'variation_id', 'attribute_value_id')
                    ->withPivot('attribute_id')
                    ->withTimestamps();
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
     * Scope para variações em estoque
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true)->where('stock_quantity', '>', 0);
    }

    /**
     * Scope para variação padrão
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Verifica se está em estoque
     */
    public function hasStock($quantity = 1)
    {
        return $this->in_stock && $this->stock_quantity >= $quantity;
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
     * Obtém todas as imagens da variação (ou do produto pai se não tiver)
     */
    public function getAllImagesAttribute()
    {
        if ($this->images && is_array($this->images) && !empty($this->images)) {
            return array_map(function($image) {
                if (strpos($image, 'http') === 0 || strpos($image, 'https') === 0) {
                    return $image;
                }
                return '/storage/' . ltrim($image, '/');
            }, $this->images);
        }

        // Fallback para imagens do produto pai
        return $this->product ? $this->product->all_images : [];
    }

    /**
     * Obtém a primeira imagem da variação
     */
    public function getFirstImageAttribute()
    {
        $images = $this->all_images;
        if (!empty($images)) {
            return $images[0];
        }
        return asset('images/no-image.svg');
    }

    /**
     * Obtém nome formatado com atributos
     */
    public function getFormattedNameAttribute()
    {
        if ($this->name) {
            return $this->name;
        }

        $productName = $this->product ? $this->product->name : 'Produto';
        $attributes = $this->attributeValues->map(function($value) {
            return $value->display_value ?: $value->value;
        })->implode(' - ');

        return $attributes ? "{$productName} - {$attributes}" : $productName;
    }

    /**
     * Obtém string de atributos para exibição
     */
    public function getAttributesStringAttribute()
    {
        return $this->attributeValues->map(function($value) {
            $attrName = $value->attribute->name ?? '';
            $attrValue = $value->display_value ?: $value->value;
            return "{$attrName}: {$attrValue}";
        })->implode(', ');
    }
}

