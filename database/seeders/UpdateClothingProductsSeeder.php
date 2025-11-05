<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Department;

class UpdateClothingProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vestuarioFeminino = Department::where('slug', 'vestuario-feminino')->first();
        $vestuarioMasculino = Department::where('slug', 'vestuario-masculino')->first();

        if (!$vestuarioFeminino || !$vestuarioMasculino) {
            $this->command->error('Departamentos de gênero não encontrados!');
            return;
        }

        // Produtos femininos
        $produtosFemininos = [
            'vestido-floral-elegante',
            'blusa-basica-feminina', 
            'calca-jeans-feminina',
            'sandalia-feminina',
            'bolsa-feminina-elegante',
            'relogio-feminino-elegante'
        ];

        foreach ($produtosFemininos as $slug) {
            $product = Product::where('slug', $slug)->first();
            if ($product) {
                $product->update(['department_id' => $vestuarioFeminino->id]);
            }
        }

        // Produtos masculinos
        $produtosMasculinos = [
            'camisa-social-masculina',
            'camiseta-basica-masculina',
            'calca-jeans-masculina',
            'sapato-social-masculino',
            'cinto-masculino-couro'
        ];

        foreach ($produtosMasculinos as $slug) {
            $product = Product::where('slug', $slug)->first();
            if ($product) {
                $product->update(['department_id' => $vestuarioMasculino->id]);
            }
        }

        // Produtos unissex (tênis)
        $produtoUnissex = Product::where('slug', 'tenis-esportivo-unissex')->first();
        if ($produtoUnissex) {
            // Vamos colocar no departamento feminino por padrão, mas pode ser alterado
            $produtoUnissex->update(['department_id' => $vestuarioFeminino->id]);
        }

        $this->command->info('Produtos atualizados com sucesso para os departamentos de gênero!');
    }
}
