<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'display_value',
        'color_hex',
        'image_url',
        'sort_order',
        'is_active',
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

        static::creating(function ($value) {
            if (empty($value->slug)) {
                $value->slug = Str::slug($value->value);
            }
            if (empty($value->display_value)) {
                $value->display_value = $value->value;
            }
        });

        static::updating(function ($value) {
            if ($value->isDirty('value') && empty($value->slug)) {
                $value->slug = Str::slug($value->value);
            }
            if ($value->isDirty('value') && empty($value->display_value)) {
                $value->display_value = $value->value;
            }
        });
    }

    /**
     * Relacionamento com atributo
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Relacionamento com variações que usam este valor
     */
    public function variations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_attributes', 'attribute_value_id', 'variation_id');
    }

    /**
     * Scope para valores ativos
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
        return $query->orderBy('sort_order')->orderBy('value');
    }

    /**
     * Obtém o valor de exibição (display_value ou value)
     */
    public function getDisplayValueAttribute($value)
    {
        return $value ?: $this->value;
    }
}



