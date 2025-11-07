<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ram',
        'storage',
        'color',
        'color_hex',
        'sku',
        'price',
        'b2b_price',
        'cost_price',
        'stock_quantity',
        'in_stock',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
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
     * Verifica se a variação está em estoque
     */
    public function hasStock($quantity = 1)
    {
        if (!$this->in_stock) {
            return false;
        }

        return $this->stock_quantity >= $quantity;
    }

    /**
     * Obtém o nome completo da variação
     */
    public function getFullNameAttribute()
    {
        $parts = [];
        if ($this->ram) $parts[] = $this->ram;
        if ($this->storage) $parts[] = $this->storage;
        if ($this->color) $parts[] = $this->color;
        
        return implode(' / ', $parts);
    }
}
