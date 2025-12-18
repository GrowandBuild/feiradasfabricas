<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com Omie ERP
 * 
 * Sincroniza produtos, estoque e vendas com Omie.
 * Omie é muito usado por empresas de médio porte.
 */
class OmieIntegration extends BaseIntegration
{
    public function getName(): string
    {
        return 'Omie';
    }

    public function isEnabled(): bool
    {
        return setting('omie_enabled', false) 
            && !empty(setting('omie_app_key'))
            && !empty(setting('omie_app_secret'));
    }

    public function syncProduct($product): array
    {
        try {
            $appKey = setting('omie_app_key');
            $appSecret = setting('omie_app_secret');

            $productData = [
                'codigo_produto' => $product->sku,
                'descricao' => $product->name,
                'valor_unitario' => $product->price,
                'unidade' => 'UN',
                'estoque_minimo' => $product->min_stock ?? 0,
            ];

            $response = Http::post('https://app.omie.com.br/api/v1/geral/produtos/', [
                'call' => 'IncluirProduto',
                'app_key' => $appKey,
                'app_secret' => $appSecret,
                'param' => [$productData]
            ]);

            if ($response->successful() && isset($response->json()['codigo_produto'])) {
                return [
                    'success' => true,
                    'omie_id' => $response->json()['codigo_produto'],
                ];
            }

            return ['success' => false, 'error' => 'Erro ao sincronizar produto'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncProducts(array $products): array
    {
        $results = [];
        foreach ($products as $product) {
            $results[] = $this->syncProduct($product);
        }
        return ['success' => true, 'results' => $results];
    }

    public function syncStock($product, int $quantity): array
    {
        try {
            $appKey = setting('omie_app_key');
            $appSecret = setting('omie_app_secret');
            $omieId = $product->omie_id ?? $product->sku;

            $response = Http::post('https://app.omie.com.br/api/v1/estoque/consulta/', [
                'call' => 'AlterarEstoque',
                'app_key' => $appKey,
                'app_secret' => $appSecret,
                'param' => [[
                    'codigo_produto' => $omieId,
                    'quantidade' => $quantity,
                ]]
            ]);

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncSale($sale): array
    {
        try {
            $appKey = setting('omie_app_key');
            $appSecret = setting('omie_app_secret');

            $items = [];
            foreach ($sale->items as $item) {
                $items[] = [
                    'codigo_produto' => $item->product_sku,
                    'descricao' => $item->product_name,
                    'quantidade' => $item->quantity,
                    'valor_unitario' => $item->unit_price,
                ];
            }

            $orderData = [
                'cabecalho' => [
                    'codigo_pedido' => $sale->sale_number,
                    'data_previsao' => $sale->created_at->format('d/m/Y'),
                    'valor_total' => $sale->total,
                ],
                'itens' => $items,
            ];

            $response = Http::post('https://app.omie.com.br/api/v1/produtos/pedido/', [
                'call' => 'IncluirPedido',
                'app_key' => $appKey,
                'app_secret' => $appSecret,
                'param' => [$orderData]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'omie_order_id' => $result['codigo_pedido'] ?? null,
                ];
            }

            return ['success' => false, 'error' => 'Erro ao sincronizar venda'];
        } catch (\Exception $e) {
            Log::error('Omie Sync Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}





