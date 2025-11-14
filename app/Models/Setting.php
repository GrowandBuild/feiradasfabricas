<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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

    /** Keys que serão criptografadas em repouso */
    protected static array $encryptedKeys = [
        'melhor_envio_token',
        'melhor_envio_client_secret',
    ];

    public static function get($key, $default = null)
    {
        // Buscar diretamente do banco sem cache
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        $raw = $setting->value;
        // Descriptografar se for chave sensível
        if (in_array($setting->key, static::$encryptedKeys, true)) {
            try { $raw = Crypt::decryptString($raw); } catch (\Throwable $e) { /* se falhar, mantém raw */ }
        }
        // Converter baseado no tipo
        $value = match ($setting->type) {
            'boolean' => (bool) $raw,
            'number' => (float) $raw,
            'json' => json_decode($raw, true),
            default => $raw,
        };
        
        return $value;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        // Converter valor para string se necessário (exceto arrays que viram JSON)
        $valueToStore = is_array($value) ? json_encode($value) : (string) $value;
        // Criptografar se for chave sensível
        if (in_array($key, static::$encryptedKeys, true)) {
            try { $valueToStore = Crypt::encryptString($valueToStore); } catch (\Throwable $e) { /* se falhar, persiste sem criptografia */ }
        }
        
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
