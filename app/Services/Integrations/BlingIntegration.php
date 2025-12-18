<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com Bling ERP
 * 
 * Sincroniza produtos, estoque e vendas com Bling.
 * Bling é o ERP mais usado por pequenas e médias empresas.
 */
class BlingIntegration extends BaseIntegration
{
    public function getName(): string
    {
        return 'Bling';
    }

    public function isEnabled(): bool
    {
        return setting('bling_enabled', false) 
            && !empty(setting('bling_api_key'));
    }

    public function syncProduct($product): array
    {
        try {
            $apiKey = setting('bling_api_key');
            
            $productData = [
                'nome' => $product->name,
                'codigo' => $product->sku,
                'preco' => $product->price,
                'precoCusto' => $product->cost_price ?? 0,
                'tipo' => 'P', // Produto
                'estoque' => $product->stock_quantity,
                'descricaoCurta' => $product->short_description ?? '',
            ];

            $response = Http::withHeaders([
                'apikey' => $apiKey,
            ])->post('https://www.bling.com.br/Api/v3/produtos', [
                'produto' => $productData
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'bling_id' => $result['data']['id'] ?? null,
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
            $apiKey = setting('bling_api_key');
            $blingId = $product->bling_id ?? null;

            if (!$blingId) {
                // Se não tem ID do Bling, sincronizar produto primeiro
                $syncResult = $this->syncProduct($product);
                $blingId = $syncResult['bling_id'] ?? null;
            }

            if (!$blingId) {
                return ['success' => false, 'error' => 'Produto não encontrado no Bling'];
            }

            $response = Http::withHeaders([
                'apikey' => $apiKey,
            ])->put("https://www.bling.com.br/Api/v3/produtos/{$blingId}/estoques", [
                'estoque' => [
                    'quantidade' => $quantity,
                ]
            ]);

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncSale($sale): array
    {
        try {
            $apiKey = setting('bling_api_key');
            
            $items = [];
            foreach ($sale->items as $item) {
                $items[] = [
                    'codigo' => $item->product_sku,
                    'descricao' => $item->product_name,
                    'quantidade' => $item->quantity,
                    'valor' => $item->unit_price,
                ];
            }

            $orderData = [
                'numero' => $sale->sale_number,
                'data' => $sale->created_at->format('d/m/Y'),
                'dataSaida' => $sale->created_at->format('d/m/Y'),
                'total' => $sale->total,
                'situacao' => 'Aprovado',
                'itens' => $items,
            ];

            $response = Http::withHeaders([
                'apikey' => $apiKey,
            ])->post('https://www.bling.com.br/Api/v3/pedidos/vendas', [
                'pedido' => $orderData
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'bling_order_id' => $result['data']['id'] ?? null,
                ];
            }

            return ['success' => false, 'error' => 'Erro ao sincronizar venda'];
        } catch (\Exception $e) {
            Log::error('Bling Sync Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}


