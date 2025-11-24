<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class MigrateBrandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brands:migrate {--dry-run : Simular a migração sem alterar dados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar marcas da coluna string "brand" para a tabela "brands" e vincular produtos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 Modo dry-run ativado - nenhuma alteração será feita.');
        }

        // Buscar marcas únicas existentes nos produtos
        $existingBrands = Product::whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand')
            ->toArray();

        if (empty($existingBrands)) {
            $this->info('✅ Nenhum produto com marca encontrada. Migração não necessária.');
            return Command::SUCCESS;
        }

        $this->info('📋 Marcas encontradas nos produtos: ' . count($existingBrands));
        $this->table(['Marca'], array_map(fn($b) => [$b], $existingBrands));

        $created = 0;
        $updated = 0;
        $linked = 0;

        foreach ($existingBrands as $brandName) {
            // Verificar se a marca já existe
            $brand = Brand::where('name', $brandName)->first();

            if (!$brand) {
                if (!$dryRun) {
                    $brand = Brand::create([
                        'name' => $brandName,
                        'slug' => \Str::slug($brandName),
                        'is_active' => true,
                        'sort_order' => 0,
                    ]);
                }
                $created++;
                $this->line("➕ Criada marca: {$brandName}");
            } else {
                $updated++;
                $this->line("🔄 Marca já existe: {$brandName}");
            }

            // Vincular produtos a esta marca
            if (!$dryRun && $brand) {
                $productsUpdated = Product::where('brand', $brandName)
                    ->whereNull('brand_id')
                    ->update(['brand_id' => $brand->id]);

                $linked += $productsUpdated;
                if ($productsUpdated > 0) {
                    $this->line("🔗 Vinculados {$productsUpdated} produtos à marca: {$brandName}");
                }
            }
        }

        $this->info('📊 Resumo da migração:');
        $this->line("   Marcas criadas: {$created}");
        $this->line("   Marcas já existentes: {$updated}");
        $this->line("   Produtos vinculados: {$linked}");

        if ($dryRun) {
            $this->warn('⚠️  Dry-run concluído. Execute sem --dry-run para aplicar as mudanças.');
        } else {
            $this->info('✅ Migração concluída com sucesso!');
        }

        return Command::SUCCESS;
    }
}
