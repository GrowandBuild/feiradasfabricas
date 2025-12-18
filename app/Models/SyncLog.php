<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'sync_type',
        'direction',
        'status',
        'data',
        'error_message',
        'retry_count',
        'synced_at',
    ];

    protected $casts = [
        'data' => 'array',
        'synced_at' => 'datetime',
    ];

    /**
     * Scope para sincronizações pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para sincronizações bem-sucedidas
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope para sincronizações falhadas
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Marcar como sucesso
     */
    public function markAsSuccess($data = null)
    {
        $this->status = 'success';
        $this->synced_at = now();
        if ($data) {
            $this->data = $data;
        }
        $this->save();
    }

    /**
     * Marcar como falha
     */
    public function markAsFailed($errorMessage)
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->retry_count++;
        $this->save();
    }
}
