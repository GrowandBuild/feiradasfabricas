<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com ContaAzul ERP
 * 
 * Sincroniza produtos, estoque e vendas com ContaAzul.
 * ContaAzul é muito usado por pequenas empresas.
 */
class ContaAzulIntegration extends BaseIntegration
{
    public function getName(): string
    {
        return 'ContaAzul';
    }

    public function isEnabled(): bool
    {
        return setting('contaazul_enabled', false) 
            && !empty(setting('contaazul_access_token'));
    }

    public function syncProduct($product): array
    {
        try {
            $accessToken = setting('contaazul_access_token');
            
            $productData = [
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'cost_price' => $product->cost_price ?? 0,
                'stock' => $product->stock_quantity,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.contaazul.com/v1/products', $productData);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'contaazul_id' => $result['id'] ?? null,
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
            $accessToken = setting('contaazul_access_token');
            $contaazulId = $product->contaazul_id ?? null;

            if (!$contaazulId) {
                $syncResult = $this->syncProduct($product);
                $contaazulId = $syncResult['contaazul_id'] ?? null;
            }

            if (!$contaazulId) {
                return ['success' => false, 'error' => 'Produto não encontrado no ContaAzul'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->put("https://api.contaazul.com/v1/products/{$contaazulId}/stock", [
                'quantity' => $quantity
            ]);

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncSale($sale): array
    {
        try {
            $accessToken = setting('contaazul_access_token');
            
            $items = [];
            foreach ($sale->items as $item) {
                $items[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            }

            $orderData = [
                'number' => $sale->sale_number,
                'date' => $sale->created_at->format('Y-m-d'),
                'total' => $sale->total,
                'items' => $items,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.contaazul.com/v1/sales', $orderData);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'contaazul_order_id' => $result['id'] ?? null,
                ];
            }

            return ['success' => false, 'error' => 'Erro ao sincronizar venda'];
        } catch (\Exception $e) {
            Log::error('ContaAzul Sync Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}





