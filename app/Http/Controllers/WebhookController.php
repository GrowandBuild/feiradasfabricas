<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\WebhookEvent;
use App\Services\PaymentService;

class WebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Webhook do Mercado Pago
     */
    public function mercadoPago(Request $request)
    {
        try {
            Log::info('Webhook Mercado Pago recebido', ['keys' => array_keys($request->all())]);

            $data = $request->all();
            $type = $data['type'] ?? '';
            $action = $data['action'] ?? '';
            // Idempotência: evitar processar notificação duplicada
            $mpEventId = $data['data']['id'] ?? null;
            if ($mpEventId && !$this->recordWebhookEvent('mercadopago', $mpEventId)) {
                Log::info('Webhook Mercado Pago duplicado ignorado', ['id' => $mpEventId]);
                return response()->json(['status' => 'duplicate'], 200);
            }

            // Processar notificação de pagamento
            if ($type === 'payment' && $action === 'payment.updated') {
                $paymentId = $data['data']['id'] ?? null;
                
                if ($paymentId) {
                    $this->processPaymentNotification($paymentId);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Erro no webhook Mercado Pago: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Processar notificação de pagamento
     */
    private function processPaymentNotification($paymentId)
    {
        try {
            // Buscar pedido pelo payment_id
            $order = Order::whereJsonContains('payment_details->payment_id', $paymentId)->first();
            
            if (!$order) {
                Log::warning('Pedido não encontrado para payment_id: ' . $paymentId);
                return;
            }

            // Verificar status do pagamento
            $statusResult = $this->paymentService->checkPaymentStatus('mercadopago', $paymentId);
            
            if ($statusResult['success']) {
                $newStatus = $statusResult['status'];
                $currentStatus = $order->payment_status;

                // Atualizar status se mudou
                if ($newStatus !== $currentStatus) {
                    $order->update([
                        'payment_status' => $newStatus,
                        'payment_details' => json_encode(array_merge(
                            json_decode($order->payment_details, true),
                            ['status' => $newStatus]
                        ))
                    ]);

                    Log::info("Status do pedido {$order->order_number} atualizado: {$currentStatus} -> {$newStatus}");

                    // Se pagamento aprovado, marcar pedido como pago
                    if ($newStatus === 'approved' || $newStatus === 'paid') {
                        $order->update(['status' => 'paid']);
                        Log::info("Pedido {$order->order_number} marcado como pago");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação de pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Webhook do PagSeguro
     */
    public function pagSeguro(Request $request)
    {
        try {
            Log::info('Webhook PagSeguro recebido', ['keys' => array_keys($request->all())]);

            $notificationCode = $request->input('notificationCode');
            $notificationType = $request->input('notificationType');

            if ($notificationCode && !$this->recordWebhookEvent('pagseguro', $notificationCode)) {
                Log::info('Webhook PagSeguro duplicado ignorado', ['code' => $notificationCode]);
                return response()->json(['status' => 'duplicate'], 200);
            }

            if ($notificationCode && $notificationType === 'transaction') {
                $this->processPagSeguroNotification($notificationCode);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Erro no webhook PagSeguro: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Processar notificação do PagSeguro
     */
    private function processPagSeguroNotification($notificationCode)
    {
        try {
            // Buscar pedido pela referência (notificationCode)
            $order = Order::where('payment_reference', $notificationCode)->first();
            
            if (!$order) {
                Log::warning('Pedido não encontrado para notificationCode: ' . $notificationCode);
                return;
            }

            // Verificar status do pagamento
            $statusResult = $this->paymentService->checkPaymentStatus('pagseguro', $notificationCode);
            
            if ($statusResult['success']) {
                $newStatus = $statusResult['status'];
                $currentStatus = $order->payment_status;

                // Atualizar status se mudou
                if ($newStatus !== $currentStatus) {
                    $order->update(['payment_status' => $newStatus]);
                    Log::info("Status do pedido {$order->order_number} atualizado: {$currentStatus} -> {$newStatus}");

                    // Se pagamento aprovado, marcar pedido como pago
                    if ($newStatus === 'approved' || $newStatus === 'paid') {
                        $order->update(['status' => 'paid']);
                        Log::info("Pedido {$order->order_number} marcado como pago");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação PagSeguro: ' . $e->getMessage());
        }
    }

    /**
     * Webhook do PayPal
     */
    public function payPal(Request $request)
    {
        try {
            Log::info('Webhook PayPal recebido', ['keys' => array_keys($request->all())]);

            $data = $request->all();
            $eventType = $data['event_type'] ?? '';

            $paypalEventId = $data['id'] ?? ($data['resource']['id'] ?? null);
            if ($paypalEventId && !$this->recordWebhookEvent('paypal', $paypalEventId)) {
                Log::info('Webhook PayPal duplicado ignorado', ['id' => $paypalEventId]);
                return response()->json(['status' => 'duplicate'], 200);
            }

            // Processar evento de pagamento aprovado
            if ($eventType === 'PAYMENT.SALE.COMPLETED') {
                $paymentId = $data['resource']['id'] ?? null;
                
                if ($paymentId) {
                    $this->processPayPalNotification($paymentId);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Erro no webhook PayPal: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Processar notificação do PayPal
     */
    private function processPayPalNotification($paymentId)
    {
        try {
            // Buscar pedido pelo payment_id
            $order = Order::whereJsonContains('payment_details->payment_id', $paymentId)->first();
            
            if (!$order) {
                Log::warning('Pedido não encontrado para payment_id PayPal: ' . $paymentId);
                return;
            }

            // Verificar status do pagamento
            $statusResult = $this->paymentService->checkPaymentStatus('paypal', $paymentId);
            
            if ($statusResult['success']) {
                $newStatus = $statusResult['status'];
                $currentStatus = $order->payment_status;

                // Atualizar status se mudou
                if ($newStatus !== $currentStatus) {
                    $order->update([
                        'payment_status' => $newStatus,
                        'payment_details' => json_encode(array_merge(
                            json_decode($order->payment_details, true),
                            ['status' => $newStatus]
                        ))
                    ]);

                    Log::info("Status do pedido {$order->order_number} atualizado: {$currentStatus} -> {$newStatus}");

                    // Se pagamento aprovado, marcar pedido como pago
                    if ($newStatus === 'approved' || $newStatus === 'paid') {
                        $order->update(['status' => 'paid']);
                        Log::info("Pedido {$order->order_number} marcado como pago");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação PayPal: ' . $e->getMessage());
        }
    }

    /**
     * Webhook do Stripe (validação de assinatura)
     */
    public function stripe(Request $request)
    {
        try {
            // Verificar assinatura do Stripe
            if (!$this->verifyStripeSignature($request)) {
                Log::warning('Stripe webhook: assinatura inválida ou ausente');
                return response()->json(['status' => 'invalid_signature'], 401);
            }

            Log::info('Webhook Stripe recebido', ['keys' => array_keys($request->all())]);

            $payload = $request->all();

            // Tentar extrair payment id / intent

            // Registrar evento Stripe (id do evento) para idempotência
            $stripeEventId = $payload['id'] ?? null;
            if ($stripeEventId && !$this->recordWebhookEvent('stripe', $stripeEventId)) {
                Log::info('Webhook Stripe duplicado ignorado', ['event_id' => $stripeEventId]);
                return response()->json(['status' => 'duplicate'], 200);
            }

            $paymentId = $payload['data']['object']['id'] ?? $payload['data']['object']['payment_intent'] ?? null;

            if ($paymentId) {
                $this->processPayPalNotification($paymentId); // reusar lógica de verificação/atualização
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Erro no webhook Stripe: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Verifica assinatura do Stripe usando o header `Stripe-Signature` e o segredo configurado.
     */
    protected function verifyStripeSignature(Request $request): bool
    {
        $header = $request->header('Stripe-Signature');
        $secret = \App\Models\Setting::get('stripe_webhook_secret', null);

        if (empty($header) || empty($secret)) {
            return false;
        }

        $payload = $request->getContent();

        // Cabeçalho no formato: t=timestamp,v1=signature,v0=...
        $parts = [];
        foreach (explode(',', $header) as $pair) {
            [$k, $v] = array_map('trim', explode('=', $pair, 2) + [1 => null]);
            $parts[$k] = $v;
        }

        $timestamp = $parts['t'] ?? null;
        $sig = $parts['v1'] ?? null;

        if (!$timestamp || !$sig) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($expected, $sig);
    }

    /**
     * Registra um evento de webhook para garantir idempotência.
     * Retorna true se o evento foi registrado agora, false se já existia.
     */
    protected function recordWebhookEvent(string $provider, string $eventId): bool
    {
        $key = "webhook_event:{$provider}:" . md5($eventId);

        // Primeiro: tentar persistir no DB usando unique constraint (idempotência robusta).
        try {
            $record = WebhookEvent::firstOrCreate(
                ['provider' => $provider, 'event_id' => $eventId],
                ['payload' => null, 'received_at' => now()]
            );

            // wasRecentlyCreated é true se o registro acabou de ser criado (não existia antes)
            return (bool) ($record->wasRecentlyCreated ?? false);
        } catch (\Exception $e) {
            // Se o DB estiver indisponível, cair para o fallback em cache.
            Log::warning('DB indisponível ao registrar evento de webhook: ' . $e->getMessage());
            try {
                return Cache::add($key, true, 60 * 24);
            } catch (\Exception $e2) {
                Log::warning('Cache indisponível ao registrar evento de webhook (fallback): ' . $e2->getMessage());
                // Conservador: indicar que o evento NÃO foi registrado para evitar duplicidade
                return false;
            }
        }
    }
}

