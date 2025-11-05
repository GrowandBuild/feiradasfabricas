<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'mobile_image',
        'link',
        'position',
        'sort_order',
        'starts_at',
        'expires_at',
        'is_active',
        'target_audience',
        'department_id',
        // Campos de estilo
        'text_color',
        'text_size',
        'text_align',
        'text_font_weight',
        'text_padding_top',
        'text_padding_bottom',
        'text_padding_left',
        'text_padding_right',
        'text_margin_top',
        'text_margin_bottom',
        'text_margin_left',
        'text_margin_right',
        'text_shadow_color',
        'text_shadow_blur',
        'description_color',
        'description_size',
        'description_align',
        'description_margin_top',
        'banner_background_color',
        'banner_height',
        'banner_padding_top',
        'banner_padding_bottom',
        'show_title',
        'show_description',
        'show_overlay',
        'overlay_color',
        'overlay_opacity',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'show_title' => 'boolean',
        'show_description' => 'boolean',
        'show_overlay' => 'boolean',
    ];

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->isAfter($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeForAudience($query, $audience)
    {
        return $query->where(function ($q) use ($audience) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', $audience);
        });
    }

    /**
     * Relacionamento com departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope para banners por departamento
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->whereNull('department_id')
              ->orWhere('department_id', $departmentId);
        });
    }

    /**
     * Scope para banners globais (sem departamento específico)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('department_id');
    }
}
