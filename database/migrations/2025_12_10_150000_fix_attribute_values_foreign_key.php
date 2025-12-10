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
        if (Schema::hasTable('attribute_values')) {
            try {
                // Listar todas as foreign keys da tabela attribute_values
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'attribute_values'
                    AND COLUMN_NAME = 'attribute_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                // Remover todas as constraints existentes que referenciam attribute_id
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `attribute_values` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Constraint pode não existir, continuar
                    }
                }
                
                // Determinar qual tabela usar: product_attributes (preferido) ou attributes (fallback)
                $targetTable = null;
                if (Schema::hasTable('product_attributes')) {
                    $targetTable = 'product_attributes';
                } elseif (Schema::hasTable('attributes')) {
                    $targetTable = 'attributes';
                }
                
                // Criar constraint correta se a tabela alvo existir
                if ($targetTable) {
                    Schema::table('attribute_values', function (Blueprint $table) use ($targetTable) {
                        $table->foreign('attribute_id')
                              ->references('id')
                              ->on($targetTable)
                              ->onDelete('cascade');
                    });
                }
            } catch (\Exception $e) {
                // Se der erro, apenas logar mas não quebrar a migração
                \Log::warning('Erro ao corrigir foreign key de attribute_values: ' . $e->getMessage());
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
        // Não fazer rollback automático para evitar problemas
    }
};
