<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder para atualizar produtos existentes com o fornecedor SHOPPINGCELL CELULARES
 * 
 * Execute com: php artisan db:seed --class=UpdateSupplierSeeder
 */
class UpdateSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar se a coluna supplier existe
        if (!Schema::hasColumn('products', 'supplier')) {
            $this->command->error('❌ A coluna "supplier" não existe na tabela products!');
            $this->command->warn('⚠️  Execute primeiro: php artisan migrate');
            return;
        }

        $this->command->info('🔄 Atualizando fornecedor apenas dos produtos da lista específica...');

        // Lista de SKUs/modelos que devem ter o fornecedor SHOPPINGCELL CELULARES
        // Apenas produtos da lista específica fornecida pelo usuário
        $shoppingCellSkus = [
            // iPhones da lista
            'BASE-12', 'BASE-13', 'BASE-13ProMax', 'BASE-14', 'BASE-14ProMax',
            'BASE-15', 'BASE-15Plus', 'BASE-15ProMax', 'BASE-16E', 'BASE-16',
            'BASE-16Plus', 'BASE-16Pro', 'BASE-16ProMax', 'BASE-17ProMax',
            // AirPods
            'APPLE-AP4-NOANC', 'APPLE-AP4-ANC', 'APPLE-APP2-USB-C', 'APPLE-APMAX',
            // AirTags
            'APPLE-AT-001', 'APPLE-AT-PACK4',
            // Apple Watch
            'APPLE-AW-SE2-40', 'APPLE-AW-S10-42', 'APPLE-AW-S10-46',
            // iPads
            'APPLE-IP10-64', 'APPLE-IP10-256', 'APPLE-IP11-128',
            'APPLE-IPMINI17P-128', 'APPLE-IPAM2-11-128', 'APPLE-IPAM3-11-128',
            'APPLE-IPPM4-11-256',
            // MacBooks
            'APPLE-MBA15-M3-8-256', 'APPLE-MBA13-M4-16-512', 'APPLE-MBA15-M4-16-256',
        ];

        // Atualizar produtos com SKU exato
        $updated = Product::whereIn('sku', $shoppingCellSkus)
            ->where(function($q) {
                $q->whereNull('supplier')
                  ->orWhere('supplier', '');
            })
            ->update(['supplier' => 'SHOPPINGCELL CELULARES']);

        // Atualizar produtos iPhone que começam com BASE-12, BASE-13, etc.
        $updated += Product::where('brand', 'Apple')
            ->where(function($q) {
                $q->where('sku', 'like', 'BASE-12%')
                  ->orWhere('sku', 'like', 'BASE-13%')
                  ->orWhere('sku', 'like', 'BASE-14%')
                  ->orWhere('sku', 'like', 'BASE-15%')
                  ->orWhere('sku', 'like', 'BASE-16%')
                  ->orWhere('sku', 'like', 'BASE-17ProMax%');
            })
            ->where(function($q) {
                $q->whereNull('supplier')
                  ->orWhere('supplier', '');
            })
            ->update(['supplier' => 'SHOPPINGCELL CELULARES']);

        $this->command->info("✅ {$updated} produtos da lista atualizados com fornecedor 'SHOPPINGCELL CELULARES'");

        // Contar total de produtos com esse fornecedor
        $total = Product::where('supplier', 'SHOPPINGCELL CELULARES')
            ->count();

        $this->command->info("📊 Total de produtos com fornecedor 'SHOPPINGCELL CELULARES': {$total}");
    }
}

