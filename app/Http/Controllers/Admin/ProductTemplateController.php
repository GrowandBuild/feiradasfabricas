<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTemplateController extends Controller
{
    public function createFromTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'brand' => 'required|string',
            'model' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'storages' => 'required|array',
            'colors' => 'required|array',
        ]);

        $category = Category::where('slug', 'smartphones')->first();
        $created = 0;

        foreach ($request->storages as $storage) {
            foreach ($request->colors as $color) {
                // Calcular preços baseados no template
                $priceMultiplier = match($storage) {
                    '64GB' => 1.0,
                    '128GB' => 1.1,
                    '256GB' => 1.25,
                    '512GB' => 1.5,
                    '1TB' => 1.8,
                    default => 1.0
                };

                $finalPrice = $request->base_price * $priceMultiplier;
                $b2bPrice = $finalPrice * 0.9;
                $costPrice = $finalPrice * 0.7;

                // Gerar SKU
                $colorCode = substr(strtoupper(str_replace(' ', '', $color)), 0, 2);
                $storageCode = str_replace('GB', '', $storage);
                $modelCode = str_replace(['iPhone ', ' '], ['', ''], $request->model);
                $sku = $modelCode . '-' . $storageCode . '-' . $colorCode;

                $productName = $request->model . ' ' . $storage . ' ' . $color;

                $product = Product::create([
                    'name' => $productName,
                    'slug' => Str::slug($productName),
                    'description' => $this->getTemplateDescription($request->template, $request->model, $storage),
                    'sku' => $sku,
                    'price' => $finalPrice,
                    'b2b_price' => $b2bPrice,
                    'cost_price' => $costPrice,
                    'stock_quantity' => rand(10, 50),
                    'min_stock' => 5,
                    'manage_stock' => true,
                    'in_stock' => true,
                    'is_active' => true,
                    'is_featured' => false,
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'specifications' => $this->getTemplateSpecs($request->template, $storage),
                    'weight' => 0.174,
                    'sort_order' => 1,
                ]);

                $product->categories()->attach($category->id);
                $created++;
            }
        }

        return redirect()->route('admin.products.index')
                        ->with('success', "{$created} produtos criados a partir do template!");
    }

    private function getTemplateDescription($template, $model, $storage)
    {
        $descriptions = [
            'premium' => "{$model} {$storage} - Smartphone premium com tecnologia de ponta, design elegante e performance excepcional.",
            'mid-range' => "{$model} {$storage} - Smartphone de médio porte com excelente custo-benefício e recursos avançados.",
            'entry' => "{$model} {$storage} - Smartphone acessível com recursos essenciais e qualidade confiável.",
            'iphone' => "{$model} {$storage} - iPhone com tela Super Retina XDR, chip A17 Pro e sistema de câmera profissional.",
            'samsung' => "{$model} {$storage} - Samsung Galaxy com tela Dynamic AMOLED, processador Snapdragon e câmera versátil.",
        ];

        return $descriptions[$template] ?? $descriptions['mid-range'];
    }

    private function getTemplateSpecs($template, $storage)
    {
        $specs = [
            'premium' => [
                'Tela' => '6.7" Super Retina XDR',
                'Processador' => 'A17 Pro',
                'Armazenamento' => $storage,
                'Câmera' => '48MP + 12MP + 12MP',
                'Bateria' => 'Até 29h de conversação',
                'Conectividade' => '5G, Wi-Fi 6E, Bluetooth 5.3'
            ],
            'mid-range' => [
                'Tela' => '6.1" Super Retina HD',
                'Processador' => 'A16 Bionic',
                'Armazenamento' => $storage,
                'Câmera' => '48MP + 12MP',
                'Bateria' => 'Até 20h de conversação',
                'Conectividade' => '5G, Wi-Fi 6, Bluetooth 5.0'
            ],
            'entry' => [
                'Tela' => '6.1" Liquid Retina HD',
                'Processador' => 'A15 Bionic',
                'Armazenamento' => $storage,
                'Câmera' => '12MP',
                'Bateria' => 'Até 17h de conversação',
                'Conectividade' => '4G LTE, Wi-Fi, Bluetooth 5.0'
            ],
        ];

        return $specs[$template] ?? $specs['mid-range'];
    }
}
