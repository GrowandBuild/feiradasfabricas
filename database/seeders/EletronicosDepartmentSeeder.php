<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class EletronicosDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar departamento de Eletrônicos
        Department::updateOrCreate(
            ['slug' => 'eletronicos'],
            [
                'name' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'description' => 'Smartphones, tablets, notebooks, acessórios e muito mais tecnologia.',
                'icon' => 'fas fa-laptop',
                'color' => '#667eea',
                'is_active' => true,
                'sort_order' => 1,
                'settings' => [
                    'show_brands' => true,
                    'show_specifications' => true,
                    'show_tech_details' => true,
                ]
            ]
        );

        $this->command->info('Departamento de Eletrônicos criado/atualizado com sucesso!');
    }
}
