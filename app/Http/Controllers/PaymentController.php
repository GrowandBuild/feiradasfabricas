<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Processa notificação do Mercado Pago
     */
    public function handleMercadoPagoNotification(Request $request)
    {
        try {
            Log::info('Notificação do Mercado Pago recebida', $request->all());

            // Verificar se é uma notificação de pagamento
            if ($request->has('type') && $request->type === 'payment') {
                $paymentId = $request->input('data.id');
                
                if ($paymentId) {
                    // Buscar pedido pelo payment_id nos detalhes de pagamento
                    $order = Order::whereJsonContains('payment_details->payment_id', $paymentId)->first();
                    
                    if (!$order) {
                        // Se não encontrar pelo payment_id, tentar buscar pelo external_reference
                        $accessToken = setting('mercadopago_access_token');
                        $response = \Http::withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken
                        ])->get('https://api.mercadopago.com/v1/payments/' . $paymentId);

                        if ($response->successful()) {
                            $paymentData = $response->json();
                            $externalReference = $paymentData['external_reference'] ?? null;
                            
                            if ($externalReference) {
                                $order = Order::where('id', $externalReference)->first();
                            }
                        }
                    }
                    
                    if ($order) {
                        // Buscar informações atualizadas do pagamento
                        $paymentService = new PaymentService();
                        $paymentStatus = $paymentService->checkPaymentStatus('mercadopago', $paymentId);
                        
                        if ($paymentStatus['success']) {
                            // Atualizar status do pedido
                            $order->update([
                                'payment_status' => $paymentStatus['status']
                            ]);

                            // Se pagamento aprovado, atualizar status do pedido
                            if ($paymentStatus['status'] === 'paid' || $paymentStatus['status'] === 'approved') {
                                $order->update([
                                    'status' => 'confirmed',
                                    'payment_status' => 'paid'
                                ]);
                            }

                            Log::info("Pedido {$order->order_number} atualizado para status: {$paymentStatus['status']}");
                        }
                    }
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação do Mercado Pago: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
