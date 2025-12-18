<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário atendente de caixa
        Admin::updateOrCreate(
            ['email' => 'caixa@feiradasfabricas.com'],
            [
                'name' => 'Atendente de Caixa',
                'password' => Hash::make('caixa123'),
                'role' => 'cashier',
                'is_active' => true,
                'permissions' => [
                    'pdv.view',
                    'pdv.create_sale',
                    'pdv.confirm_payment',
                    'pdv.search_products',
                ],
            ]
        );

        $this->command->info('✅ Usuário atendente de caixa criado com sucesso!');
        $this->command->info('📧 Email: caixa@feiradasfabricas.com');
        $this->command->info('🔑 Senha: caixa123');
        $this->command->warn('⚠️  Altere a senha após o primeiro acesso!');
    }
}
