<?php

namespace App\Services;

use App\Models\PhysicalSale;
use App\Models\PhysicalSaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;
use App\Services\SyncService;

class PhysicalStoreService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Verifica se a loja física está ativada
     */
    public function isEnabled(): bool
    {
        return setting('enable_physical_store_sync', false);
    }

    /**
     * Criar uma venda física
     */
    public function createSale(array $data): PhysicalSale
    {
        if (!$this->isEnabled()) {
            throw new \Exception('Loja física não está ativada. Ative em Configurações > Loja Física.');
        }

        try {
            DB::beginTransaction();

            $sale = PhysicalSale::create([
                'sale_number' => PhysicalSale::generateSaleNumber(),
                'admin_id' => auth('admin')->id(),
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'installments' => $data['installments'] ?? 1,
                'notes' => $data['notes'] ?? null,
                'synced_to_ecommerce' => false,
            ]);

            // Criar itens da venda
            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Se tiver variação, usar dados da variação
                $variation = null;
                if (!empty($itemData['variation_id'])) {
                    $variation = \App\Models\ProductVariation::find($itemData['variation_id']);
                }
                
                $productName = $itemData['product_name'] ?? $product->name;
                $productSku = $itemData['product_sku'] ?? ($variation ? $variation->sku : $product->sku);
                $unitPrice = $itemData['unit_price'];
                $quantity = $itemData['quantity'];
                $discount = $itemData['discount'] ?? 0;
                
                // Verificar estoque disponível
                $availableStock = $variation 
                    ? $variation->stock_quantity 
                    : ($this->inventoryService->isSyncEnabled() 
                        ? $this->inventoryService->getAvailableStock($product)
                        : $product->stock_quantity);
                
                if ($availableStock < $quantity) {
                    throw new \Exception("Estoque insuficiente para {$productName}. Disponível: {$availableStock}, Solicitado: {$quantity}");
                }
                
                PhysicalSaleItem::create([
                    'physical_sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_variation_id' => $itemData['variation_id'] ?? null,
                    'product_name' => $productName,
                    'product_sku' => $productSku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'subtotal' => ($unitPrice * $quantity) - $discount,
                ]);

                // Atualizar estoque
                if ($variation) {
                    // Atualizar estoque da variação
                    $variation->stock_quantity = max(0, $variation->stock_quantity - $quantity);
                    if ($variation->stock_quantity <= 0) {
                        $variation->in_stock = false;
                    }
                    $variation->save();
                } else {
                    // Atualizar estoque do produto
                    if (setting('sync_inventory', false)) {
                        $this->inventoryService->updateStock(
                            $product,
                            -$quantity,
                            'physical_store'
                        );
                    } else {
                        // Sem sincronização, apenas atualizar localmente
                        $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
                        if ($product->stock_quantity <= 0) {
                            $product->in_stock = false;
                        }
                        $product->save();
                    }
                }
            }

            // Sincronizar venda se necessário
            if (setting('sync_sales', false)) {
                $syncService = app(SyncService::class);
                $syncService->syncSale($sale);
            }

            // Sincronizar com ERPs (Bling, Omie, ContaAzul)
            $integrationManager = app(\App\Services\Integrations\IntegrationManager::class);
            $integrationManager->syncSale($sale);

            // Emitir nota fiscal se configurado
            if (setting('sync_fiscal_enabled', false)) {
                $integrationManager->issueInvoice($sale);
            }

            DB::commit();
            return $sale->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao criar venda física: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Buscar produtos para PDV
     */
    public function searchProducts(string $query): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['variations' => function($q) {
                $q->where('in_stock', true);
            }])
            ->limit(20)
            ->get();
    }
}

