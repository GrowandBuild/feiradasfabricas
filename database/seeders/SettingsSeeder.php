<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $settings = [
            // Configurações do Site
            ['key' => 'site_name', 'value' => 'Feira das Fábricas', 'type' => 'string', 'group' => 'site'],
            ['key' => 'site_email', 'value' => '', 'type' => 'string', 'group' => 'site'],
            ['key' => 'site_phone', 'value' => '', 'type' => 'string', 'group' => 'site'],
            ['key' => 'site_address', 'value' => '', 'type' => 'string', 'group' => 'site'],

            // Configurações de Pagamento - Stripe
            ['key' => 'stripe_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'stripe_public_key', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'stripe_secret_key', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'stripe_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'payment'],

            // Configurações de Pagamento - PagSeguro
            ['key' => 'pagseguro_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'pagseguro_email', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'pagseguro_token', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'pagseguro_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'payment'],

            // Configurações de Pagamento - PayPal
            ['key' => 'paypal_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'paypal_client_id', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'paypal_client_secret', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'paypal_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'payment'],

            // Configurações de Pagamento - Mercado Pago
            ['key' => 'mercadopago_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'mercadopago_public_key', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'mercadopago_access_token', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'mercadopago_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'payment'],

            // Configurações de Entrega - Correios
            ['key' => 'correios_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'delivery'],
            ['key' => 'correios_codigo_empresa', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'correios_senha', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'correios_cep_origem', 'value' => '', 'type' => 'string', 'group' => 'delivery'],

            // Configurações de Entrega - Total Express
            ['key' => 'total_express_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'delivery'],
            ['key' => 'total_express_api_key', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'total_express_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'delivery'],

            // Configurações de Entrega - Jadlog
            ['key' => 'jadlog_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'delivery'],
            ['key' => 'jadlog_cnpj', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'jadlog_api_key', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'jadlog_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'delivery'],

            // Configurações de Entrega - Loggi
            ['key' => 'loggi_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'delivery'],
            ['key' => 'loggi_api_key', 'value' => '', 'type' => 'string', 'group' => 'delivery'],
            ['key' => 'loggi_sandbox', 'value' => true, 'type' => 'boolean', 'group' => 'delivery'],

            // Configurações de Estoque
            ['key' => 'stock_alert_threshold', 'value' => 10, 'type' => 'number', 'group' => 'stock'],
            ['key' => 'auto_stock_management', 'value' => false, 'type' => 'boolean', 'group' => 'stock'],
            ['key' => 'stock_reserve_time', 'value' => 24, 'type' => 'number', 'group' => 'stock'],

            // Configurações de Notificação
            ['key' => 'email_notifications', 'value' => true, 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'sms_notifications', 'value' => false, 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_email', 'value' => '', 'type' => 'string', 'group' => 'notification'],

            // Configurações de Segurança
            ['key' => 'two_factor_auth', 'value' => false, 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'session_timeout', 'value' => 120, 'type' => 'number', 'group' => 'security'],
            ['key' => 'max_login_attempts', 'value' => 5, 'type' => 'number', 'group' => 'security'],

            // Configurações de Loja Física / PDV (Sincronização)
            ['key' => 'enable_physical_store_sync', 'value' => false, 'type' => 'boolean', 'group' => 'physical_store', 'description' => 'Ativa/desativa sincronização entre e-commerce e loja física'],
            ['key' => 'physical_store_name', 'value' => '', 'type' => 'string', 'group' => 'physical_store', 'description' => 'Nome da loja física'],
            ['key' => 'sync_inventory', 'value' => false, 'type' => 'boolean', 'group' => 'physical_store', 'description' => 'Sincronizar estoque entre e-commerce e loja física'],
            ['key' => 'sync_sales', 'value' => false, 'type' => 'boolean', 'group' => 'physical_store', 'description' => 'Sincronizar vendas entre e-commerce e loja física'],
            ['key' => 'sync_coupons', 'value' => false, 'type' => 'boolean', 'group' => 'physical_store', 'description' => 'Sincronizar cupons entre e-commerce e loja física'],
            ['key' => 'inventory_reservation_time', 'value' => 15, 'type' => 'number', 'group' => 'physical_store', 'description' => 'Tempo de reserva de estoque em minutos'],
            ['key' => 'auto_sync_interval', 'value' => 5, 'type' => 'number', 'group' => 'physical_store', 'description' => 'Intervalo de sincronização automática em minutos'],
            ['key' => 'pos_payment_mode', 'value' => 'manual', 'type' => 'string', 'group' => 'physical_store', 'description' => 'Modo de pagamento no PDV: manual (operador processa na maquininha), tef (integração TEF), api (gateway API).'],
            ['key' => 'pos_api_gateway', 'value' => 'mercadopago', 'type' => 'string', 'group' => 'physical_store', 'description' => 'Gateway de pagamento para modo API: mercadopago, cielo, pagseguro, ton, sumup, getnet, yelly, magalu, turbopay, infinity'],
            
            // Configurações Mercado Pago
            ['key' => 'mercadopago_access_token', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Token de acesso Mercado Pago para PDV'],
            
            // Configurações Cielo
            ['key' => 'cielo_merchant_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Merchant ID Cielo'],
            ['key' => 'cielo_merchant_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Merchant Key Cielo'],
            ['key' => 'cielo_environment', 'value' => 'sandbox', 'type' => 'string', 'group' => 'payment', 'description' => 'Ambiente Cielo: sandbox ou production'],
            
            // Configurações GetNet
            ['key' => 'getnet_client_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Client ID GetNet'],
            ['key' => 'getnet_client_secret', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Client Secret GetNet'],
            ['key' => 'getnet_seller_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Seller ID GetNet'],
            ['key' => 'getnet_environment', 'value' => 'sandbox', 'type' => 'string', 'group' => 'payment', 'description' => 'Ambiente GetNet: sandbox ou production'],
            
            // Configurações SumUp
            ['key' => 'sumup_access_token', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Token de acesso SumUp'],
            
            // Integrações ERP e Fiscal
            ['key' => 'plugnotas_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Ativar integração com PlugNotas (emissão de notas fiscais)'],
            ['key' => 'plugnotas_api_key', 'value' => '', 'type' => 'string', 'group' => 'integrations', 'description' => 'API Key PlugNotas'],
            ['key' => 'plugnotas_environment', 'value' => 'sandbox', 'type' => 'string', 'group' => 'integrations', 'description' => 'Ambiente PlugNotas: sandbox ou production'],
            
            ['key' => 'bling_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Ativar integração com Bling ERP'],
            ['key' => 'bling_api_key', 'value' => '', 'type' => 'string', 'group' => 'integrations', 'description' => 'API Key Bling'],
            
            ['key' => 'omie_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Ativar integração com Omie ERP'],
            ['key' => 'omie_app_key', 'value' => '', 'type' => 'string', 'group' => 'integrations', 'description' => 'App Key Omie'],
            ['key' => 'omie_app_secret', 'value' => '', 'type' => 'string', 'group' => 'integrations', 'description' => 'App Secret Omie'],
            
            ['key' => 'contaazul_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Ativar integração com ContaAzul ERP'],
            ['key' => 'contaazul_access_token', 'value' => '', 'type' => 'string', 'group' => 'integrations', 'description' => 'Access Token ContaAzul'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Configurações básicas criadas/atualizadas com sucesso!');
        $this->command->line('Total de configurações: ' . count($settings));
    }
}
