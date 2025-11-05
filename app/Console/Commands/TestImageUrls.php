<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class TestImageUrls extends Command
{
    protected $signature = 'images:test-urls';
    protected $description = 'Testar URLs das imagens dos produtos';

    public function handle()
    {
        $products = Product::take(3)->get();
        
        foreach ($products as $product) {
            $this->info("Produto: {$product->name}");
            $this->line("SKU: {$product->sku}");
            $this->line("First Image URL: {$product->first_image}");
            
            // Testar se a URL é acessível
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'HEAD'
                ]
            ]);
            
            $headers = @get_headers($product->first_image, 1, $context);
            
            if ($headers && strpos($headers[0], '200') !== false) {
                $this->info("✅ Imagem acessível");
            } else {
                $this->error("❌ Imagem não acessível");
                if ($headers) {
                    $this->line("Status: " . $headers[0]);
                }
            }
            
            $this->line("---");
        }
    }
}