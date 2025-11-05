<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'title',
        'image',
        'link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope para selos ativos
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
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Obtém a URL da imagem
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/no-image.svg');
        }

        // Se for URL completa, retornar como está
        if (strpos($this->image, 'http') === 0) {
            return $this->image;
        }

        // Se for caminho local, retornar com asset
        return asset('storage/' . $this->image);
    }
}
