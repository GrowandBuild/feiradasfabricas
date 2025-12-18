<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PhysicalSale;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Log;

class SyncService
{
    /**
     * Verifica se a sincronização está ativada
     */
    protected function isSyncEnabled(): bool
    {
        return setting('enable_physical_store_sync', false);
    }

    /**
     * Sincronizar estoque de um produto
     */
    public function syncInventory(Product $product, int $quantity, string $source): bool
    {
        if (!$this->isSyncEnabled() || !setting('sync_inventory', false)) {
            return false;
        }

        try {
            $log = SyncLog::create([
                'entity_type' => 'product',
                'entity_id' => $product->id,
                'sync_type' => 'inventory',
                'direction' => $source === 'ecommerce' ? 'ecommerce_to_physical' : 'physical_to_ecommerce',
                'status' => 'pending',
                'data' => [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'source' => $source,
                    'current_stock' => $product->stock_quantity,
                ],
            ]);

            // Aqui seria a lógica de sincronização real
            // Por enquanto, apenas logamos
            Log::info("Sincronizando estoque do produto {$product->id}", [
                'quantity' => $quantity,
                'source' => $source,
            ]);

            $log->markAsSuccess([
                'synced_at' => now(),
                'final_stock' => $product->stock_quantity,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar estoque: ' . $e->getMessage());
            
            if (isset($log)) {
                $log->markAsFailed($e->getMessage());
            }

            return false;
        }
    }

    /**
     * Sincronizar venda física
     */
    public function syncSale(PhysicalSale $sale): bool
    {
        if (!$this->isSyncEnabled() || !setting('sync_sales', false)) {
            return false;
        }

        try {
            $log = SyncLog::create([
                'entity_type' => 'physical_sale',
                'entity_id' => $sale->id,
                'sync_type' => 'sale',
                'direction' => 'physical_to_ecommerce',
                'status' => 'pending',
                'data' => [
                    'sale_number' => $sale->sale_number,
                    'total' => $sale->total,
                    'items_count' => $sale->items->count(),
                ],
            ]);

            // Aqui seria a lógica de sincronização real
            // Por enquanto, apenas marcamos como sincronizado
            $sale->synced_to_ecommerce = true;
            $sale->synced_at = now();
            $sale->save();

            $log->markAsSuccess([
                'synced_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar venda: ' . $e->getMessage());
            
            if (isset($log)) {
                $log->markAsFailed($e->getMessage());
            }

            return false;
        }
    }

    /**
     * Verificar status da sincronização
     */
    public function getSyncStatus(): array
    {
        if (!$this->isSyncEnabled()) {
            return [
                'enabled' => false,
                'message' => 'Sincronização desativada',
            ];
        }

        $pending = SyncLog::pending()->count();
        $failed = SyncLog::failed()->where('created_at', '>=', now()->subHour())->count();

        return [
            'enabled' => true,
            'inventory_sync' => setting('sync_inventory', false),
            'sales_sync' => setting('sync_sales', false),
            'coupons_sync' => setting('sync_coupons', false),
            'pending_syncs' => $pending,
            'recent_failures' => $failed,
        ];
    }
}





