<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Department;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar departamentos
        $eletronicos = Department::where('slug', 'eletronicos')->first();

        // Criar banner principal com imagem local
        Banner::create([
            'title' => 'Bem-vindo à Feira das Fábricas',
            'description' => 'Sua loja online completa com produtos de qualidade para todas as suas necessidades. Eletrônicos, vestuário e muito mais em um só lugar!',
            'image' => 'banners/banner-principal-desktop.jpg',
            'mobile_image' => 'banners/banner-principal-mobile.jpg',
            'link' => '/departamento/eletronicos',
            'position' => 'hero',
            'sort_order' => 1,
            'is_active' => true,
            'target_audience' => 'all',
            'department_id' => null,
        ]);

        // Banner de promoção
        Banner::create([
            'title' => 'Frete Grátis em Compras Acima de R$ 100',
            'description' => 'Aproveite nossa promoção especial de frete grátis',
            'image' => 'banners/banner-promocao-desktop.jpg',
            'mobile_image' => 'banners/banner-promocao-mobile.jpg',
            'link' => '/produtos',
            'position' => 'category',
            'sort_order' => 1,
            'is_active' => true,
            'target_audience' => 'all',
            'department_id' => null,
        ]);

        // Banners para departamento de Eletrônicos
        if ($eletronicos) {
            Banner::create([
                'title' => 'Eletrônicos em Promoção',
                'description' => 'Smartphones, notebooks e muito mais com preços especiais',
                'image' => 'banners/banner-eletronicos-desktop.jpg',
                'mobile_image' => 'banners/banner-eletronicos-mobile.jpg',
                'link' => '/departamento/eletronicos',
                'position' => 'hero',
                'sort_order' => 1,
                'is_active' => true,
                'target_audience' => 'all',
                'department_id' => $eletronicos->id,
            ]);

            Banner::create([
                'title' => 'Apple com Preços Imperdíveis',
                'description' => 'iPhone, iPad e MacBook com desconto especial',
                'image' => 'banners/banner-apple-desktop.jpg',
                'mobile_image' => 'banners/banner-apple-mobile.jpg',
                'link' => '/departamento/eletronicos?marca=apple',
                'position' => 'category',
                'sort_order' => 1,
                'is_active' => true,
                'target_audience' => 'all',
                'department_id' => $eletronicos->id,
            ]);

            Banner::create([
                'title' => 'Samsung Galaxy',
                'description' => 'A nova linha Galaxy com tecnologia de ponta',
                'image' => 'banners/banner-samsung-desktop.jpg',
                'mobile_image' => 'banners/banner-samsung-mobile.jpg',
                'link' => '/departamento/eletronicos?marca=samsung',
                'position' => 'category',
                'sort_order' => 2,
                'is_active' => true,
                'target_audience' => 'all',
                'department_id' => $eletronicos->id,
            ]);
        }

        // Banner de rodapé global
        Banner::create([
            'title' => 'Newsletter',
            'description' => 'Receba nossas ofertas exclusivas por email',
            'image' => 'banners/banner-newsletter-desktop.jpg',
            'mobile_image' => 'banners/banner-newsletter-mobile.jpg',
            'link' => '/newsletter',
            'position' => 'footer',
            'sort_order' => 1,
            'is_active' => true,
            'target_audience' => 'all',
            'department_id' => null,
        ]);

        $this->command->info('✅ Banners criados com sucesso!');
        $this->command->info('📝 Para usar imagens locais, faça upload através do painel admin.');
    }
}