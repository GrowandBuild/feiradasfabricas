<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMercadoPagoPreference extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercadopago:preference-test {amount=10.00}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a criação de uma preferência de pagamento via Mercado Pago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = (float) $this->argument('amount');

        $this->info('🧪 Testando criação de preferência via Mercado Pago...');
        $this->info("💰 Valor: R$ " . number_format($amount, 2, ',', '.'));
        $this->newLine();

        $accessToken = setting('mercadopago_access_token');
        
        if (empty($accessToken)) {
            $this->error('❌ Access Token do Mercado Pago não configurado!');
            return 1;
        }

        $this->info('🔄 Criando preferência de pagamento...');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [
                    [
                        'title' => 'Teste de Pagamento - Feira das Fábricas',
                        'description' => 'Produto de teste para verificar integração',
                        'quantity' => 1,
                        'unit_price' => $amount,
                        'currency_id' => 'BRL'
                    ]
                ],
                'back_urls' => [
                    'success' => url('/'),
                    'failure' => url('/'),
                    'pending' => url('/')
                ],
                'external_reference' => 'TEST_' . time(),
                'metadata' => [
                    'test' => true,
                    'order_id' => 'TEST_' . time(),
                    'customer_name' => 'Cliente de Teste'
                ],
                'payment_methods' => [
                    'excluded_payment_methods' => [],
                    'excluded_payment_types' => [],
                    'installments' => 12
                ],
                'notification_url' => url('/payment/mercadopago/notification'),
                'statement_descriptor' => 'FEIRA DAS FABRICAS'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ Preferência criada com sucesso!');
                $this->newLine();
                
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['ID da Preferência', $data['id']],
                        ['Status', $data['status'] ?? 'N/A'],
                        ['Total', 'R$ ' . number_format($data['total_amount'] ?? 0, 2, ',', '.')],
                        ['URL de Pagamento', $data['init_point'] ?? 'N/A'],
                        ['Referência Externa', $data['external_reference'] ?? 'N/A'],
                        ['Data de Criação', $data['date_created'] ?? 'N/A'],
                    ]
                );

                if (isset($data['init_point'])) {
                    $this->newLine();
                    $this->info('🔗 URL para pagamento:');
                    $this->line($data['init_point']);
                    $this->newLine();
                    $this->info('💡 Copie esta URL e cole no navegador para testar o pagamento.');
                    $this->info('💡 Use dados de teste do Mercado Pago para completar o pagamento.');
                }

                // Mostrar informações dos itens
                if (isset($data['items']) && is_array($data['items'])) {
                    $this->newLine();
                    $this->info('📦 Itens da preferência:');
                    foreach ($data['items'] as $item) {
                        $this->line("   • {$item['title']} - R$ " . number_format($item['unit_price'], 2, ',', '.'));
                    }
                }

                // Mostrar métodos de pagamento
                if (isset($data['payment_methods'])) {
                    $this->newLine();
                    $this->info('💳 Configurações de pagamento:');
                    $this->line('   • Parcelas máximas: ' . ($data['payment_methods']['installments'] ?? 'N/A'));
                }

            } else {
                $this->error('❌ Erro ao criar preferência:');
                $this->error('Status: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar preferência: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Teste de preferência concluído!');
        $this->info('💡 Esta preferência pode ser usada para testar pagamentos reais.');
        $this->info('💡 Ambiente: ' . (setting('mercadopago_sandbox', true) ? 'Sandbox (Teste)' : 'Produção'));

        return 0;
    }
}
