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
        'slug',
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
     * Calcula o preço B2C baseado no custo e margem de lucro do produto pai
     */
    public function getB2cPriceAttribute()
    {
        // Se já tem um valor definido explicitamente, usar ele
        if (isset($this->attributes['price']) && $this->attributes['price'] > 0) {
            return $this->attributes['price'];
        }

        // Se tem custo e produto pai com margem, calcular
        if ($this->cost_price && $this->product) {
            $margin = $this->product->profit_margin_b2c ?? 20.00;
            return $this->cost_price * (1 + ($margin / 100));
        }

        return $this->attributes['price'] ?? 0;
    }

    /**
     * Calcula o preço B2B baseado no custo e margem de lucro do produto pai
     */
    public function getCalculatedB2bPriceAttribute()
    {
        // Se já tem um valor definido explicitamente, usar ele
        if (isset($this->attributes['b2b_price']) && $this->attributes['b2b_price'] > 0) {
            return $this->attributes['b2b_price'];
        }

        // Se tem custo e produto pai com margem, calcular
        if ($this->cost_price && $this->product) {
            $margin = $this->product->profit_margin_b2b ?? 10.00;
            return $this->cost_price * (1 + ($margin / 100));
        }

        return $this->attributes['b2b_price'] ?? 0;
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

    /**
     * Gera slug amigável para a variação (usado em URL indexável)
     */
    public function getGeneratedSlugAttribute(): string
    {
        $base = $this->product ? $this->product->slug : 'produto';
        $segments = [];
        if ($this->color) { $segments[] = str_replace([' /','/','  '], ' ', strtolower($this->color)); }
        if ($this->storage) { $segments[] = strtolower(str_replace(' ', '', $this->storage)); }
        if ($this->ram) { $segments[] = strtolower(str_replace(' ', '', $this->ram)); }
        $tail = implode('-', array_filter(array_map(function($s){
            $s = preg_replace('/[^a-z0-9]+/','-',$s); return trim($s,'-');
        }, $segments)));
        return $tail ?: $base;
    }

    protected static function booted()
    {
        static::saving(function(self $variation){
            if (empty($variation->slug)) {
                $variation->slug = $variation->generated_slug;
            }
        });
    }
}
