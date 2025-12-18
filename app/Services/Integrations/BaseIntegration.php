<?php

namespace App\Services\Integrations;

abstract class BaseIntegration
{
    protected array $config;
    protected bool $enabled;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->enabled = $this->isEnabled();
    }

    abstract public function getName(): string;
    abstract public function isEnabled(): bool;
    
    // Sincronização de produtos
    abstract public function syncProduct($product): array;
    abstract public function syncProducts(array $products): array;
    
    // Sincronização de estoque
    abstract public function syncStock($product, int $quantity): array;
    
    // Sincronização de vendas
    abstract public function syncSale($sale): array;
    
    // Nota fiscal (se aplicável)
    public function issueInvoice($sale): array
    {
        return ['success' => false, 'error' => 'Emissão de nota fiscal não suportada'];
    }
}


