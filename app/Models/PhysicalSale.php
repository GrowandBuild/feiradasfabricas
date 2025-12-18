<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'admin_id',
        'customer_id',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_confirmed_at',
        'payment_confirmed_by',
        'installments',
        'status',
        'notes',
        'synced_to_ecommerce',
        'synced_at',
        'nfe_issued',
        'nfe_key',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'synced_to_ecommerce' => 'boolean',
        'nfe_issued' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * Relacionamento com admin (operador do caixa)
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Relacionamento com cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relacionamento com itens da venda
     */
    public function items()
    {
        return $this->hasMany(PhysicalSaleItem::class);
    }

    /**
     * Gerar número de venda único
     */
    public static function generateSaleNumber()
    {
        $year = date('Y');
        $lastSale = static::where('sale_number', 'like', "VF-{$year}-%")
            ->orderBy('sale_number', 'desc')
            ->first();

        if ($lastSale) {
            $number = (int) substr($lastSale->sale_number, -3) + 1;
        } else {
            $number = 1;
        }

        return sprintf('VF-%s-%03d', $year, $number);
    }
}
