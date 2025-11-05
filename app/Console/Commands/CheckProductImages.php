<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class CheckProductImages extends Command
{
    protected $signature = 'products:check-images';
    protected $description = 'Verificar imagens dos produtos';

    public function handle()
    {
        $products = Product::all();
        
        $this->info("Total de produtos: " . $products->count());
        
        $withImages = $products->filter(function($product) {
            return !empty($product->images) && is_array($product->images);
        });
        
        $this->info("Produtos com imagens: " . $withImages->count());
        
        if ($withImages->count() > 0) {
            $this->info("\nPrimeiros 3 produtos com imagens:");
            $withImages->take(3)->each(function($product) {
                $this->line("- {$product->name}");
                $this->line("  SKU: {$product->sku}");
                $this->line("  Imagens: " . json_encode($product->images));
                $this->line("  First Image URL: {$product->first_image}");
                $this->line("");
            });
        }
        
        $withoutImages = $products->filter(function($product) {
            return empty($product->images) || !is_array($product->images);
        });
        
        if ($withoutImages->count() > 0) {
            $this->warn("\nProdutos sem imagens:");
            $withoutImages->take(5)->each(function($product) {
                $this->line("- {$product->name} (SKU: {$product->sku})");
            });
        }
    }
}