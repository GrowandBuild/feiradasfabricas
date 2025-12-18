<?php

namespace App\Services;

use App\Models\PhysicalSale;
use App\Services\PaymentGateways\GatewayFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Serviço para integração com maquininhas de cartão no PDV
 * 
 * Suporta diferentes modos:
 * 1. Manual - Operador processa na maquininha e registra no sistema
 * 2. TEF (Transferência Eletrônica de Fundos) - Integração direta
 * 3. API Gateway - Integração via APIs (PagSeguro, Mercado Pago, etc.)
 */
class POSPaymentService
{
    /**
     * Processar pagamento no PDV
     * 
     * @param PhysicalSale $sale
     * @param array $paymentData
     * @return array
     */
    public function processPayment(PhysicalSale $sale, array $paymentData): array
    {
        $mode = setting('pos_payment_mode', 'manual'); // manual, tef, api
        
        switch ($mode) {
            case 'manual':
                return $this->processManualPayment($sale, $paymentData);
            
            case 'tef':
                return $this->processTEFPayment($sale, $paymentData);
            
            case 'api':
                return $this->processAPIPayment($sale, $paymentData);
            
            default:
                return $this->processManualPayment($sale, $paymentData);
        }
    }

    /**
     * Modo Manual - Operador processa na maquininha física
     * 
     * Neste modo, o sistema apenas registra o pagamento.
     * O operador processa o cartão na maquininha física e confirma no sistema.
     */
    protected function processManualPayment(PhysicalSale $sale, array $paymentData): array
    {
        try {
            // Validar dados básicos
            if (empty($paymentData['payment_method'])) {
                throw new \Exception('Método de pagamento não informado');
            }

            // Para pagamentos em cartão no modo manual, esperamos confirmação do operador
            if (in_array($paymentData['payment_method'], ['cartao_debito', 'cartao_credito'])) {
                // Registrar como "aguardando confirmação"
                $sale->update([
                    'payment_status' => 'pending_confirmation',
                    'payment_method' => $paymentData['payment_method'],
                    'installments' => $paymentData['installments'] ?? 1,
                ]);

                return [
                    'success' => true,
                    'mode' => 'manual',
                    'message' => 'Aguardando confirmação do pagamento na maquininha',
                    'requires_confirmation' => true,
                    'sale_id' => $sale->id,
                ];
            }

            // Para dinheiro, PIX, cheque - confirmar automaticamente
            $sale->update([
                'payment_status' => 'confirmed',
                'payment_method' => $paymentData['payment_method'],
            ]);

            return [
                'success' => true,
                'mode' => 'manual',
                'message' => 'Pagamento registrado com sucesso',
                'requires_confirmation' => false,
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento manual: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Confirmar pagamento manual (após processar na maquininha)
     */
    public function confirmManualPayment(PhysicalSale $sale, array $confirmationData): array
    {
        try {
            // Validar dados de confirmação
            if (empty($confirmationData['confirmed']) || !$confirmationData['confirmed']) {
                throw new \Exception('Confirmação não autorizada');
            }

            // Atualizar status do pagamento
            $sale->update([
                'payment_status' => 'confirmed',
                'payment_reference' => $confirmationData['reference'] ?? null, // NSU, autorização, etc.
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => auth('admin')->id(),
            ]);

            // Atualizar estoque se ainda não foi atualizado
            if ($sale->status !== 'completed') {
                $sale->update(['status' => 'completed']);
            }

            return [
                'success' => true,
                'message' => 'Pagamento confirmado com sucesso',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar pagamento manual: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Modo TEF - Integração direta com maquininha
     * 
     * Requer integração com provedores TEF como:
     * - GetNet
     * - Cielo
     * - Rede
     * - Elavon
     */
    protected function processTEFPayment(PhysicalSale $sale, array $paymentData): array
    {
        try {
            $provider = setting('tef_provider', 'cielo'); // cielo, getnet, rede, elavon
            
            // Aqui você implementaria a integração com o provedor TEF escolhido
            // Por enquanto, retornamos um exemplo de estrutura
            
            Log::info('Tentando processar pagamento TEF', [
                'sale_id' => $sale->id,
                'provider' => $provider,
                'amount' => $sale->total,
            ]);

            // Exemplo de estrutura para integração futura
            return [
                'success' => false,
                'error' => 'Integração TEF não configurada. Use modo manual ou configure um provedor TEF.',
                'mode' => 'tef',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento TEF: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Modo API - Integração via APIs de pagamento
     * 
     * Usa APIs de gateways como Mercado Pago, PagSeguro, Cielo, etc.
     */
    protected function processAPIPayment(PhysicalSale $sale, array $paymentData): array
    {
        try {
            $gatewayName = setting('pos_api_gateway', 'mercadopago');
            
            // Criar instância do gateway
            $gateway = GatewayFactory::create($gatewayName);
            
            // Processar pagamento
            $result = $gateway->processPayment($sale->total, [
                'sale_id' => $sale->id,
                'description' => "Venda PDV #{$sale->sale_number}",
                'installments' => $paymentData['installments'] ?? 1,
                'payment_method' => $paymentData['payment_method'],
                'card_number' => $paymentData['card_number'] ?? null,
                'card_holder' => $paymentData['card_holder'] ?? null,
                'card_expiration' => $paymentData['card_expiration'] ?? null,
                'card_cvv' => $paymentData['card_cvv'] ?? null,
                'card_brand' => $paymentData['card_brand'] ?? null,
                'card_token' => $paymentData['card_token'] ?? null,
                'email' => $paymentData['email'] ?? null,
            ]);

            if ($result['success']) {
                // Atualizar venda com dados da transação
                $sale->update([
                    'payment_status' => 'confirmed',
                    'payment_reference' => $result['transaction_id'] ?? $result['payment_id'] ?? null,
                    'payment_confirmed_at' => now(),
                    'status' => 'completed',
                ]);

                return [
                    'success' => true,
                    'mode' => 'api',
                    'gateway' => $gatewayName,
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'message' => 'Pagamento processado com sucesso via ' . ucfirst($gatewayName),
                ];
            }

            return [
                'success' => false,
                'error' => $result['error'] ?? 'Erro ao processar pagamento',
                'mode' => 'api',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento via API: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obter instruções para o operador baseado no modo de pagamento
     */
    public function getPaymentInstructions(string $paymentMethod, string $mode = 'manual'): string
    {
        if ($mode === 'manual') {
            switch ($paymentMethod) {
                case 'cartao_debito':
                    return 'Processe o cartão na maquininha como DÉBITO. Após aprovação, confirme o pagamento no sistema.';
                
                case 'cartao_credito':
                    return 'Processe o cartão na maquininha como CRÉDITO. Informe o número de parcelas. Após aprovação, confirme o pagamento no sistema.';
                
                case 'dinheiro':
                    return 'Receba o valor em dinheiro e confirme o pagamento.';
                
                case 'pix':
                    return 'Gere o QR Code PIX ou receba via chave. Após confirmação, confirme o pagamento no sistema.';
                
                case 'cheque':
                    return 'Receba o cheque e confirme o pagamento no sistema.';
                
                default:
                    return 'Processe o pagamento conforme o método escolhido e confirme no sistema.';
            }
        }

        return 'Siga as instruções na tela para processar o pagamento.';
    }
}

