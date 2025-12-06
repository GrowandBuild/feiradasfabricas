<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductVariation;

class BackfillAttributesHash extends Command
{
    protected $signature = 'variations:backfill-hash {--chunk=500}';
    protected $description = 'Backfill attributes_hash for existing product variations';

    public function handle()
    {
        $chunk = (int)$this->option('chunk');
        $this->info('Starting backfill of attributes_hash...');
        ProductVariation::chunk($chunk, function($rows) use ($chunk) {
            foreach ($rows as $row) {
                try {
                    $attrs = $row->attributes ?? [];
                    $normalized = ProductVariation::normalizeAttributesForHash($attrs);
                    $hash = md5($normalized);
                    if ($row->attributes_hash !== $hash) {
                        $row->attributes_hash = $hash;
                        $row->save();
                        $this->line("Updated variation {$row->id} -> hash {$hash}");
                    }
                } catch (\Throwable $e) {
                    $this->error("Failed to compute hash for variation {$row->id}: " . $e->getMessage());
                }
            }
        });

        $this->info('Backfill complete.');
        return 0;
    }
}
