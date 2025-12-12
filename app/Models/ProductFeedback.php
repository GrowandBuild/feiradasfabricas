<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductFeedback extends Model
{
    use HasFactory;

    protected $table = 'product_feedbacks';

    protected $fillable = [
        'product_id',
        'customer_id',
        'admin_id',
        'text',
        'image',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento com cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relacionamento com admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Retorna a URL da imagem
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return Storage::url($this->image);
    }

    /**
     * Retorna o nome do autor do feedback
     */
    public function getAuthorNameAttribute()
    {
        if ($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }

        if ($this->admin) {
            return $this->admin->name;
        }

        return 'Anônimo';
    }

    /**
     * Scope para feedbacks aprovados
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope para feedbacks de um produto
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
