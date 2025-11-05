<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Helper global para acessar configurações
     */
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('set_setting')) {
    /**
     * Helper global para definir configurações
     */
    function set_setting($key, $value, $type = 'string', $group = 'general')
    {
        return Setting::set($key, $value, $type, $group);
    }
}
