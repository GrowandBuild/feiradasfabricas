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
        if ($this->command->option('with-examples') || app()->environment('local')) {
            $this->command->info('🎭 Executando seeders de dados de exemplo...');
            
            $this->call([
                CategorySeeder::class,
                CustomerSeeder::class,
            ]);
            
            // Seeders específicos de produtos (apenas se solicitado)
            if ($this->command->option('with-products')) {
                $this->command->info('📱 Executando seeders de produtos...');
                
                $this->call([
                    CompleteiPhoneSeeder::class,
                    MissingiPhoneSeeder::class,
                    iPhoneImagesSeeder::class,
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
        $this->command->line('   - Use --with-examples para incluir dados de exemplo');
        $this->command->line('   - Use --with-products para incluir produtos de exemplo');
    }
}