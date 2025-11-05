<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_active',
        'company_name',
        'cnpj',
        'ie',
        'contact_person',
        'department',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'country',
        'b2b_status',
        'b2b_notes',
        'credit_limit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    /**
     * Relacionamento com pedidos
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relacionamento com itens do carrinho
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope para clientes B2C
     */
    public function scopeB2C($query)
    {
        return $query->where('type', 'b2c');
    }

    /**
     * Scope para clientes B2B
     */
    public function scopeB2B($query)
    {
        return $query->where('type', 'b2b');
    }

    /**
     * Scope para clientes B2B aprovados
     */
    public function scopeB2BApproved($query)
    {
        return $query->where('type', 'b2b')->where('b2b_status', 'approved');
    }

    /**
     * Verifica se é cliente B2B aprovado
     */
    public function isB2BApproved()
    {
        return $this->type === 'b2b' && $this->b2b_status === 'approved';
    }

    /**
     * Obtém o nome completo
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Obtém o nome da empresa ou nome completo
     */
    public function getDisplayNameAttribute()
    {
        if ($this->type === 'b2b' && $this->company_name) {
            return $this->company_name;
        }
        return $this->full_name;
    }

    /**
     * Obtém o endereço completo
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address;
        if ($this->number) {
            $address .= ', ' . $this->number;
        }
        if ($this->complement) {
            $address .= ', ' . $this->complement;
        }
        if ($this->neighborhood) {
            $address .= ', ' . $this->neighborhood;
        }
        if ($this->city) {
            $address .= ', ' . $this->city;
        }
        if ($this->state) {
            $address .= ' - ' . $this->state;
        }
        if ($this->zip_code) {
            $address .= ', ' . $this->zip_code;
        }
        return $address;
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'target');
    }
}
