<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Department;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Buscar departamentos existentes
        $eletronicos = Department::where('slug', 'eletronicos')->first();
        $vestuarioMasculino = Department::where('slug', 'vestuario-masculino')->first();
        $vestuarioFeminino = Department::where('slug', 'vestuario-feminino')->first();

        $categories = [];

        // Categorias para Eletrônicos
        if ($eletronicos) {
            $categories = array_merge($categories, [
                [
                    'name' => 'Smartphones',
                    'slug' => 'smartphones',
                    'description' => 'Celulares e smartphones',
                    'department_id' => $eletronicos->id,
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Tablets',
                    'slug' => 'tablets',
                    'description' => 'Tablets e dispositivos móveis',
                    'department_id' => $eletronicos->id,
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'name' => 'Notebooks',
                    'slug' => 'notebooks',
                    'description' => 'Laptops e notebooks',
                    'department_id' => $eletronicos->id,
                    'is_active' => true,
                    'sort_order' => 3
                ],
                [
                    'name' => 'Acessórios',
                    'slug' => 'acessorios-eletronicos',
                    'description' => 'Acessórios para dispositivos eletrônicos',
                    'department_id' => $eletronicos->id,
                    'is_active' => true,
                    'sort_order' => 4
                ]
            ]);
        }

        // Categorias para Vestuário Masculino
        if ($vestuarioMasculino) {
            $categories = array_merge($categories, [
                [
                    'name' => 'Camisetas',
                    'slug' => 'camisetas-masculinas',
                    'description' => 'Camisetas masculinas',
                    'department_id' => $vestuarioMasculino->id,
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Calças',
                    'slug' => 'calcas-masculinas',
                    'description' => 'Calças masculinas',
                    'department_id' => $vestuarioMasculino->id,
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'name' => 'Camisas',
                    'slug' => 'camisas-masculinas',
                    'description' => 'Camisas masculinas',
                    'department_id' => $vestuarioMasculino->id,
                    'is_active' => true,
                    'sort_order' => 3
                ],
                [
                    'name' => 'Calçados',
                    'slug' => 'calcados-masculinos',
                    'description' => 'Sapatos e tênis masculinos',
                    'department_id' => $vestuarioMasculino->id,
                    'is_active' => true,
                    'sort_order' => 4
                ]
            ]);
        }

        // Categorias para Vestuário Feminino
        if ($vestuarioFeminino) {
            $categories = array_merge($categories, [
                [
                    'name' => 'Blusas',
                    'slug' => 'blusas-femininas',
                    'description' => 'Blusas femininas',
                    'department_id' => $vestuarioFeminino->id,
                    'is_active' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Vestidos',
                    'slug' => 'vestidos',
                    'description' => 'Vestidos femininos',
                    'department_id' => $vestuarioFeminino->id,
                    'is_active' => true,
                    'sort_order' => 2
                ],
                [
                    'name' => 'Calças',
                    'slug' => 'calcas-femininas',
                    'description' => 'Calças femininas',
                    'department_id' => $vestuarioFeminino->id,
                    'is_active' => true,
                    'sort_order' => 3
                ],
                [
                    'name' => 'Calçados',
                    'slug' => 'calcados-femininos',
                    'description' => 'Sapatos e sandálias femininos',
                    'department_id' => $vestuarioFeminino->id,
                    'is_active' => true,
                    'sort_order' => 4
                ]
            ]);
        }

        // Criar categorias
        $createdCount = 0;
        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            
            if ($category->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info('Categorias básicas criadas/atualizadas com sucesso!');
        $this->command->line('Total de categorias: ' . count($categories));
        $this->command->line('Novas categorias criadas: ' . $createdCount);
        
        // Mostrar categorias por departamento
        $this->command->line('Categorias por departamento:');
        foreach (Department::with('categories')->get() as $dept) {
            if ($dept->categories->count() > 0) {
                $this->command->line('- ' . $dept->name . ': ' . $dept->categories->count() . ' categorias');
            }
        }
    }
}
