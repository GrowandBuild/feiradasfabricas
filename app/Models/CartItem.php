<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'customer_id',
        'product_id',
        'variation_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento com variação
     */
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    /**
     * Relacionamento com cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Obtém o produto ou variação para exibição
     */
    public function getDisplayProduct()
    {
        return $this->variation ?? $this->product;
    }

    /**
     * Obtém nome do item (produto ou variação)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->variation) {
            return $this->variation->formatted_name ?? $this->product->name;
        }
        return $this->product->name ?? 'Produto';
    }

    /**
     * Obtém SKU do item (produto ou variação)
     */
    public function getDisplaySkuAttribute()
    {
        if ($this->variation) {
            return $this->variation->sku ?? $this->product->sku;
        }
        return $this->product->sku ?? '';
    }

    /**
     * Obtém imagem do item (variação ou produto)
     */
    public function getDisplayImageAttribute()
    {
        if ($this->variation && $this->variation->first_image) {
            return $this->variation->first_image;
        }
        return $this->product->first_image ?? asset('images/no-image.svg');
    }

    /**
     * Calcula o total do item
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Scope para itens de uma sessão específica
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope para itens de um cliente específico
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
