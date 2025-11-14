<?php

return [
    'providers' => [
        // Por segurança, defaults conservadores: Correios desativado por padrão.
        'correios' => false,
        'jadlog' => false,
        'total_express' => false,
        'loggi' => false,
        'melhor_envio' => false,
    ],
    'timeout' => 10,
    'cache_ttl' => 600, // 10 min
    'defaults' => [
        // Valores padrão para quando o produto não informar dimensões/peso
        'min_weight' => 0.3,     // kg mínimo considerado
        'fallback_weight' => 1.0,// kg quando nada informado
        'length' => 20,          // cm
        'height' => 20,          // cm
        'width'  => 20,          // cm
    ],
];
