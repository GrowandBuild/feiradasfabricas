<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class TestMercadoPagoConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercadopago:test {--detailed : Mostrar informações detalhadas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a conexão com a API do Mercado Pago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testando conexão com Mercado Pago...');
        $this->newLine();

        // Verificar configurações
        $this->info('📋 Verificando configurações...');
        
        $accessToken = setting('mercadopago_access_token');
        $publicKey = setting('mercadopago_public_key');
        $sandbox = setting('mercadopago_sandbox', true);
        $enabled = setting('mercadopago_enabled', false);

        $this->table(
            ['Configuração', 'Valor', 'Status'],
            [
                ['Mercado Pago Habilitado', $enabled ? 'Sim' : 'Não', $enabled ? '✅' : '❌'],
                ['Ambiente', $sandbox ? 'Sandbox (Teste)' : 'Produção', $sandbox ? '🧪' : '🚀'],
                ['Access Token', $accessToken ? 'Configurado' : 'Não configurado', $accessToken ? '✅' : '❌'],
                ['Public Key', $publicKey ? 'Configurado' : 'Não configurado', $publicKey ? '✅' : '❌'],
            ]
        );

        if (!$enabled) {
            $this->error('❌ Mercado Pago não está habilitado!');
            $this->info('💡 Habilite nas configurações do admin primeiro.');
            return 1;
        }

        if (!$accessToken) {
            $this->error('❌ Access Token não configurado!');
            $this->info('💡 Configure o Access Token nas configurações do admin.');
            return 1;
        }

        $this->newLine();

        // Testar conexão com API
        $this->info('🌐 Testando conexão com API...');
        
        try {
            // Testar com endpoint de usuários
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->get('https://api.mercadopago.com/users/me');

            if ($response->successful()) {
                $this->info('✅ Conexão com API estabelecida com sucesso!');
                
                if ($this->option('detailed')) {
                    $data = $response->json();
                    $this->info('📊 Informações da conta:');
                    $this->line('   • ID da Conta: ' . ($data['id'] ?? 'N/A'));
                    $this->line('   • Nickname: ' . ($data['nickname'] ?? 'N/A'));
                    $this->line('   • País: ' . ($data['country_id'] ?? 'N/A'));
                    $this->line('   • Site ID: ' . ($data['site_id'] ?? 'N/A'));
                    $this->line('   • Status: ' . (is_array($data['status'] ?? null) ? json_encode($data['status']) : ($data['status'] ?? 'N/A')));
                }
            } else {
                $this->error('❌ Erro na conexão com API!');
                $this->error('Status: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro ao conectar com API: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Testar criação de preferência (opcional)
        $this->info('🛒 Testando criação de preferência de pagamento...');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [
                    [
                        'title' => 'Teste de Conexão',
                        'quantity' => 1,
                        'unit_price' => 10.00,
                        'currency_id' => 'BRL'
                    ]
                ],
                'back_urls' => [
                    'success' => url('/'),
                    'failure' => url('/'),
                    'pending' => url('/')
                ],
                'external_reference' => 'test_' . time()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ Preferência criada com sucesso!');
                
                if ($this->option('detailed')) {
                    $this->info('📋 Detalhes da preferência:');
                    $this->line('   • ID: ' . $data['id']);
                    $this->line('   • Status: ' . ($data['status'] ?? 'N/A'));
                    $this->line('   • URL: ' . ($data['init_point'] ?? 'N/A'));
                    $this->line('   • Total: R$ ' . number_format($data['total_amount'] ?? 0, 2, ',', '.'));
                }
            } else {
                $this->warn('⚠️  Erro ao criar preferência:');
                $this->error('Status: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Erro ao testar criação de preferência: ' . $e->getMessage());
        }

        $this->newLine();

        // Testar métodos de pagamento disponíveis
        $this->info('💳 Testando métodos de pagamento disponíveis...');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->get('https://api.mercadopago.com/v1/payment_methods?country_id=BR');

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ Métodos de pagamento carregados!');
                
                if ($this->option('verbose') && is_array($data)) {
                    $this->info('💳 Métodos disponíveis:');
                    foreach (array_slice($data, 0, 5) as $method) {
                        $this->line('   • ' . ($method['name'] ?? 'N/A') . ' (' . ($method['id'] ?? 'N/A') . ')');
                    }
                    if (count($data) > 5) {
                        $this->line('   • ... e mais ' . (count($data) - 5) . ' métodos');
                    }
                }
            } else {
                $this->warn('⚠️  Erro ao carregar métodos de pagamento:');
                $this->error('Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Erro ao testar métodos de pagamento: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Teste de conexão concluído!');
        
        if ($sandbox) {
            $this->info('💡 Você está no ambiente de sandbox (teste).');
            $this->info('💡 Para usar em produção, altere o ambiente nas configurações.');
        }

        return 0;
    }
}