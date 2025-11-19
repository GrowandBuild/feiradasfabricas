<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'type',
        'reference',
        'reference_id',
        'title',
        'sort_order',
        'enabled',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'enabled' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
