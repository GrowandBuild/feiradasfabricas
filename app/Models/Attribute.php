<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'key', 'department_id', 'sort_order', 'is_active', 'meta'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
