<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Department;

class AssociateProductsToEletronicosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eletronicos = Department::where('slug', 'eletronicos')->first();
        
        if (!$eletronicos) {
            $this->command->error('Departamento de eletrônicos não encontrado!');
            return;
        }

        // Buscar produtos que não têm departamento associado ou que são de eletrônicos
        $products = Product::whereNull('department_id')
            ->orWhere(function($query) {
                $query->where('brand', 'LIKE', '%Apple%')
                      ->orWhere('brand', 'LIKE', '%Samsung%')
                      ->orWhere('brand', 'LIKE', '%Xiaomi%')
                      ->orWhere('brand', 'LIKE', '%Motorola%')
                      ->orWhere('brand', 'LIKE', '%Infinix%')
                      ->orWhere('brand', 'LIKE', '%JBL%')
                      ->orWhere('brand', 'LIKE', '%Oppo%')
                      ->orWhere('brand', 'LIKE', '%Realme%')
                      ->orWhere('brand', 'LIKE', '%Tecno%');
            })
            ->get();

        $count = 0;
        foreach ($products as $product) {
            $product->update(['department_id' => $eletronicos->id]);
            $count++;
        }

        $this->command->info("{$count} produtos associados ao departamento de eletrônicos!");
    }
}
