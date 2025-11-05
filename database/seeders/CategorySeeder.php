<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Os melhores smartphones do mercado',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Áudio',
                'slug' => 'audio',
                'description' => 'Fones, caixas de som e equipamentos de áudio',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Acessórios',
                'slug' => 'acessorios',
                'description' => 'Acessórios para seus dispositivos',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tablets',
                'slug' => 'tablets',
                'description' => 'Tablets para trabalho e entretenimento',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Notebooks',
                'slug' => 'notebooks',
                'description' => 'Notebooks para todas as necessidades',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
