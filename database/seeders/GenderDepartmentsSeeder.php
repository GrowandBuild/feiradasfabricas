<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class GenderDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Primeiro, vamos atualizar o departamento de vestuário existente para ser mais genérico
        $vestuario = Department::where('slug', 'vestuario')->first();
        if ($vestuario) {
            $vestuario->update([
                'name' => 'Vestuário Geral',
                'description' => 'Departamento geral de vestuário - explore nossas categorias específicas',
                'icon' => 'fas fa-tshirt',
                'is_active' => false, // Vamos desativar o geral
            ]);
        }

        // Criar departamento de Vestuário Feminino
        Department::updateOrCreate(
            ['slug' => 'vestuario-feminino'],
            [
                'name' => 'Vestuário Feminino',
                'slug' => 'vestuario-feminino',
                'description' => 'Moda feminina com elegância e estilo. Roupas, calçados e acessórios exclusivos para mulheres.',
                'icon' => 'fas fa-female',
                'color' => '#e91e63',
                'is_active' => true,
                'sort_order' => 2,
                'settings' => [
                    'show_sizes' => true,
                    'show_colors' => true,
                    'show_materials' => true,
                    'gender' => 'feminino',
                ]
            ]
        );

        // Criar departamento de Vestuário Masculino
        Department::updateOrCreate(
            ['slug' => 'vestuario-masculino'],
            [
                'name' => 'Vestuário Masculino',
                'slug' => 'vestuario-masculino',
                'description' => 'Estilo masculino com conforto e qualidade. Roupas, calçados e acessórios para o homem moderno.',
                'icon' => 'fas fa-male',
                'color' => '#2196f3',
                'is_active' => true,
                'sort_order' => 3,
                'settings' => [
                    'show_sizes' => true,
                    'show_colors' => true,
                    'show_materials' => true,
                    'gender' => 'masculino',
                ]
            ]
        );

        $this->command->info('Departamentos de gênero criados com sucesso!');
    }
}
