<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Relacionamento com produtos
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relacionamento com categorias
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relacionamento com banners
     */
    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    /**
     * Scope para departamentos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordenado por sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Obtém a URL do departamento
     */
    public function getUrlAttribute()
    {
        return route('department', $this->slug);
    }

    /**
     * Obtém produtos em destaque do departamento
     */
    public function getFeaturedProductsAttribute()
    {
        return $this->products()->active()->featured()->take(8)->get();
    }

    /**
     * Obtém o total de produtos do departamento
     */
    public function getTotalProductsAttribute()
    {
        return $this->products()->active()->count();
    }
}
