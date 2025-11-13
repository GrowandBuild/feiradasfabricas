<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];
        $now = now()->toAtomString();

        // Páginas principais
        $urls[] = [ 'loc' => route('home'), 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '1.0' ];
        $urls[] = [ 'loc' => route('products'), 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '0.9' ];

        // Produtos e variações
        Product::active()->available()->with('activeVariations')->chunk(500, function($chunk) use (&$urls) {
            foreach ($chunk as $product) {
                $urls[] = [
                    'loc' => route('product', $product->slug),
                    'lastmod' => optional($product->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
                foreach ($product->activeVariations as $variation) {
                    if (!$variation->slug) continue;
                    $urls[] = [
                        'loc' => route('product.variant', [$product->slug, $variation->slug]),
                        'lastmod' => optional($variation->updated_at ?? $product->updated_at)->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            }
        });

        $xml = view('sitemap.xml', compact('urls'))->render();
        return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
