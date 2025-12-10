<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Stripe: validate Stripe-Signature header using secret
        if (str_contains($path, 'stripe')) {
            $header = $request->header('Stripe-Signature');
            $secret = \App\Models\Setting::get('stripe_webhook_secret', null);

            if (empty($header) || empty($secret)) {
                Log::warning('Stripe webhook rejected: missing signature or secret');
                return response()->json(['error' => 'invalid_signature'], 401);
            }

            $payload = $request->getContent();
            $parts = [];
            foreach (explode(',', $header) as $pair) {
                [$k, $v] = array_map('trim', explode('=', $pair, 2) + [1 => null]);
                $parts[$k] = $v;
            }

            $timestamp = $parts['t'] ?? null;
            $sig = $parts['v1'] ?? null;

            if (!$timestamp || !$sig) {
                Log::warning('Stripe webhook rejected: malformed signature header');
                return response()->json(['error' => 'invalid_signature'], 401);
            }

            $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
            if (!hash_equals($expected, $sig)) {
                Log::warning('Stripe webhook rejected: signature mismatch');
                return response()->json(['error' => 'invalid_signature'], 401);
            }
        }

        // Generic: if a provider secret is configured and an X-Hub-Signature header exists,
        // verify HMAC sha256(payload, secret).
        $hubHeader = $request->header('X-Hub-Signature') ?: $request->header('X-Signature');
        if ($hubHeader) {
            $provider = 'generic';
            $secret = \App\Models\Setting::get('webhook_' . $provider . '_secret', null);

            if ($secret) {
                $payload = $request->getContent();
                $expected = hash_hmac('sha256', $payload, $secret);
                // header format may be 'sha256=...' or raw
                $sig = str_contains($hubHeader, '=') ? explode('=', $hubHeader, 2)[1] : $hubHeader;
                if (!hash_equals($expected, $sig)) {
                    Log::warning('Webhook rejected: X-Hub-Signature mismatch');
                    return response()->json(['error' => 'invalid_signature'], 401);
                }
            }
        }

        return $next($request);
    }
}
