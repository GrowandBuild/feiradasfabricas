<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
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
            Log::info('Webhook Mercado Pago recebido', $request->all());

            $data = $request->all();
            $type = $data['type'] ?? '';
            $action = $data['action'] ?? '';

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
            Log::info('Webhook PagSeguro recebido', $request->all());

            $notificationCode = $request->input('notificationCode');
            $notificationType = $request->input('notificationType');

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
            Log::info('Webhook PayPal recebido', $request->all());

            $data = $request->all();
            $eventType = $data['event_type'] ?? '';

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
}

