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
        'stock_quantity' => 'integer',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get attributes as array for display
     */
    public function getAttributesAttribute()
    {
        return [
            'Cor' => $this->color,
            'RAM' => $this->ram,
            'Armazenamento' => $this->storage,
        ];
    }
}
