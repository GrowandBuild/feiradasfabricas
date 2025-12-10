<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('attribute_values') && Schema::hasTable('product_attributes')) {
            // Listar todas as foreign keys da tabela attribute_values
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'attribute_values'
                AND COLUMN_NAME = 'attribute_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            // Remover todas as foreign keys existentes que apontam para a tabela errada
            foreach ($foreignKeys as $fk) {
                if ($fk->REFERENCED_TABLE_NAME !== 'product_attributes') {
                    try {
                        DB::statement("ALTER TABLE `attribute_values` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Ignorar se não conseguir remover
                    }
                }
            }
            
            // Verificar se já existe foreign key correta
            $hasCorrectFk = false;
            foreach ($foreignKeys as $fk) {
                if ($fk->REFERENCED_TABLE_NAME === 'product_attributes') {
                    $hasCorrectFk = true;
                    break;
                }
            }
            
            // Adicionar foreign key correta se não existir
            if (!$hasCorrectFk) {
                try {
                    Schema::table('attribute_values', function (Blueprint $table) {
                        $table->foreign('attribute_id')
                              ->references('id')
                              ->on('product_attributes')
                              ->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    // Se falhar, tentar com SQL direto
                    try {
                        DB::statement('
                            ALTER TABLE `attribute_values`
                            ADD CONSTRAINT `attribute_values_attribute_id_foreign`
                            FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
                        ');
                    } catch (\Exception $e2) {
                        // Ignorar se já existir
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Não fazer rollback para evitar problemas
    }
};

