<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'is_published', 'cover_path'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(AlbumImage::class)->orderBy('position');
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_path) return null;
        return Storage::url($this->cover_path);
    }
}
