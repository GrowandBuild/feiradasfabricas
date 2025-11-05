<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            // Configurações Gerais
            ['key' => 'site_name', 'value' => 'Feira das Fábricas', 'type' => 'string', 'group' => 'general', 'description' => 'Nome do site'],
            ['key' => 'site_description', 'value' => 'A melhor feira online do Brasil', 'type' => 'string', 'group' => 'general', 'description' => 'Descrição do site'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Logo do site'],
            ['key' => 'currency', 'value' => 'BRL', 'type' => 'string', 'group' => 'general', 'description' => 'Moeda padrão'],
            ['key' => 'timezone', 'value' => 'America/Sao_Paulo', 'type' => 'string', 'group' => 'general', 'description' => 'Fuso horário'],

            // Configurações de E-mail
            ['key' => 'mail_from_address', 'value' => 'noreply@feiradasfabricas.com', 'type' => 'string', 'group' => 'email', 'description' => 'E-mail remetente'],
            ['key' => 'mail_from_name', 'value' => 'Feira das Fábricas', 'type' => 'string', 'group' => 'email', 'description' => 'Nome do remetente'],
            ['key' => 'order_notification_email', 'value' => 'pedidos@feiradasfabricas.com', 'type' => 'string', 'group' => 'email', 'description' => 'E-mail para notificações de pedidos'],

            // Configurações de Pagamento
            ['key' => 'payment_methods', 'value' => '["credit_card","pix","boleto"]', 'type' => 'json', 'group' => 'payment', 'description' => 'Métodos de pagamento aceitos'],
            ['key' => 'pix_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Chave PIX'],
            ['key' => 'bank_account', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Dados da conta bancária'],

            // Configurações de Frete
            ['key' => 'free_shipping_minimum', 'value' => '200', 'type' => 'number', 'group' => 'shipping', 'description' => 'Valor mínimo para frete grátis'],
            ['key' => 'default_shipping_cost', 'value' => '15', 'type' => 'number', 'group' => 'shipping', 'description' => 'Custo padrão do frete'],
            ['key' => 'shipping_zones', 'value' => '[]', 'type' => 'json', 'group' => 'shipping', 'description' => 'Zonas de entrega'],

            // Configurações de Estoque
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'number', 'group' => 'inventory', 'description' => 'Limite para alerta de estoque baixo'],
            ['key' => 'auto_disable_out_of_stock', 'value' => 'true', 'type' => 'boolean', 'group' => 'inventory', 'description' => 'Desabilitar produtos sem estoque automaticamente'],

            // Configurações de Cliente B2B
            ['key' => 'b2b_auto_approval', 'value' => 'false', 'type' => 'boolean', 'group' => 'b2b', 'description' => 'Aprovação automática de clientes B2B'],
            ['key' => 'b2b_minimum_order', 'value' => '500', 'type' => 'number', 'group' => 'b2b', 'description' => 'Valor mínimo do pedido B2B'],
            ['key' => 'b2b_discount_percentage', 'value' => '10', 'type' => 'number', 'group' => 'b2b', 'description' => 'Desconto padrão para B2B (%)'],

            // Configurações de SEO
            ['key' => 'meta_title', 'value' => 'Feira das Fábricas - A melhor feira online', 'type' => 'string', 'group' => 'seo', 'description' => 'Meta título'],
            ['key' => 'meta_description', 'value' => 'Encontre os melhores produtos com os melhores preços', 'type' => 'string', 'group' => 'seo', 'description' => 'Meta descrição'],
            ['key' => 'meta_keywords', 'value' => 'feira, online, produtos, eletrônicos, celulares', 'type' => 'string', 'group' => 'seo', 'description' => 'Meta palavras-chave'],

            // Configurações de Notificações
            ['key' => 'notification_email_new_order', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notificar por e-mail novos pedidos'],
            ['key' => 'notification_email_low_stock', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notificar por e-mail estoque baixo'],
            ['key' => 'notification_email_new_customer', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notificar por e-mail novos clientes'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
