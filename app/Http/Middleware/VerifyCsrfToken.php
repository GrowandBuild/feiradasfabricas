<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Excluir webhooks de provedores de pagamento
        'payment/stripe/webhook',
        'payment/mercadopago/notification',
        'payment/pagseguro/notification',
    ];
}
