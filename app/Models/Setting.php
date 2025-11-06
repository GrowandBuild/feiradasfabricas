<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get($key, $default = null)
    {
        // Buscar diretamente do banco sem cache
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Converter baseado no tipo
        $value = match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'number' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
        
        return $value;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        // Converter valor para string se necessário (exceto arrays que viram JSON)
        $valueToStore = is_array($value) ? json_encode($value) : (string) $value;
        
        // Buscar o registro existente primeiro
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            // Se existe, atualizar explicitamente todos os campos
            $setting->value = $valueToStore;
            $setting->type = $type;
            $setting->group = $group;
            $setting->save();
        } else {
            // Se não existe, criar novo
            $setting = static::create([
                'key' => $key,
                'value' => $valueToStore,
                'type' => $type,
                'group' => $group,
            ]);
        }

        // Recarregar do banco para garantir que está atualizado
        $setting->refresh();

        return $setting;
    }
}
