<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seeder específico para produção - apenas dados essenciais
     * 
     * @return void
     */
    public function run()
    {
        $this->command->info('🏭 Executando seeders para PRODUÇÃO...');
        $this->command->warn('⚠️  Este seeder é específico para ambiente de produção!');
        
        // Apenas os seeders obrigatórios e seguros para produção
        $this->call([
            // Configurações básicas do sistema
            SettingsSeeder::class,
            
            // Departamentos básicos (apenas os que têm views)
            DepartmentSeeder::class,
            
            // Categorias básicas
            CategoriesSeeder::class,
            
            // Usuário admin padrão
            AdminSeeder::class,
        ]);
        
        $this->command->info('✅ Seeder de produção executado com sucesso!');
        $this->command->line('');
        $this->command->line('🔐 IMPORTANTE - Credenciais de acesso:');
        $this->command->line('   Super Admin: admin@feiradasfabricas.com / admin123');
        $this->command->line('   Gerente: gerente@feiradasfabricas.com / gerente123');
        $this->command->line('');
        $this->command->warn('🚨 ATENÇÃO: Altere as senhas padrão imediatamente após o primeiro login!');
        $this->command->line('');
        $this->command->line('📋 Próximos passos:');
        $this->command->line('   1. Acesse /admin/login');
        $this->command->line('   2. Faça login com as credenciais acima');
        $this->command->line('   3. Vá em Configurações e configure as APIs');
        $this->command->line('   4. Altere as senhas padrão');
        $this->command->line('   5. Configure os dados da empresa');
    }
}
