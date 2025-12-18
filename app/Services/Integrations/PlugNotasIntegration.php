<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com PlugNotas - Emissão de Notas Fiscais
 * 
 * PlugNotas é o motor fiscal mais usado no Brasil.
 * Emite NFe, NFCe, NFSe automaticamente.
 */
class PlugNotasIntegration extends BaseIntegration
{
    public function getName(): string
    {
        return 'PlugNotas';
    }

    public function isEnabled(): bool
    {
        return setting('plugnotas_enabled', false) 
            && !empty(setting('plugnotas_api_key'));
    }

    public function syncProduct($product): array
    {
        // PlugNotas não sincroniza produtos, apenas emite notas
        return ['success' => true, 'message' => 'PlugNotas não requer sincronização de produtos'];
    }

    public function syncProducts(array $products): array
    {
        return ['success' => true];
    }

    public function syncStock($product, int $quantity): array
    {
        return ['success' => true];
    }

    public function syncSale($sale): array
    {
        // PlugNotas não sincroniza vendas, apenas emite notas
        return ['success' => true];
    }

    /**
     * Emitir Nota Fiscal (NFe ou NFCe)
     */
    public function issueInvoice($sale): array
    {
        try {
            $apiKey = setting('plugnotas_api_key');
            $environment = setting('plugnotas_environment', 'sandbox');
            
            $baseUrl = $environment === 'production'
                ? 'https://api.plugnotas.com.br'
                : 'https://api.sandbox.plugnotas.com.br';

            // Preparar dados da nota fiscal
            $invoiceData = $this->prepareInvoiceData($sale);

            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/v2/nfce", $invoiceData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Atualizar venda com dados da nota
                $sale->update([
                    'nfe_issued' => true,
                    'nfe_key' => $result['chave'] ?? null,
                    'nfe_number' => $result['numero'] ?? null,
                    'nfe_xml' => $result['xml'] ?? null,
                ]);

                return [
                    'success' => true,
                    'nfe_key' => $result['chave'] ?? null,
                    'nfe_number' => $result['numero'] ?? null,
                    'pdf_url' => $result['pdf'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Erro ao emitir nota fiscal',
            ];

        } catch (\Exception $e) {
            Log::error('PlugNotas Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Preparar dados da nota fiscal
     */
    protected function prepareInvoiceData($sale): array
    {
        $items = [];
        foreach ($sale->items as $index => $item) {
            $items[] = [
                'numero' => $index + 1,
                'codigo' => $item->product_sku,
                'descricao' => $item->product_name,
                'cfop' => '5102', // Venda dentro do estado
                'un' => 'UN',
                'quantidade' => $item->quantity,
                'valor_unitario' => $item->unit_price,
                'valor_total' => $item->subtotal,
            ];
        }

        return [
            'natureza_operacao' => 'Venda',
            'data_emissao' => now()->format('Y-m-d\TH:i:s'),
            'tipo' => 'NFCe', // ou 'NFe'
            'finalidade' => '1', // Normal
            'consumidor_final' => $sale->customer_id ? '0' : '1',
            'items' => $items,
            'total' => [
                'valor_produtos' => $sale->subtotal,
                'valor_desconto' => $sale->discount,
                'valor_total' => $sale->total,
            ],
        ];
    }
}





