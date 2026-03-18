<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'department_id',
        'product_ids',
        'limit',
        'position',
        'enabled',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'enabled' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the products for this section.
     * If product_ids is present, return those products in given order.
     * Otherwise, if department_id is set, return products from that department.
     */
    public function getProducts()
    {
        $query = Product::query()->where('is_active', true)->where('in_stock', true)
            ->with(['variations' => function($q) {
                $q->where('in_stock', true);
            }]);

        // Lógica 1: NOVA - Buscar produtos que têm esta seção em homepage_section_ids (PRIORIDADE)
        $productsWithThisSection = Product::where(function($q) {
            $q->whereNotNull('homepage_section_ids')
              ->whereJsonContains('homepage_section_ids', $this->id);
        })
        ->where('is_active', true)
        ->where('in_stock', true)
        ->with(['variations' => function($q) {
            $q->where('in_stock', true);
        }])
        ->limit($this->limit ?? 4)
        ->get();

        // Se encontrou produtos pela nova lógica, retornar eles
        if ($productsWithThisSection->count() > 0) {
            return $productsWithThisSection;
        }

        // Lógica 2: Se há product_ids específicos (mantido para compatibilidade)
        if (is_array($this->product_ids) && count($this->product_ids) > 0) {
            $ids = $this->product_ids;
            // preserve order
            $ordered = Product::whereIn('id', $ids)
                ->with(['variations' => function($q) {
                    $q->where('in_stock', true);
                }])
                ->get()
                ->sortBy(function($p) use ($ids) {
                    return array_search($p->id, $ids);
                });
            return $ordered->take($this->limit ?? 4);
        }

        // Lógica 3: Se há department_id (mantido para compatibilidade)
        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
            return $query->limit($this->limit ?? 4)->get();
        }

        // Se não encontrou nada, retornar coleção vazia
        return collect();
    }
}
