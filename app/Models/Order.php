<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'payment_status',
        'shipping_status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address',
        'shipping_number',
        'shipping_complement',
        'shipping_neighborhood',
        'shipping_city',
        'shipping_state',
        'shipping_zip_code',
        'shipping_phone',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address',
        'billing_number',
        'billing_complement',
        'billing_neighborhood',
        'billing_city',
        'billing_state',
        'billing_zip_code',
        'payment_method',
        'payment_details',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relacionamento com cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relacionamento com itens do pedido
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relacionamento com usos de cupons
     */
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope para pedidos por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para pedidos de um cliente
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Gera número do pedido
     */
    public static function generateOrderNumber()
    {
        $prefix = 'FD';
        $date = now()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', $prefix . $date . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtém o endereço de entrega completo
     */
    public function getShippingAddressFullAttribute()
    {
        $address = $this->shipping_address;
        if ($this->shipping_number) {
            $address .= ', ' . $this->shipping_number;
        }
        if ($this->shipping_complement) {
            $address .= ', ' . $this->shipping_complement;
        }
        if ($this->shipping_neighborhood) {
            $address .= ', ' . $this->shipping_neighborhood;
        }
        if ($this->shipping_city) {
            $address .= ', ' . $this->shipping_city;
        }
        if ($this->shipping_state) {
            $address .= ' - ' . $this->shipping_state;
        }
        if ($this->shipping_zip_code) {
            $address .= ', ' . $this->shipping_zip_code;
        }
        return $address;
    }

    /**
     * Obtém o endereço de cobrança completo
     */
    public function getBillingAddressFullAttribute()
    {
        $address = $this->billing_address;
        if ($this->billing_number) {
            $address .= ', ' . $this->billing_number;
        }
        if ($this->billing_complement) {
            $address .= ', ' . $this->billing_complement;
        }
        if ($this->billing_neighborhood) {
            $address .= ', ' . $this->billing_neighborhood;
        }
        if ($this->billing_city) {
            $address .= ', ' . $this->billing_city;
        }
        if ($this->billing_state) {
            $address .= ' - ' . $this->billing_state;
        }
        if ($this->billing_zip_code) {
            $address .= ', ' . $this->billing_zip_code;
        }
        return $address;
    }

    /**
     * Obtém o label do status
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$this->status] ?? 'Desconhecido';
    }

    /**
     * Obtém a cor do status
     */
    public function getStatusColor()
    {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Obtém o label do status de pagamento
     */
    public function getPaymentStatusLabel()
    {
        $labels = [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'partial' => 'Parcial',
            'refunded' => 'Reembolsado',
            'failed' => 'Falhou',
        ];

        return $labels[$this->payment_status] ?? 'Desconhecido';
    }

    /**
     * Obtém a cor do status de pagamento
     */
    public function getPaymentStatusColor()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'partial' => 'info',
            'refunded' => 'secondary',
            'failed' => 'danger',
        ];

        return $colors[$this->payment_status] ?? 'secondary';
    }

    /**
     * Obtém o label do status de entrega
     */
    public function getShippingStatusLabel()
    {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$this->shipping_status] ?? 'Desconhecido';
    }

    /**
     * Obtém a cor do status de entrega
     */
    public function getShippingStatusColor()
    {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->shipping_status] ?? 'secondary';
    }

    /**
     * Obtém o label do método de pagamento
     */
    public function getPaymentMethodLabel()
    {
        $labels = [
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'pix' => 'PIX',
            'boleto' => 'Boleto Bancário',
            'transfer' => 'Transferência Bancária',
        ];

        return $labels[$this->payment_method] ?? 'Desconhecido';
    }
}
