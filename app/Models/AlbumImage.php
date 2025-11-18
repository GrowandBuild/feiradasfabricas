<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AlbumImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id', 'path', 'alt', 'position'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
