<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrador Principal - Super Admin
        Admin::updateOrCreate(
            ['email' => 'admin@feiradasfabricas.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
                'permissions' => ['*'], // Todas as permissões
            ]
        );

        // Gerente - Admin com permissões limitadas
        Admin::updateOrCreate(
            ['email' => 'gerente@feiradasfabricas.com'],
            [
                'name' => 'Gerente',
                'password' => Hash::make('gerente123'),
                'role' => 'admin',
                'is_active' => true,
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'products.create',
                    'products.edit',
                    'categories.view',
                    'categories.create',
                    'categories.edit',
                    'orders.view',
                    'orders.edit',
                    'customers.view',
                    'customers.edit',
                    'reports.view',
                ],
            ]
        );

        $this->command->info('Usuários admin criados/atualizados com sucesso!');
        $this->command->line('Admin: admin@feiradasfabricas.com / admin123');
        $this->command->line('Gerente: gerente@feiradasfabricas.com / gerente123');
    }
}
