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
    // TTL do cache de cotação (segundos). Ajuste conforme perfil de tráfego.
    // Se alterar, as chaves antigas serão naturalmente expiradas; incluir no signature evita reuse indevido.
    'cache_ttl' => 600, // 10 min
    // Logar hits de cache para auditoria? (impacto mínimo)
    'cache_log_hits' => true,
    // Prefixo centralizado das chaves de cache para facilitar limpeza seletiva futura.
    'cache_prefix' => 'shipping_quotes',
    // Estratégia de agregação de pacotes:
    // single = consolida em um único pacote volumétrico (atual padrão)
    // multi  = envia cada pacote saneado individualmente ao provider
    'aggregate_strategy' => 'single',
    'defaults' => [
        // Valores padrão para quando o produto não informar dimensões/peso
        'min_weight' => 0.3,     // kg mínimo considerado
        'fallback_weight' => 1.0,// kg quando nada informado
        'length' => 20,          // cm
        'height' => 20,          // cm
        'width'  => 20,          // cm
    ],
];
