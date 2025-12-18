<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'source',
        'reference_type',
        'reference_id',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope para reservas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope para reservas expiradas
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', false)
              ->orWhere('expires_at', '<=', now());
        });
    }

    /**
     * Verificar se está expirada
     */
    public function isExpired()
    {
        return $this->expires_at <= now() || !$this->is_active;
    }

    /**
     * Expirar reserva
     */
    public function expire()
    {
        $this->is_active = false;
        $this->save();
    }
}
