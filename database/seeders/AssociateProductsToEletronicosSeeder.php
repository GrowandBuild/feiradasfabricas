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

        // Lista de marcas de eletrônicos
        $electronicBrands = ['Apple', 'Samsung', 'Xiaomi', 'Motorola', 'Infinix', 'JBL', 'Oppo', 'Realme', 'Tecno', 'Sony'];
        
        // Buscar produtos que são de eletrônicos e que não têm department_id definido
        // (os seeders InfinixProductsSeeder e OppoProductsSeeder já definem department_id ao criar)
        $products = Product::whereNull('department_id')
            ->where(function($query) use ($electronicBrands) {
                // Verificar se é uma das marcas de eletrônicos
                $first = true;
                foreach ($electronicBrands as $brand) {
                    if ($first) {
                        $query->where('brand', 'LIKE', "%{$brand}%");
                        $first = false;
                    } else {
                        $query->orWhere('brand', 'LIKE', "%{$brand}%");
                    }
                }
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
