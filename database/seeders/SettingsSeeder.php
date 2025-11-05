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
