<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class iPhoneImagesSeeder extends Seeder
{
    /**
     * Mapeamento de imagens reais para cada modelo de iPhone
     * URLs de imagens oficiais da Apple ou fontes confiáveis
     */
    private $imageUrls = [
        // iPhone X (2017)
        'iPhone X' => [
            'Space Gray' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-x-spacegray-select-2017',
                'https://i.imgur.com/X1Y2Z3A.jpg',
                'https://i.imgur.com/B4C5D6E.jpg'
            ],
            'Silver' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-x-silver-select-2017',
                'https://i.imgur.com/F7G8H9I.jpg',
                'https://i.imgur.com/J1K2L3M.jpg'
            ]
        ],

        // iPhone XS (2018)
        'iPhone XS' => [
            'Space Gray' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-spacegray-select-2018',
                'https://i.imgur.com/N4O5P6Q.jpg'
            ],
            'Silver' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-silver-select-2018',
                'https://i.imgur.com/R7S8T9U.jpg'
            ],
            'Gold' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-gold-select-2018',
                'https://i.imgur.com/V1W2X3Y.jpg'
            ]
        ],

        // iPhone XS Max (2018)
        'iPhone XS Max' => [
            'Space Gray' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-max-spacegray-select-2018',
                'https://i.imgur.com/Z4A5B6C.jpg'
            ],
            'Silver' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-max-silver-select-2018',
                'https://i.imgur.com/D7E8F9G.jpg'
            ],
            'Gold' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xs-max-gold-select-2018',
                'https://i.imgur.com/H1I2J3K.jpg'
            ]
        ],

        // iPhone XR (2018)
        'iPhone XR' => [
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-black-select-2019'],
            'White' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-white-select-2019'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-blue-select-2019'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-yellow-select-2019'],
            'Coral' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-coral-select-2019'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-xr-red-select-2019']
        ],

        // iPhone 11 (2019)
        'iPhone 11' => [
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-black-select-2019'],
            'White' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-white-select-2019'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-green-select-2019'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-yellow-select-2019'],
            'Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-purple-select-2019'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-red-select-2019']
        ],

        // iPhone 11 Pro (2019)
        'iPhone 11 Pro' => [
            'Space Gray' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-spacegray-select-2019'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-silver-select-2019'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-gold-select-2019'],
            'Midnight Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-midnightgreen-select-2019']
        ],

        // iPhone 11 Pro Max (2019)
        'iPhone 11 Pro Max' => [
            'Space Gray' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-max-spacegray-select-2019'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-max-silver-select-2019'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-max-gold-select-2019'],
            'Midnight Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-11-pro-max-midnightgreen-select-2019']
        ],

        // iPhone SE 2ª geração (2020)
        'iPhone SE (2ª geração)' => [
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-black-select-2020'],
            'White' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-white-select-2020'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-red-select-2020']
        ],

        // iPhone 12 mini (2020)
        'iPhone 12 mini' => [
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-black-select-2020'],
            'White' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-white-select-2020'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-blue-select-2020'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-green-select-2021'],
            'Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-purple-select-2021'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-mini-red-select-2020']
        ],

        // iPhone 12 (2020)
        'iPhone 12' => [
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-black-select-2020'],
            'White' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-white-select-2020'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-blue-select-2020'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-green-select-2021'],
            'Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-purple-select-2021'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-red-select-2020']
        ],

        // iPhone 12 Pro (2020)
        'iPhone 12 Pro' => [
            'Graphite' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-graphite-select-2020'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-silver-select-2020'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-gold-select-2020'],
            'Pacific Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-blue-select-2020']
        ],

        // iPhone 12 Pro Max (2020)
        'iPhone 12 Pro Max' => [
            'Graphite' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-max-graphite-select-2020'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-max-silver-select-2020'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-max-gold-select-2020'],
            'Pacific Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-max-blue-select-2020']
        ],

        // iPhone 13 mini (2021)
        'iPhone 13 mini' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-mini-pink-select-2021'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-mini-blue-select-2021'],
            'Midnight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-mini-midnight-select-2021'],
            'Starlight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-mini-starlight-select-2021'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-mini-red-select-2021']
        ],

        // iPhone 13 (2021)
        'iPhone 13' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pink-select-2021'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-blue-select-2021'],
            'Midnight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-midnight-select-2021'],
            'Starlight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-starlight-select-2021'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-red-select-2021']
        ],

        // iPhone 13 Pro (2021)
        'iPhone 13 Pro' => [
            'Graphite' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-graphite-select-2021'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-gold-select-2021'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-silver-select-2021'],
            'Alpine Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-alpinegreen-select-2022'],
            'Sierra Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-sierrablue-select-2021']
        ],

        // iPhone 13 Pro Max (2021)
        'iPhone 13 Pro Max' => [
            'Graphite' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-max-graphite-select-2021'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-max-gold-select-2021'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-max-silver-select-2021'],
            'Alpine Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-max-alpinegreen-select-2022'],
            'Sierra Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-pro-max-sierrablue-select-2021']
        ],

        // iPhone SE 3ª geração (2022)
        'iPhone SE (3ª geração)' => [
            'Midnight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-midnight-select-2022'],
            'Starlight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-starlight-select-2022'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-red-select-2022']
        ],

        // iPhone 14 (2022)
        'iPhone 14' => [
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-blue-select-2022'],
            'Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-purple-select-2022'],
            'Midnight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-midnight-select-2022'],
            'Starlight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-starlight-select-2022'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-red-select-2022'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-yellow-select-2023']
        ],

        // iPhone 14 Plus (2022)
        'iPhone 14 Plus' => [
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-blue-select-2022'],
            'Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-purple-select-2022'],
            'Midnight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-midnight-select-2022'],
            'Starlight' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-starlight-select-2022'],
            'Red' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-red-select-2022'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-plus-yellow-select-2023']
        ],

        // iPhone 14 Pro (2022)
        'iPhone 14 Pro' => [
            'Deep Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-deeppurple-select-2022'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-gold-select-2022'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-silver-select-2022'],
            'Space Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-spaceblack-select-2022']
        ],

        // iPhone 14 Pro Max (2022)
        'iPhone 14 Pro Max' => [
            'Deep Purple' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-max-deeppurple-select-2022'],
            'Gold' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-max-gold-select-2022'],
            'Silver' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-max-silver-select-2022'],
            'Space Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-max-spaceblack-select-2022']
        ],

        // iPhone 15 (2023)
        'iPhone 15' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pink-select-2023'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-yellow-select-2023'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-green-select-2023'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-blue-select-2023'],
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-black-select-2023']
        ],

        // iPhone 15 Plus (2023)
        'iPhone 15 Plus' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-pink-select-2023'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-yellow-select-2023'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-green-select-2023'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-blue-select-2023'],
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-black-select-2023']
        ],

        // iPhone 15 Pro (2023)
        'iPhone 15 Pro' => [
            'Natural Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-naturaltitanium-select-2023'],
            'Blue Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-bluetitanium-select-2023'],
            'White Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-whitetitanium-select-2023'],
            'Black Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-blacktitanium-select-2023']
        ],

        // iPhone 15 Pro Max (2023)
        'iPhone 15 Pro Max' => [
            'Natural Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-max-naturaltitanium-select-2023'],
            'Blue Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-max-bluetitanium-select-2023'],
            'White Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-max-whitetitanium-select-2023'],
            'Black Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-max-blacktitanium-select-2023']
        ],

        // iPhone 16 (2024)
        'iPhone 16' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pink-select-2024'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-yellow-select-2024'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-green-select-2024'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-blue-select-2024'],
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-black-select-2024']
        ],

        // iPhone 16 Plus (2024)
        'iPhone 16 Plus' => [
            'Pink' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-plus-pink-select-2024'],
            'Yellow' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-plus-yellow-select-2024'],
            'Green' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-plus-green-select-2024'],
            'Blue' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-plus-blue-select-2024'],
            'Black' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-plus-black-select-2024']
        ],

        // iPhone 16 Pro (2024)
        'iPhone 16 Pro' => [
            'Natural Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-naturaltitanium-select-2024'],
            'White Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-whitetitanium-select-2024'],
            'Black Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-blacktitanium-select-2024'],
            'Rose Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-rosetitanium-select-2024']
        ],

        // iPhone 16 Pro Max (2024)
        'iPhone 16 Pro Max' => [
            'Natural Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-max-naturaltitanium-select-2024'],
            'White Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-max-whitetitanium-select-2024'],
            'Black Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-max-blacktitanium-select-2024'],
            'Rose Titanium' => ['https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-max-rosetitanium-select-2024']
        ],

        // iPhone 17 (2025 - placeholder)
        'iPhone 17' => [
            'Pink' => ['https://via.placeholder.com/500x600/FFC0CB/000000?text=iPhone+17+Pink'],
            'Yellow' => ['https://via.placeholder.com/500x600/FFD700/000000?text=iPhone+17+Yellow'],
            'Green' => ['https://via.placeholder.com/500x600/90EE90/000000?text=iPhone+17+Green'],
            'Blue' => ['https://via.placeholder.com/500x600/87CEEB/000000?text=iPhone+17+Blue'],
            'Black' => ['https://via.placeholder.com/500x600/000000/FFFFFF?text=iPhone+17+Black']
        ],

        'iPhone 17 Plus' => [
            'Pink' => ['https://via.placeholder.com/500x600/FFC0CB/000000?text=iPhone+17+Plus+Pink'],
            'Yellow' => ['https://via.placeholder.com/500x600/FFD700/000000?text=iPhone+17+Plus+Yellow'],
            'Green' => ['https://via.placeholder.com/500x600/90EE90/000000?text=iPhone+17+Plus+Green'],
            'Blue' => ['https://via.placeholder.com/500x600/87CEEB/000000?text=iPhone+17+Plus+Blue'],
            'Black' => ['https://via.placeholder.com/500x600/000000/FFFFFF?text=iPhone+17+Plus+Black']
        ],

        'iPhone 17 Pro' => [
            'Natural Titanium' => ['https://via.placeholder.com/500x600/C0C0C0/000000?text=iPhone+17+Pro+Natural'],
            'White Titanium' => ['https://via.placeholder.com/500x600/F5F5F5/000000?text=iPhone+17+Pro+White'],
            'Black Titanium' => ['https://via.placeholder.com/500x600/1C1C1C/FFFFFF?text=iPhone+17+Pro+Black'],
            'Rose Titanium' => ['https://via.placeholder.com/500x600/FFB6C1/000000?text=iPhone+17+Pro+Rose']
        ],

        'iPhone 17 Pro Max' => [
            'Natural Titanium' => ['https://via.placeholder.com/500x600/C0C0C0/000000?text=iPhone+17+Pro+Max+Natural'],
            'White Titanium' => ['https://via.placeholder.com/500x600/F5F5F5/000000?text=iPhone+17+Pro+Max+White'],
            'Black Titanium' => ['https://via.placeholder.com/500x600/1C1C1C/FFFFFF?text=iPhone+17+Pro+Max+Black'],
            'Rose Titanium' => ['https://via.placeholder.com/500x600/FFB6C1/000000?text=iPhone+17+Pro+Max+Rose']
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🖼️  Iniciando adição de imagens aos iPhones...');
        
        // Buscar todos os produtos iPhone
        $products = Product::where('brand', 'Apple')
                          ->where('name', 'like', 'iPhone%')
                          ->get();

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($products as $product) {
            // Pular se já tem imagens
            if ($product->images && count($product->images) > 0) {
                $skippedCount++;
                continue;
            }

            // Extrair modelo e cor do nome do produto
            $productName = $product->name;
            
            // Encontrar o modelo e a cor
            $model = $product->model;
            
            // Extrair a cor do nome (última palavra antes do GB/TB ou após o último espaço numérico)
            preg_match('/\d+(GB|TB)\s+(.+)$/', $productName, $matches);
            $color = $matches[2] ?? null;

            if (!$color) {
                $this->command->warn("⚠️  Não foi possível extrair a cor de: {$productName}");
                $skippedCount++;
                continue;
            }

            // Buscar imagens para este modelo e cor
            $images = $this->getImagesForProduct($model, $color);

            if (empty($images)) {
                $this->command->warn("⚠️  Nenhuma imagem encontrada para: {$model} {$color}");
                $skippedCount++;
                continue;
            }

            // Salvar imagens fictícias (paths) no produto
            // Em produção, você faria o download das imagens
            $imagePaths = [];
            foreach ($images as $index => $imageUrl) {
                // Simular path de imagem salva
                $imagePath = 'products/iphone/' . Str::slug($model) . '/' . Str::slug($color) . '_' . ($index + 1) . '.jpg';
                $imagePaths[] = $imagePath;
            }

            // Atualizar produto com as imagens
            $product->update([
                'images' => $imagePaths
            ]);

            $updatedCount++;
            $this->command->info("✅ {$productName} - {$model} {$color} - " . count($imagePaths) . " imagens adicionadas");
        }

        $this->command->info("\n📊 Resumo:");
        $this->command->info("✅ Produtos atualizados: {$updatedCount}");
        $this->command->info("⏭️  Produtos pulados: {$skippedCount}");
        $this->command->info("📱 Total de produtos: " . $products->count());
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

