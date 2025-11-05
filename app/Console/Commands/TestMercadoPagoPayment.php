<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\PaymentService;

class TestMercadoPagoPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercadopago:payment-test {amount=10.00} {--method=pix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa um pagamento completo via Mercado Pago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = (float) $this->argument('amount');
        $method = $this->option('method');

        $this->info('🧪 Testando pagamento via Mercado Pago...');
        $this->info("💰 Valor: R$ " . number_format($amount, 2, ',', '.'));
        $this->info("💳 Método: " . strtoupper($method));
        $this->newLine();

        // Dados de teste
        $paymentData = [
            'payment_method_id' => $method,
            'email' => 'test@teste.com',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'cpf' => '12345678901'
        ];

        $metadata = [
            'order_id' => 'TEST_' . time(),
            'customer_id' => '123',
            'description' => 'Teste de pagamento'
        ];

        $paymentService = new PaymentService();

        $this->info('🔄 Processando pagamento...');
        
        try {
            $result = $paymentService->processMercadoPagoPayment(
                $amount,
                'BRL',
                $paymentData,
                $metadata
            );

            if ($result['success']) {
                $this->info('✅ Pagamento processado com sucesso!');
                $this->newLine();
                
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['Payment ID', $result['payment_id'] ?? 'N/A'],
                        ['Status', $result['status'] ?? 'N/A'],
                        ['Payment URL', $result['payment_url'] ?? 'N/A'],
                    ]
                );

                if (isset($result['payment_url']) && $result['payment_url']) {
                    $this->newLine();
                    $this->info('🔗 URL para pagamento:');
                    $this->line($result['payment_url']);
                }

                // Simular verificação de status
                $this->newLine();
                $this->info('🔍 Verificando status do pagamento...');
                
                $accessToken = setting('mercadopago_access_token');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])->get('https://api.mercadopago.com/v1/payments/' . $result['payment_id']);

                if ($response->successful()) {
                    $paymentInfo = $response->json();
                    $this->info('📊 Status atual do pagamento:');
                    $this->table(
                        ['Campo', 'Valor'],
                        [
                            ['ID', $paymentInfo['id'] ?? 'N/A'],
                            ['Status', $paymentInfo['status'] ?? 'N/A'],
                            ['Status Detail', $paymentInfo['status_detail'] ?? 'N/A'],
                            ['Transaction Amount', 'R$ ' . number_format($paymentInfo['transaction_amount'] ?? 0, 2, ',', '.')],
                            ['Date Created', $paymentInfo['date_created'] ?? 'N/A'],
                            ['Date Approved', $paymentInfo['date_approved'] ?? 'N/A'],
                        ]
                    );
                } else {
                    $this->warn('⚠️  Não foi possível verificar o status do pagamento');
                }

            } else {
                $this->error('❌ Erro no pagamento:');
                $this->error($result['error'] ?? 'Erro desconhecido');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro ao processar pagamento: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Teste de pagamento concluído!');
        $this->info('💡 Este é um ambiente de sandbox - nenhum valor real foi cobrado.');

        return 0;
    }
}