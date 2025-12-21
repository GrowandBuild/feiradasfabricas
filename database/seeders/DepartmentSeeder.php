<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Verificar se a tabela departments existe antes de tentar seedar
        if (!Schema::hasTable('departments')) {
            $this->command->warn('⚠️  Tabela "departments" não existe. Execute as migrations primeiro.');
            $this->command->line('   Execute: php artisan migrate');
            return;
        }

        // Apenas os departamentos que realmente têm views criadas
        $departments = [
            [
                'name' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'description' => 'Smartphones, tablets, notebooks, acessórios e muito mais',
                'icon' => 'bi-phone',
                'color' => '#007bff',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Vestuário Masculino',
                'slug' => 'vestuario-masculino',
                'description' => 'Roupas, calçados e acessórios masculinos',
                'icon' => 'bi-person',
                'color' => '#28a745',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Vestuário Feminino',
                'slug' => 'vestuario-feminino',
                'description' => 'Roupas, calçados e acessórios femininos',
                'icon' => 'bi-person-heart',
                'color' => '#e83e8c',
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($departments as $departmentData) {
            Department::updateOrCreate(
                ['slug' => $departmentData['slug']],
                $departmentData
            );
        }

        $this->command->info('Departamentos básicos criados/atualizados com sucesso!');
        $this->command->line('Departamentos criados:');
        foreach ($departments as $dept) {
            $this->command->line('- ' . $dept['name'] . ' (' . $dept['slug'] . ')');
        }
    }
}