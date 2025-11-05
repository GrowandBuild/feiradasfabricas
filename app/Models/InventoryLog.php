<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'admin_id',
        'type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'notes',
        'reference',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'in' => 'Entrada',
            'out' => 'Saída',
            'adjustment' => 'Ajuste',
            'sale' => 'Venda',
            'return' => 'Devolução',
            default => $this->type,
        };
    }
}
