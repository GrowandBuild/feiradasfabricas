<?php

namespace App\Services\Integrations;

/**
 * Gerenciador de Integrações
 * 
 * Centraliza todas as integrações e permite sincronização automática
 */
class IntegrationManager
{
    protected array $integrations = [];

    public function __construct()
    {
        $this->loadIntegrations();
    }

    /**
     * Carregar integrações disponíveis
     */
    protected function loadIntegrations(): void
    {
        $this->integrations = [
            'plugnotas' => new PlugNotasIntegration(),
            'bling' => new BlingIntegration(),
            'omie' => new OmieIntegration(),
            'contaazul' => new ContaAzulIntegration(),
        ];
    }

    /**
     * Obter integração específica
     */
    public function getIntegration(string $name): ?BaseIntegration
    {
        return $this->integrations[strtolower($name)] ?? null;
    }

    /**
     * Obter todas as integrações ativas
     */
    public function getActiveIntegrations(): array
    {
        return array_filter($this->integrations, function($integration) {
            return $integration->isEnabled();
        });
    }

    /**
     * Sincronizar produto com todas as integrações ativas
     */
    public function syncProduct($product): array
    {
        $results = [];
        foreach ($this->getActiveIntegrations() as $name => $integration) {
            if (method_exists($integration, 'syncProduct')) {
                $results[$name] = $integration->syncProduct($product);
            }
        }
        return $results;
    }

    /**
     * Sincronizar estoque com todas as integrações ativas
     */
    public function syncStock($product, int $quantity): array
    {
        $results = [];
        foreach ($this->getActiveIntegrations() as $name => $integration) {
            if (method_exists($integration, 'syncStock')) {
                $results[$name] = $integration->syncStock($product, $quantity);
            }
        }
        return $results;
    }

    /**
     * Sincronizar venda com todas as integrações ativas
     */
    public function syncSale($sale): array
    {
        $results = [];
        foreach ($this->getActiveIntegrations() as $name => $integration) {
            if (method_exists($integration, 'syncSale')) {
                $results[$name] = $integration->syncSale($sale);
            }
        }
        return $results;
    }

    /**
     * Emitir nota fiscal (PlugNotas)
     */
    public function issueInvoice($sale): array
    {
        $plugnotas = $this->getIntegration('plugnotas');
        
        if ($plugnotas && $plugnotas->isEnabled()) {
            return $plugnotas->issueInvoice($sale);
        }

        return ['success' => false, 'error' => 'PlugNotas não está ativado'];
    }
}


