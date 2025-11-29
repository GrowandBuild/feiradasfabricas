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
        $query = Product::query()->where('is_active', true)->where('in_stock', true);

        if (is_array($this->product_ids) && count($this->product_ids) > 0) {
            $ids = $this->product_ids;
            // preserve order
            $ordered = Product::whereIn('id', $ids)->get()->sortBy(function($p) use ($ids) {
                return array_search($p->id, $ids);
            });
            return $ordered->take($this->limit ?? 4);
        }

        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }

        return $query->limit($this->limit ?? 4)->get();
    }
}
