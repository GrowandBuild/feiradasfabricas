<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductVariation;

class BackfillVariationSlugs extends Command
{
    protected $signature = 'variations:backfill-slugs {--force : Regerar mesmo se já existir slug}';
    protected $description = 'Popula/atualiza o campo slug das variações baseado em cor + armazenamento + ram.';

    public function handle(): int
    {
        $force = $this->option('force');
        $count = 0; $updated = 0;

        ProductVariation::with('product')->chunk(500, function($chunk) use (&$count,&$updated,$force){
            foreach ($chunk as $variation) {
                $count++;
                $generated = $variation->generated_slug;
                if ($force || empty($variation->slug)) {
                    $variation->slug = $generated;
                    $variation->save();
                    $updated++;
                    $this->line("[OK] var #{$variation->id} => {$variation->slug}");
                }
            }
        });

        $this->info("Slugs processados: {$count}; atualizados: {$updated}");
        return Command::SUCCESS;
    }
}
