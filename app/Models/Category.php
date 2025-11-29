<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'icon_class',
        'cover',
        'is_active',
        'sort_order',
        'department_id',
        'show_avatar',
        'show_cover',
        'show_title',
        'show_description',
        'show_button',
        'button_position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_avatar' => 'boolean',
        'show_cover' => 'boolean',
        'show_title' => 'boolean',
        'show_description' => 'boolean',
        'show_button' => 'boolean',
        // button_position remains a string (top|center|bottom)
    ];

    /**
     * Relacionamento com departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relacionamento com produtos
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    /**
     * Scope para categorias ativas
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
}
