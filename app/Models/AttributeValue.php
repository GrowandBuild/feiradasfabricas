<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id', 'value', 'hex', 'sort_order', 'is_active', 'meta'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
