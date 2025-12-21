<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'variation_id',
        'attribute_id',
        'attribute_value_id',
    ];

    /**
     * Relacionamento com variação
     */
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }

    /**
     * Relacionamento com atributo
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Relacionamento com valor do atributo
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}



