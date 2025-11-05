<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 Iniciando seeders da Feira das Fábricas...');
        
        // Seeders obrigatórios - ordem importante!
        $this->command->info('📋 Executando seeders obrigatórios...');
        
        // 1. Configurações básicas (deve ser primeiro)
        $this->call([
            SettingsSeeder::class,
        ]);
        
        // 2. Departamentos básicos
        $this->call([
            DepartmentSeeder::class,
        ]);
        
        // 3. Categorias básicas (depende dos departamentos)
        $this->call([
            CategoriesSeeder::class,
        ]);
        
        // 4. Usuário admin padrão
        $this->call([
            AdminSeeder::class,
        ]);
        
        // Seeders opcionais de dados de exemplo
        // Use variável de ambiente SEED_WITH_EXAMPLES=1 para incluir dados de exemplo
        $withExamples = app()->environment('local') || env('SEED_WITH_EXAMPLES', false);
        
        if ($withExamples) {
            $this->command->info('🎭 Executando seeders de dados de exemplo...');
            
            $this->call([
                CategorySeeder::class,
                CustomerSeeder::class,
            ]);
            
            // Seeders específicos de produtos
            // Use variável de ambiente SEED_WITH_PRODUCTS=1 para incluir produtos
            $withProducts = env('SEED_WITH_PRODUCTS', false);
            
            if ($withProducts) {
                $this->command->info('📱 Executando seeder de produtos de produção...');
                
                // Apenas o ProductionProductsSeeder é responsável por carregar produtos no site
                $this->call([
                    ProductionProductsSeeder::class,
                ]);
            }
        }
        
        $this->command->info('✅ Seeders executados com sucesso!');
        $this->command->line('');
        $this->command->line('🔑 Credenciais de acesso:');
        $this->command->line('   Admin: admin@feiradasfabricas.com / admin123');
        $this->command->line('   Gerente: gerente@feiradasfabricas.com / gerente123');
        $this->command->line('');
        $this->command->line('💡 Dicas:');
        $this->command->line('   - Configure SEED_WITH_EXAMPLES=1 no .env para incluir dados de exemplo');
        $this->command->line('   - Configure SEED_WITH_PRODUCTS=1 no .env para incluir produtos de exemplo');
    }
}