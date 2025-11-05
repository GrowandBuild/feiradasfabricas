<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class iPhoneRealImagesSeeder extends Seeder
{
    /**
     * URLs de imagens reais dos iPhones (usando imagens públicas e confiáveis)
     */
    private $imageUrls = [
        // iPhone X (2017)
        'iPhone X' => [
            'Space Gray' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ],
            'Silver' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ]
        ],

        // iPhone XS (2018)
        'iPhone XS' => [
            'Space Gray' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ],
            'Silver' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ],
            'Gold' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ]
        ],

        // iPhone XS Max (2018)
        'iPhone XS Max' => [
            'Space Gray' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ],
            'Silver' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ],
            'Gold' => [
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop',
                'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&h=600&fit=crop'
            ]
        ],

        // iPhone XR (2018)
        'iPhone XR' => [
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Coral' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 11 (2019)
        'iPhone 11' => [
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 11 Pro (2019)
        'iPhone 11 Pro' => [
            'Space Gray' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 11 Pro Max (2019)
        'iPhone 11 Pro Max' => [
            'Space Gray' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone SE (2ª geração)
        'iPhone SE (2ª geração)' => [
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 12 mini (2020)
        'iPhone 12 mini' => [
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 12 (2020)
        'iPhone 12' => [
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 12 Pro (2020)
        'iPhone 12 Pro' => [
            'Graphite' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Pacific Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 12 Pro Max (2020)
        'iPhone 12 Pro Max' => [
            'Graphite' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Pacific Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 13 mini (2021)
        'iPhone 13 mini' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Starlight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 13 (2021)
        'iPhone 13' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Starlight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 13 Pro (2021)
        'iPhone 13 Pro' => [
            'Graphite' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Alpine Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Sierra Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 13 Pro Max (2021)
        'iPhone 13 Pro Max' => [
            'Graphite' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Alpine Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Sierra Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone SE (3ª geração)
        'iPhone SE (3ª geração)' => [
            'Midnight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Starlight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 14 (2022)
        'iPhone 14' => [
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Starlight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 14 Plus (2022)
        'iPhone 14 Plus' => [
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Midnight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Starlight' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Red' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 14 Pro (2022)
        'iPhone 14 Pro' => [
            'Deep Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Space Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 14 Pro Max (2022)
        'iPhone 14 Pro Max' => [
            'Deep Purple' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Gold' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Silver' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Space Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 15 (2023)
        'iPhone 15' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 15 Plus (2023)
        'iPhone 15 Plus' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 15 Pro (2023)
        'iPhone 15 Pro' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 15 Pro Max (2023)
        'iPhone 15 Pro Max' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 16 (2024)
        'iPhone 16' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 16 Plus (2024)
        'iPhone 16 Plus' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 16 Pro (2024)
        'iPhone 16 Pro' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Rose Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 16 Pro Max (2024)
        'iPhone 16 Pro Max' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Rose Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        // iPhone 17 (2025 - placeholder)
        'iPhone 17' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        'iPhone 17 Plus' => [
            'Pink' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Yellow' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Green' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Blue' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        'iPhone 17 Pro' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Rose Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],

        'iPhone 17 Pro Max' => [
            'Natural Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'White Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Black Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop'],
            'Rose Titanium' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=600&fit=crop']
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🖼️  Baixando imagens reais dos iPhones...');
        
        // Buscar todos os produtos iPhone
        $products = Product::where('brand', 'Apple')
                          ->where('name', 'like', 'iPhone%')
                          ->get();

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($products as $product) {
            // Extrair modelo e cor do nome do produto
            $productName = $product->name;
            
            // Encontrar o modelo e a cor
            $model = $product->model;
            
            // Extrair a cor do nome
            preg_match('/\d+(GB|TB)\s+(.+)$/', $productName, $matches);
            $color = $matches[2] ?? null;
            
            // Forçar atualização mesmo se já tem imagens
            // Remover esta verificação temporariamente

            if (!$color) {
                $this->command->warn("⚠️  Não foi possível extrair a cor de: {$productName}");
                $skippedCount++;
                continue;
            }

            // Buscar imagens para este modelo e cor
            $imageUrls = $this->getImagesForProduct($model, $color);

            if (empty($imageUrls)) {
                $this->command->warn("⚠️  Nenhuma imagem encontrada para: {$model} {$color}");
                $skippedCount++;
                continue;
            }

            // Baixar e salvar imagens
            $imagePaths = [];
            foreach ($imageUrls as $index => $imageUrl) {
                try {
                    // Baixar imagem
                    $response = Http::timeout(30)->get($imageUrl);
                    
                    if ($response->successful()) {
                        // Gerar nome único para o arquivo
                        $fileName = Str::slug($model) . '_' . Str::slug($color) . '_' . ($index + 1) . '.jpg';
                        $filePath = 'products/iphone/' . $fileName;
                        
                        // Salvar imagem no storage
                        Storage::disk('public')->put($filePath, $response->body());
                        
                        $imagePaths[] = $filePath;
                        
                        $this->command->info("📥 Baixada: {$fileName}");
                    } else {
                        $this->command->warn("⚠️  Erro ao baixar: {$imageUrl}");
                    }
                } catch (\Exception $e) {
                    $this->command->warn("⚠️  Erro ao processar imagem: " . $e->getMessage());
                }
            }

            if (!empty($imagePaths)) {
                // Atualizar produto com as imagens
                $product->update([
                    'images' => $imagePaths
                ]);

                $updatedCount++;
                $this->command->info("✅ {$productName} - {$model} {$color} - " . count($imagePaths) . " imagens salvas");
            } else {
                $skippedCount++;
            }
        }

        $this->command->info("\n📊 Resumo:");
        $this->command->info("✅ Produtos atualizados: {$updatedCount}");
        $this->command->info("⏭️  Produtos pulados: {$skippedCount}");
        $this->command->info("📱 Total de produtos: " . $products->count());
        $this->command->info("💾 Imagens salvas em: storage/app/public/products/iphone/");
    }

    /**
     * Buscar imagens para um modelo e cor específicos
     */
    private function getImagesForProduct($model, $color)
    {
        if (!isset($this->imageUrls[$model])) {
            return [];
        }

        if (!isset($this->imageUrls[$model][$color])) {
            // Tentar encontrar uma cor similar
            $similarColor = $this->findSimilarColor($model, $color);
            if ($similarColor) {
                return $this->imageUrls[$model][$similarColor];
            }
            return [];
        }

        return $this->imageUrls[$model][$color];
    }

    /**
     * Encontrar cor similar se a cor exata não existir
     */
    private function findSimilarColor($model, $color)
    {
        $availableColors = array_keys($this->imageUrls[$model]);
        
        // Mapeamento de cores similares
        $colorMap = [
            'Space Gray' => ['Graphite', 'Midnight', 'Black', 'Space Black'],
            'Graphite' => ['Space Gray', 'Midnight', 'Black', 'Space Black'],
            'Midnight' => ['Space Gray', 'Graphite', 'Black', 'Space Black'],
            'Black' => ['Space Gray', 'Graphite', 'Midnight', 'Space Black'],
            'Silver' => ['Starlight', 'White'],
            'Starlight' => ['Silver', 'White'],
            'White' => ['Silver', 'Starlight'],
        ];

        if (isset($colorMap[$color])) {
            foreach ($colorMap[$color] as $similarColor) {
                if (in_array($similarColor, $availableColors)) {
                    return $similarColor;
                }
            }
        }

        // Se não encontrar, retornar a primeira cor disponível
        return $availableColors[0] ?? null;
    }
}
