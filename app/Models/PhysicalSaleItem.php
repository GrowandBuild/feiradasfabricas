<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'physical_sale_id',
        'product_id',
        'product_variation_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relacionamento com venda física
     */
    public function physicalSale()
    {
        return $this->belongsTo(PhysicalSale::class);
    }

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento com variação do produto
     */
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
