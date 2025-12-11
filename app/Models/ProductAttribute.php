<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot do model - gerar slug automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });

        static::updating(function ($attribute) {
            if ($attribute->isDirty('name') && empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Relacionamento com valores do atributo
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    /**
     * Relacionamento com todos os valores (incluindo inativos)
     */
    public function allValues()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id')
                    ->orderBy('sort_order');
    }

    /**
     * Relacionamento com variações que usam este atributo
     */
    public function variations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_attributes', 'attribute_id', 'variation_id');
    }

    /**
     * Scope para atributos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Verifica se é do tipo cor
     */
    public function isColorType()
    {
        return $this->type === 'color';
    }

    /**
     * Verifica se é do tipo tamanho
     */
    public function isSizeType()
    {
        return $this->type === 'size';
    }

    /**
     * Verifica se é do tipo imagem
     */
    public function isImageType()
    {
        return $this->type === 'image';
    }
}


