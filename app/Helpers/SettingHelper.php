<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * Obter valor de configuração
     */
    public static function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Definir valor de configuração
     */
    public static function set($key, $value)
    {
        return Setting::set($key, $value);
    }

    /**
     * Verificar se uma configuração está ativada
     */
    public static function isEnabled($key, $default = false)
    {
        return (bool) self::get($key, $default);
    }

    /**
     * Obter configurações de pagamento
     */
    public static function getPaymentConfig($provider)
    {
        $configs = [
            'stripe' => [
                'enabled' => self::isEnabled('stripe_enabled'),
                'public_key' => self::get('stripe_public_key'),
                'secret_key' => self::get('stripe_secret_key'),
                'sandbox' => self::isEnabled('stripe_sandbox', true)
            ],
            'pagseguro' => [
                'enabled' => self::isEnabled('pagseguro_enabled'),
                'email' => self::get('pagseguro_email'),
                'token' => self::get('pagseguro_token'),
                'sandbox' => self::isEnabled('pagseguro_sandbox', true)
            ],
            'paypal' => [
                'enabled' => self::isEnabled('paypal_enabled'),
                'client_id' => self::get('paypal_client_id'),
                'client_secret' => self::get('paypal_client_secret'),
                'sandbox' => self::isEnabled('paypal_sandbox', true)
            ],
            'mercadopago' => [
                'enabled' => self::isEnabled('mercadopago_enabled'),
                'public_key' => self::get('mercadopago_public_key'),
                'access_token' => self::get('mercadopago_access_token'),
                'sandbox' => self::isEnabled('mercadopago_sandbox', true)
            ]
        ];

        return $configs[$provider] ?? null;
    }

    /**
     * Obter configurações de entrega
     */
    public static function getDeliveryConfig($provider)
    {
        $configs = [
            'correios' => [
                'enabled' => self::isEnabled('correios_enabled'),
                'codigo_empresa' => self::get('correios_codigo_empresa'),
                'senha' => self::get('correios_senha'),
                'cep_origem' => self::get('correios_cep_origem')
            ],
            'total_express' => [
                'enabled' => self::isEnabled('total_express_enabled'),
                'api_key' => self::get('total_express_api_key'),
                'sandbox' => self::isEnabled('total_express_sandbox', true)
            ],
            'jadlog' => [
                'enabled' => self::isEnabled('jadlog_enabled'),
                'cnpj' => self::get('jadlog_cnpj'),
                'api_key' => self::get('jadlog_api_key'),
                'sandbox' => self::isEnabled('jadlog_sandbox', true)
            ],
            'loggi' => [
                'enabled' => self::isEnabled('loggi_enabled'),
                'api_key' => self::get('loggi_api_key'),
                'sandbox' => self::isEnabled('loggi_sandbox', true)
            ],
            'melhor_envio' => [
                'enabled' => self::isEnabled('melhor_envio_enabled'),
                'client_id' => self::get('melhor_envio_client_id'),
                'client_secret' => self::get('melhor_envio_client_secret'),
                'token' => self::get('melhor_envio_token'),
                'sandbox' => self::isEnabled('melhor_envio_sandbox', true),
                'service_ids' => self::get('melhor_envio_service_ids'),
                'cep_origem' => self::get('melhor_envio_cep_origem') ?? self::get('correios_cep_origem')
            ]
        ];

        return $configs[$provider] ?? null;
    }

    /**
     * Obter providers de pagamento ativos
     */
    public static function getActivePaymentProviders()
    {
        $providers = [];
        
        if (self::isEnabled('stripe_enabled')) {
            $providers[] = 'stripe';
        }
        
        if (self::isEnabled('pagseguro_enabled')) {
            $providers[] = 'pagseguro';
        }
        
        if (self::isEnabled('paypal_enabled')) {
            $providers[] = 'paypal';
        }
        
        if (self::isEnabled('mercadopago_enabled')) {
            $providers[] = 'mercadopago';
        }

        return $providers;
    }

    /**
     * Obter providers de entrega ativos
     */
    public static function getActiveDeliveryProviders()
    {
        $providers = [];
        
        if (self::isEnabled('correios_enabled')) {
            $providers[] = 'correios';
        }
        
        if (self::isEnabled('total_express_enabled')) {
            $providers[] = 'total_express';
        }
        
        if (self::isEnabled('jadlog_enabled')) {
            $providers[] = 'jadlog';
        }
        
        if (self::isEnabled('loggi_enabled')) {
            $providers[] = 'loggi';
        }
        
        if (self::isEnabled('melhor_envio_enabled')) {
            $providers[] = 'melhor_envio';
        }

        return $providers;
    }

    /**
     * Obter configurações do site
     */
    public static function getSiteConfig()
    {
        return [
            'name' => self::get('site_name', 'Feira das Fábricas'),
            'email' => self::get('site_email'),
            'phone' => self::get('site_phone'),
            'address' => self::get('site_address')
        ];
    }

    /**
     * Obter configurações de estoque
     */
    public static function getStockConfig()
    {
        return [
            'alert_threshold' => (int) self::get('stock_alert_threshold', 10),
            'auto_management' => self::isEnabled('auto_stock_management'),
            'reserve_time' => (int) self::get('stock_reserve_time', 24)
        ];
    }

    /**
     * Obter configurações de notificação
     */
    public static function getNotificationConfig()
    {
        return [
            'email_notifications' => self::isEnabled('email_notifications', true),
            'sms_notifications' => self::isEnabled('sms_notifications'),
            'notification_email' => self::get('notification_email')
        ];
    }

    /**
     * Obter configurações de segurança
     */
    public static function getSecurityConfig()
    {
        return [
            'two_factor_auth' => self::isEnabled('two_factor_auth'),
            'session_timeout' => (int) self::get('session_timeout', 120),
            'max_login_attempts' => (int) self::get('max_login_attempts', 5)
        ];
    }

    /**
     * Verificar se um provider está configurado corretamente
     */
    public static function isProviderConfigured($type, $provider)
    {
        if ($type === 'payment') {
            $config = self::getPaymentConfig($provider);
        } elseif ($type === 'delivery') {
            $config = self::getDeliveryConfig($provider);
        } else {
            return false;
        }

        if (!$config || !$config['enabled']) {
            return false;
        }

        // Verificar se todas as credenciais necessárias estão preenchidas
        $requiredFields = self::getRequiredFields($type, $provider);
        
        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obter campos obrigatórios para cada provider
     */
    private static function getRequiredFields($type, $provider)
    {
        $requiredFields = [
            'payment' => [
                'stripe' => ['secret_key'],
                'pagseguro' => ['email', 'token'],
                'paypal' => ['client_id', 'client_secret'],
                'mercadopago' => ['access_token']
            ],
            'delivery' => [
                'correios' => ['codigo_empresa', 'senha', 'cep_origem'],
                'total_express' => ['api_key'],
                'jadlog' => ['cnpj', 'api_key'],
                'loggi' => ['api_key'],
                'melhor_envio' => ['client_id', 'client_secret']
            ]
        ];

        return $requiredFields[$type][$provider] ?? [];
    }

    /**
     * Obter status de configuração de todos os providers
     */
    public static function getProvidersStatus()
    {
        $status = [
            'payment' => [],
            'delivery' => []
        ];

        // Status dos providers de pagamento
        $paymentProviders = ['stripe', 'pagseguro', 'paypal', 'mercadopago'];
        foreach ($paymentProviders as $provider) {
            $status['payment'][$provider] = [
                'enabled' => self::isEnabled($provider . '_enabled'),
                'configured' => self::isProviderConfigured('payment', $provider)
            ];
        }

        // Status dos providers de entrega
        $deliveryProviders = ['correios', 'total_express', 'jadlog', 'loggi'];
        // Include Melhor Envio status
        $deliveryProviders[] = 'melhor_envio';
        foreach ($deliveryProviders as $provider) {
            $status['delivery'][$provider] = [
                'enabled' => self::isEnabled($provider . '_enabled'),
                'configured' => self::isProviderConfigured('delivery', $provider)
            ];
        }

        return $status;
    }
}
