<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cliente B2C de exemplo
        Customer::create([
            'type' => 'b2c',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao.silva@email.com',
            'phone' => '(11) 99999-9999',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'address' => 'Rua das Flores, 123',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01234-567',
            'country' => 'Brasil',
        ]);

        // Cliente B2B de exemplo
        Customer::create([
            'type' => 'b2b',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@empresa.com.br',
            'phone' => '(11) 88888-8888',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'company_name' => 'Tech Solutions Ltda',
            'cnpj' => '12.345.678/0001-90',
            'ie' => '123456789',
            'contact_person' => 'Maria Santos',
            'department' => 'Compras',
            'address' => 'Av. Paulista, 1000',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310-100',
            'country' => 'Brasil',
            'b2b_status' => 'approved',
            'credit_limit' => 50000.00,
        ]);

        // Cliente B2B pendente
        Customer::create([
            'type' => 'b2b',
            'first_name' => 'Carlos',
            'last_name' => 'Oliveira',
            'email' => 'carlos.oliveira@loja.com.br',
            'phone' => '(11) 77777-7777',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'company_name' => 'Loja Eletrônicos Oliveira',
            'cnpj' => '98.765.432/0001-10',
            'ie' => '987654321',
            'contact_person' => 'Carlos Oliveira',
            'department' => 'Vendas',
            'address' => 'Rua Comercial, 456',
            'neighborhood' => 'Comercial',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'zip_code' => '20000-000',
            'country' => 'Brasil',
            'b2b_status' => 'pending',
            'credit_limit' => 25000.00,
        ]);
    }
}
