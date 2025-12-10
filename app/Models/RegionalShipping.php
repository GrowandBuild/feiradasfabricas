<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionalShipping extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'regional_shipping';

    protected $fillable = [
        'name',
        'description',
        'cep_start',
        'cep_end',
        'cep_list',
        'pricing_type',
        'fixed_price',
        'price_per_kg',
        'price_per_item',
        'min_price',
        'max_price',
        'delivery_days_min',
        'delivery_days_max',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'fixed_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'price_per_item' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'delivery_days_min' => 'integer',
        'delivery_days_max' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Verifica se um CEP está dentro desta região
     */
    public function matchesCep(string $cep): bool
    {
        $normalizedCep = preg_replace('/\D+/', '', $cep);
        
        if (strlen($normalizedCep) !== 8) {
            return false;
        }

        // Verificar lista de CEPs específicos (pode ser array ou string JSON)
        if (!empty($this->cep_list)) {
            $cepList = $this->cep_list;
            
            // Se for string, tentar decodificar JSON
            if (is_string($cepList)) {
                $decoded = json_decode($cepList, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $cepList = $decoded;
                } else {
                    // Tentar como string separada por vírgula
                    $cepList = array_map('trim', explode(',', $cepList));
                }
            }
            
            // Verificar se o CEP está na lista
            if (is_array($cepList)) {
                foreach ($cepList as $listedCep) {
                    $normalizedListedCep = preg_replace('/\D+/', '', (string) $listedCep);
                    if ($normalizedListedCep === $normalizedCep) {
                        return true;
                    }
                }
            }
        }

        // Verificar range de CEPs
        if (!empty($this->cep_start) && !empty($this->cep_end)) {
            $cepNum = (int) $normalizedCep;
            $startNum = (int) preg_replace('/\D+/', '', $this->cep_start);
            $endNum = (int) preg_replace('/\D+/', '', $this->cep_end);
            return $cepNum >= $startNum && $cepNum <= $endNum;
        }

        return false;
    }

    /**
     * Calcula o preço do frete baseado no tipo de precificação
     */
    public function calculatePrice(float $weight = 0, int $quantity = 1): float
    {
        $price = 0;

        switch ($this->pricing_type) {
            case 'fixed':
                $price = (float) ($this->fixed_price ?? 0);
                break;
            case 'per_weight':
                $price = (float) ($this->price_per_kg ?? 0) * max(0, $weight);
                break;
            case 'per_item':
                $price = (float) ($this->price_per_item ?? 0) * max(1, $quantity);
                break;
        }

        // Aplicar limites mínimo e máximo
        if ($this->min_price !== null && $price < $this->min_price) {
            $price = (float) $this->min_price;
        }
        if ($this->max_price !== null && $price > $this->max_price) {
            $price = (float) $this->max_price;
        }

        return round($price, 2);
    }

    /**
     * Retorna o prazo de entrega formatado
     */
    public function getDeliveryTimeAttribute(): string
    {
        if ($this->delivery_days_max) {
            return "{$this->delivery_days_min} a {$this->delivery_days_max} dias úteis";
        }
        return "{$this->delivery_days_min} dia" . ($this->delivery_days_min > 1 ? 's' : '') . " útil" . ($this->delivery_days_min > 1 ? 'eis' : '');
    }

    /**
     * Scope para buscar apenas regiões ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por prioridade
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
