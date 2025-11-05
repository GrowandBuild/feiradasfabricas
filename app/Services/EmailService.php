<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmailService
{
    protected $fromName;
    protected $fromAddress;
    protected $replyTo;

    public function __construct()
    {
        $this->fromName = setting('email_from_name', 'Feira das Fábricas');
        $this->fromAddress = setting('email_from_address', 'noreply@feiradasfabricas.com');
        $this->replyTo = setting('email_reply_to', 'contato@feiradasfabricas.com');
    }

    /**
     * Enviar confirmação de pedido
     */
    public function enviarConfirmacaoPedido(Order $order)
    {
        if (!setting('email_template_order_confirmation', true)) {
            return false;
        }

        try {
            $customer = $order->customer;
            if (!$customer || !$customer->email) {
                throw new \Exception('Cliente não encontrado ou sem email');
            }

            $data = [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->orderItems,
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
            ];

            Mail::send('emails.order-confirmation', $data, function ($message) use ($customer, $order) {
                $message->to($customer->email, $customer->first_name . ' ' . $customer->last_name)
                        ->subject('Confirmação do Pedido #' . $order->order_number . ' - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);
            });

            Log::info('Email de confirmação de pedido enviado', [
                'order_id' => $order->id,
                'customer_email' => $customer->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar confirmação de pedido: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            
            return false;
        }
    }

    /**
     * Enviar confirmação de pagamento
     */
    public function enviarConfirmacaoPagamento(Order $order)
    {
        if (!setting('email_template_payment_confirmation', true)) {
            return false;
        }

        try {
            $customer = $order->customer;
            if (!$customer || !$customer->email) {
                throw new \Exception('Cliente não encontrado ou sem email');
            }

            $data = [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->orderItems,
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
            ];

            Mail::send('emails.payment-confirmation', $data, function ($message) use ($customer, $order) {
                $message->to($customer->email, $customer->first_name . ' ' . $customer->last_name)
                        ->subject('Pagamento Confirmado - Pedido #' . $order->order_number . ' - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);
            });

            Log::info('Email de confirmação de pagamento enviado', [
                'order_id' => $order->id,
                'customer_email' => $customer->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar confirmação de pagamento: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            
            return false;
        }
    }

    /**
     * Enviar notificação de envio
     */
    public function enviarNotificacaoEnvio(Order $order, $trackingCode = null)
    {
        if (!setting('email_template_shipping_notification', true)) {
            return false;
        }

        try {
            $customer = $order->customer;
            if (!$customer || !$customer->email) {
                throw new \Exception('Cliente não encontrado ou sem email');
            }

            $data = [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->orderItems,
                'tracking_code' => $trackingCode,
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
            ];

            Mail::send('emails.shipping-notification', $data, function ($message) use ($customer, $order) {
                $message->to($customer->email, $customer->first_name . ' ' . $customer->last_name)
                        ->subject('Seu pedido foi enviado - #' . $order->order_number . ' - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);
            });

            Log::info('Email de notificação de envio enviado', [
                'order_id' => $order->id,
                'customer_email' => $customer->email,
                'tracking_code' => $trackingCode
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação de envio: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            
            return false;
        }
    }

    /**
     * Enviar nota fiscal em anexo
     */
    public function enviarNotaFiscal(Order $order, $invoicePath)
    {
        if (!setting('email_template_invoice_attachment', true)) {
            return false;
        }

        try {
            $customer = $order->customer;
            if (!$customer || !$customer->email) {
                throw new \Exception('Cliente não encontrado ou sem email');
            }

            $data = [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->orderItems,
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
            ];

            Mail::send('emails.invoice-attachment', $data, function ($message) use ($customer, $order, $invoicePath) {
                $message->to($customer->email, $customer->first_name . ' ' . $customer->last_name)
                        ->subject('Nota Fiscal - Pedido #' . $order->order_number . ' - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);

                if (Storage::exists($invoicePath)) {
                    $message->attach(Storage::path($invoicePath), [
                        'as' => 'NotaFiscal_' . $order->order_number . '.pdf',
                        'mime' => 'application/pdf'
                    ]);
                }
            });

            Log::info('Email com nota fiscal enviado', [
                'order_id' => $order->id,
                'customer_email' => $customer->email,
                'invoice_path' => $invoicePath
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar nota fiscal: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            
            return false;
        }
    }

    /**
     * Enviar confirmação de entrega
     */
    public function enviarConfirmacaoEntrega(Order $order)
    {
        if (!setting('email_template_delivery_confirmation', true)) {
            return false;
        }

        try {
            $customer = $order->customer;
            if (!$customer || !$customer->email) {
                throw new \Exception('Cliente não encontrado ou sem email');
            }

            $data = [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->orderItems,
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
            ];

            Mail::send('emails.delivery-confirmation', $data, function ($message) use ($customer, $order) {
                $message->to($customer->email, $customer->first_name . ' ' . $customer->last_name)
                        ->subject('Pedido Entregue - #' . $order->order_number . ' - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);
            });

            Log::info('Email de confirmação de entrega enviado', [
                'order_id' => $order->id,
                'customer_email' => $customer->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar confirmação de entrega: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            
            return false;
        }
    }

    /**
     * Enviar email de teste
     */
    public function enviarEmailTeste($email)
    {
        try {
            $data = [
                'company_name' => setting('site_name', 'Feira das Fábricas'),
                'company_email' => setting('contact_email', 'contato@feiradasfabricas.com'),
                'company_phone' => setting('contact_phone', ''),
                'test_message' => 'Este é um email de teste para verificar se as configurações de email estão funcionando corretamente.',
            ];

            Mail::send('emails.test', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Email de Teste - ' . setting('site_name', 'Feira das Fábricas'))
                        ->from($this->fromAddress, $this->fromName)
                        ->replyTo($this->replyTo);
            });

            Log::info('Email de teste enviado', [
                'test_email' => $email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de teste: ' . $e->getMessage(), [
                'test_email' => $email
            ]);
            
            throw $e;
        }
    }

    /**
     * Testar configurações SMTP
     */
    public function testarSMTP()
    {
        try {
            // Configurar SMTP dinamicamente
            $config = [
                'driver' => 'smtp',
                'host' => setting('smtp_host', 'smtp.gmail.com'),
                'port' => setting('smtp_port', '587'),
                'encryption' => setting('smtp_encryption', 'tls'),
                'username' => setting('smtp_username', ''),
                'password' => setting('smtp_password', ''),
                'timeout' => setting('smtp_timeout', '30'),
            ];

            config(['mail.mailers.smtp' => $config]);

            // Teste simples de conectividade
            $transport = new \Swift_SmtpTransport(
                $config['host'],
                $config['port'],
                $config['encryption']
            );
            
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
            $transport->setTimeout($config['timeout']);

            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();

            return [
                'success' => true,
                'message' => 'Conexão SMTP estabelecida com sucesso'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão SMTP: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processar fila de emails automáticos
     */
    public function processarFilaEmails()
    {
        if (!setting('email_enabled', false)) {
            return;
        }

        // Buscar pedidos que precisam de emails
        $orders = Order::where('status', 'pending')
                      ->where('email_sent', false)
                      ->where('created_at', '>', now()->subDays(7))
                      ->get();

        foreach ($orders as $order) {
            $this->enviarConfirmacaoPedido($order);
            $order->update(['email_sent' => true]);
        }
    }
}
