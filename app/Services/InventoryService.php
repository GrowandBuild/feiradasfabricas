<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryReservation;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Verifica se a sincronização está ativada
     */
    public function isSyncEnabled(): bool
    {
        return setting('enable_physical_store_sync', false) 
            && setting('sync_inventory', false);
    }

    /**
     * Obter estoque disponível de um produto
     * Considera reservas se sincronização estiver ativa
     */
    public function getAvailableStock(Product $product): int
    {
        $stock = $product->stock_quantity;

        // Se sincronização não está ativa, retorna estoque normal
        if (!$this->isSyncEnabled()) {
            return $stock;
        }

        // Calcular reservas ativas
        $reserved = InventoryReservation::where('product_id', $product->id)
            ->active()
            ->sum('quantity');

        return max(0, $stock - $reserved);
    }

    /**
     * Atualizar estoque de um produto
     */
    public function updateStock(Product $product, int $quantity, string $source = 'ecommerce'): bool
    {
        try {
            DB::beginTransaction();

            // Comportamento padrão (sem sync)
            if (!$this->isSyncEnabled()) {
                $product->stock_quantity += $quantity;
                $product->save();

                // Log de estoque
                InventoryLog::create([
                    'product_id' => $product->id,
                    'type' => $quantity > 0 ? 'in' : 'out',
                    'quantity' => abs($quantity),
                    'source' => $source,
                ]);

                DB::commit();
                return true;
            }

            // Comportamento com sincronização
            // A lógica de sincronização será implementada no SyncService
            $product->stock_quantity += $quantity;
            $product->save();

            // Log de estoque
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => $quantity > 0 ? 'in' : 'out',
                'quantity' => abs($quantity),
                'source' => $source,
            ]);

            // Sincronizar se necessário
            if (setting('sync_inventory', false)) {
                app(SyncService::class)->syncInventory($product, $quantity, $source);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar estoque: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reservar estoque temporariamente
     */
    public function reserveStock(Product $product, int $quantity, string $source, $referenceType = null, $referenceId = null): ?InventoryReservation
    {
        // Se sincronização não está ativa, não reserva
        if (!$this->isSyncEnabled()) {
            return null;
        }

        $availableStock = $this->getAvailableStock($product);

        if ($availableStock < $quantity) {
            throw new \Exception("Estoque insuficiente. Disponível: {$availableStock}, Solicitado: {$quantity}");
        }

        $reservationTime = setting('inventory_reservation_time', 15);
        $expiresAt = now()->addMinutes($reservationTime);

        return InventoryReservation::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'source' => $source,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);
    }

    /**
     * Liberar reserva de estoque
     */
    public function releaseReservation(InventoryReservation $reservation): bool
    {
        $reservation->expire();
        return true;
    }

    /**
     * Limpar reservas expiradas
     */
    public function cleanExpiredReservations(): int
    {
        $expired = InventoryReservation::expired()
            ->where('is_active', true)
            ->get();

        $count = 0;
        foreach ($expired as $reservation) {
            $reservation->expire();
            $count++;
        }

        return $count;
    }
}

