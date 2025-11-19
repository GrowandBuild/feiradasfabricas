<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'department_id',
    ];

    // Helpful accessor for normalized key
    public function getKeyNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }
}
