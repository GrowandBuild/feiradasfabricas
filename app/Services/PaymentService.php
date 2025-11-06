<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Processar pagamento via Stripe
     */
    public function processStripePayment($amount, $currency, $paymentMethodId, $metadata = [])
    {
        try {
            $secretKey = setting('stripe_secret_key');
            
            if (empty($secretKey)) {
                throw new \Exception('Chave secreta do Stripe não configurada');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $amount * 100, // Stripe usa centavos
                'currency' => $currency,
                'payment_method' => $paymentMethodId,
                'confirm' => 'true',
                'metadata' => $metadata
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_intent_id' => $data['id'],
                    'status' => $data['status'],
                    'client_secret' => $data['client_secret']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error']['message'] ?? 'Erro desconhecido'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento Stripe: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processar pagamento via PagSeguro
     */
    public function processPagSeguroPayment($amount, $currency, $paymentData, $metadata = [])
    {
        try {
            $email = setting('pagseguro_email');
            $token = setting('pagseguro_token');
            $sandbox = setting('pagseguro_sandbox', true);
            
            if (empty($email) || empty($token)) {
                throw new \Exception('Credenciais do PagSeguro não configuradas');
            }

            $baseUrl = $sandbox ? 'https://ws.sandbox.pagseguro.uol.com.br' : 'https://ws.pagseguro.uol.com.br';

            $response = Http::post($baseUrl . '/v2/transactions', array_merge([
                'email' => $email,
                'token' => $token,
                'currency' => $currency,
                'itemId1' => '1',
                'itemDescription1' => 'Compra na Feira das Fábricas',
                'itemAmount1' => number_format($amount, 2, '.', ''),
                'itemQuantity1' => '1',
                'reference' => $metadata['order_id'] ?? uniqid(),
                'senderEmail' => $paymentData['email'] ?? '',
                'senderName' => $paymentData['name'] ?? '',
                'senderPhone' => $paymentData['phone'] ?? '',
                'shippingAddressStreet' => $paymentData['address']['street'] ?? '',
                'shippingAddressNumber' => $paymentData['address']['number'] ?? '',
                'shippingAddressComplement' => $paymentData['address']['complement'] ?? '',
                'shippingAddressDistrict' => $paymentData['address']['district'] ?? '',
                'shippingAddressPostalCode' => $paymentData['address']['postal_code'] ?? '',
                'shippingAddressCity' => $paymentData['address']['city'] ?? '',
                'shippingAddressState' => $paymentData['address']['state'] ?? '',
                'shippingAddressCountry' => 'BRA'
            ]));

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                return [
                    'success' => true,
                    'transaction_id' => (string) $xml->code,
                    'status' => 'pending',
                    'payment_url' => $sandbox ? 
                        'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=' . (string) $xml->code :
                        'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=' . (string) $xml->code
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro ao processar pagamento PagSeguro'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento PagSeguro: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processar pagamento via PayPal
     */
    public function processPayPalPayment($amount, $currency, $paymentData, $metadata = [])
    {
        try {
            $clientId = setting('paypal_client_id');
            $clientSecret = setting('paypal_client_secret');
            $sandbox = setting('paypal_sandbox', true);
            
            if (empty($clientId) || empty($clientSecret)) {
                throw new \Exception('Credenciais do PayPal não configuradas');
            }

            $baseUrl = $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

            // Obter access token
            $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if (!$tokenResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'Falha na autenticação com PayPal'
                ];
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Criar ordem de pagamento
            $orderResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => number_format($amount, 2, '.', '')
                        ],
                        'reference_id' => $metadata['order_id'] ?? uniqid()
                    ]
                ],
                'application_context' => [
                    'return_url' => route('payment.paypal.return'),
                    'cancel_url' => route('payment.paypal.cancel')
                ]
            ]);

            if ($orderResponse->successful()) {
                $orderData = $orderResponse->json();
                return [
                    'success' => true,
                    'order_id' => $orderData['id'],
                    'status' => $orderData['status'],
                    'approve_url' => collect($orderData['links'])->firstWhere('rel', 'approve')['href'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Erro ao criar ordem PayPal'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento PayPal: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processar pagamento via Mercado Pago
     */
    public function processMercadoPagoPayment($amount, $currency, $paymentData, $metadata = [])
    {
        try {
            $accessToken = setting('mercadopago_access_token');
            $sandbox = setting('mercadopago_sandbox', true);
            
            if (empty($accessToken)) {
                throw new \Exception('Access Token do Mercado Pago não configurado');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => uniqid()
            ])->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => $amount,
                'description' => 'Compra na Feira das Fábricas',
                'payment_method_id' => $paymentData['payment_method_id'] ?? 'pix',
                'payer' => [
                    'email' => $paymentData['email'] ?? '',
                    'first_name' => $paymentData['first_name'] ?? '',
                    'last_name' => $paymentData['last_name'] ?? '',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $paymentData['cpf'] ?? ''
                    ]
                ],
                'external_reference' => $metadata['order_id'] ?? uniqid()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'status' => $data['status'],
                    'payment_url' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['message'] ?? 'Erro desconhecido'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento Mercado Pago: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Criar preferência de pagamento Mercado Pago (para PIX e Boleto)
     */
    public function createMercadoPagoPreference($amount, $currency, $paymentData, $metadata = [])
    {
        try {
            $accessToken = setting('mercadopago_access_token');
            $sandbox = setting('mercadopago_sandbox', true);
            
            if (empty($accessToken)) {
                throw new \Exception('Access Token do Mercado Pago não configurado');
            }

            // Determinar método de pagamento
            $paymentMethod = $paymentData['payment_method'] ?? 'pix';
            
            // Configurar métodos de pagamento permitidos
            $paymentMethods = [];
            if ($paymentMethod === 'pix') {
                $paymentMethods = [
                    'excluded_payment_methods' => [],
                    'excluded_payment_types' => [
                        ['id' => 'credit_card'],
                        ['id' => 'debit_card'],
                        ['id' => 'ticket']
                    ],
                    'installments' => 1
                ];
            } elseif ($paymentMethod === 'boleto') {
                $paymentMethods = [
                    'excluded_payment_methods' => [],
                    'excluded_payment_types' => [
                        ['id' => 'credit_card'],
                        ['id' => 'debit_card'],
                        ['id' => 'bank_transfer']
                    ],
                    'installments' => 1
                ];
            }

            // Preparar itens
            $items = [];
            if (isset($metadata['cart_items']) && is_array($metadata['cart_items'])) {
                foreach ($metadata['cart_items'] as $item) {
                    $items[] = [
                        'title' => $item['product']['name'] ?? 'Produto',
                        'description' => $item['product']['description'] ?? '',
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => (float) ($item['price'] ?? 0),
                        'currency_id' => $currency
                    ];
                }
            } else {
                // Fallback: item único
                $items[] = [
                    'title' => 'Compra na Feira das Fábricas',
                    'description' => 'Pedido temporário',
                    'quantity' => 1,
                    'unit_price' => (float) $amount,
                    'currency_id' => $currency
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => $items,
                'payer' => [
                    'name' => ($paymentData['first_name'] ?? '') . ' ' . ($paymentData['last_name'] ?? ''),
                    'surname' => $paymentData['last_name'] ?? '',
                    'email' => $paymentData['email'] ?? '',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $paymentData['cpf'] ?? ''
                    ],
                    'address' => [
                        'street_name' => $paymentData['address']['street'] ?? '',
                        'street_number' => '',
                        'zip_code' => $paymentData['address']['postal_code'] ?? '',
                        'city' => $paymentData['address']['city'] ?? '',
                        'federal_unit' => $paymentData['address']['state'] ?? ''
                    ]
                ],
                'back_urls' => [
                    'success' => route('checkout.success', 'temp'),
                    'failure' => route('checkout.index'),
                    'pending' => route('checkout.payment.pix')
                ],
                'auto_return' => 'approved',
                'payment_methods' => $paymentMethods,
                'external_reference' => $metadata['temp_order'] ?? uniqid(),
                'metadata' => $metadata,
                'notification_url' => url('/payment/mercadopago/notification'),
                'statement_descriptor' => 'FEIRA DAS FABRICAS'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Para PIX, buscar o QR code
                $paymentUrl = null;
                $paymentId = null;
                
                if ($paymentMethod === 'pix') {
                    // Para PIX, sempre criar pagamento diretamente para obter QR code
                    $pixPayment = $this->createMercadoPagoPixPayment($amount, $currency, $paymentData, $metadata);
                    if ($pixPayment['success']) {
                        $paymentUrl = $pixPayment['payment_url'] ?? $pixPayment['qr_code_base64'] ?? null;
                        $paymentId = $pixPayment['payment_id'] ?? null;
                    } else {
                        // Se falhar, usar init_point como fallback
                        $paymentUrl = $data['init_point'] ?? null;
                    }
                } elseif ($paymentMethod === 'boleto') {
                    // Para boleto, usar init_point
                    $paymentUrl = $data['init_point'] ?? null;
                }
                
                return [
                    'success' => true,
                    'preference_id' => $data['id'],
                    'payment_id' => $paymentId,
                    'payment_url' => $paymentUrl ?? $data['init_point'] ?? null,
                    'status' => 'pending',
                    'init_point' => $data['init_point'] ?? null
                ];
            } else {
                $errorData = $response->json();
                Log::error('Erro ao criar preferência Mercado Pago: ' . json_encode($errorData));
                return [
                    'success' => false,
                    'error' => $errorData['message'] ?? 'Erro ao criar preferência de pagamento'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar preferência Mercado Pago: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Criar pagamento PIX diretamente no Mercado Pago
     */
    private function createMercadoPagoPixPayment($amount, $currency, $paymentData, $metadata = [])
    {
        try {
            $accessToken = setting('mercadopago_access_token');
            
            if (empty($accessToken)) {
                throw new \Exception('Access Token do Mercado Pago não configurado');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => uniqid()
            ])->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => (float) $amount,
                'description' => 'Compra na Feira das Fábricas',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $paymentData['email'] ?? '',
                    'first_name' => $paymentData['first_name'] ?? '',
                    'last_name' => $paymentData['last_name'] ?? '',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => preg_replace('/[^0-9]/', '', $paymentData['cpf'] ?? '')
                    ]
                ],
                'external_reference' => $metadata['temp_order'] ?? uniqid(),
                'metadata' => $metadata
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'status' => $data['status'],
                    'payment_url' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null
                ];
            } else {
                $errorData = $response->json();
                return [
                    'success' => false,
                    'error' => $errorData['message'] ?? 'Erro ao criar pagamento PIX'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento PIX: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar status de pagamento
     */
    public function checkPaymentStatus($provider, $paymentId)
    {
        switch ($provider) {
            case 'stripe':
                return $this->checkStripePaymentStatus($paymentId);
            case 'pagseguro':
                return $this->checkPagSeguroPaymentStatus($paymentId);
            case 'paypal':
                return $this->checkPayPalPaymentStatus($paymentId);
            case 'mercadopago':
                return $this->checkMercadoPagoPaymentStatus($paymentId);
            default:
                return [
                    'success' => false,
                    'error' => 'Provider não reconhecido'
                ];
        }
    }

    private function checkStripePaymentStatus($paymentIntentId)
    {
        try {
            $secretKey = setting('stripe_secret_key');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey
            ])->get('https://api.stripe.com/v1/payment_intents/' . $paymentIntentId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'],
                    'amount' => $data['amount'] / 100,
                    'currency' => $data['currency']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status Stripe: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    private function checkPagSeguroPaymentStatus($transactionId)
    {
        try {
            $email = setting('pagseguro_email');
            $token = setting('pagseguro_token');
            $sandbox = setting('pagseguro_sandbox', true);
            
            $baseUrl = $sandbox ? 'https://ws.sandbox.pagseguro.uol.com.br' : 'https://ws.pagseguro.uol.com.br';

            $response = Http::get($baseUrl . '/v3/transactions/' . $transactionId, [
                'email' => $email,
                'token' => $token
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                return [
                    'success' => true,
                    'status' => $this->mapPagSeguroStatus((string) $xml->status),
                    'amount' => (float) $xml->grossAmount,
                    'currency' => 'BRL'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status PagSeguro: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    private function checkPayPalPaymentStatus($orderId)
    {
        try {
            $clientId = setting('paypal_client_id');
            $clientSecret = setting('paypal_client_secret');
            $sandbox = setting('paypal_sandbox', true);
            
            $baseUrl = $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

            $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json()['access_token'];
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])->get($baseUrl . '/v2/checkout/orders/' . $orderId);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'status' => strtolower($data['status']),
                        'amount' => (float) $data['purchase_units'][0]['amount']['value'],
                        'currency' => $data['purchase_units'][0]['amount']['currency_code']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status PayPal: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    private function checkMercadoPagoPaymentStatus($paymentId)
    {
        try {
            $accessToken = setting('mercadopago_access_token');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken
            ])->get('https://api.mercadopago.com/v1/payments/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $this->mapMercadoPagoStatus($data['status']),
                    'amount' => $data['transaction_amount'],
                    'currency' => $data['currency_id']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status Mercado Pago: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Mapear status do PagSeguro para status padrão
     */
    private function mapPagSeguroStatus($status)
    {
        $statusMap = [
            '1' => 'pending',    // Aguardando pagamento
            '2' => 'reviewing',  // Em análise
            '3' => 'paid',       // Paga
            '4' => 'available',  // Disponível
            '5' => 'dispute',    // Em disputa
            '6' => 'returned',   // Devolvida
            '7' => 'cancelled'   // Cancelada
        ];

        return $statusMap[$status] ?? 'unknown';
    }

    /**
     * Mapear status do Mercado Pago para status padrão
     */
    private function mapMercadoPagoStatus($status)
    {
        $statusMap = [
            'pending' => 'pending',
            'approved' => 'paid',
            'authorized' => 'authorized',
            'in_process' => 'processing',
            'in_mediation' => 'dispute',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'chargeback'
        ];

        return $statusMap[$status] ?? 'unknown';
    }
}
