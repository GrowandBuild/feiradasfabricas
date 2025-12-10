<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'image',
        'link',
        'position',
        'auto_close_seconds',
        'show_close_button',
        'is_active',
    ];

    protected $casts = [
        'auto_close_seconds' => 'integer',
        'show_close_button' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
