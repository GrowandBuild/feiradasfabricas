<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adicionar coluna se não existir
        if (!Schema::hasColumn('products', 'has_variations')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('has_variations')->default(false)->after('is_featured');
                $table->index('has_variations');
            });
        } else {
            // Coluna já existe, tentar adicionar índice apenas se não existir
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->index('has_variations');
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // Se o erro for de índice duplicado, ignorar (código 1061 ou mensagem contém "Duplicate key")
                if (strpos($e->getMessage(), 'Duplicate key') === false && strpos($e->getMessage(), '1061') === false) {
                    throw $e; // Relançar se for outro tipo de erro
                }
                // Índice já existe, continuar normalmente
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['has_variations']);
            $table->dropColumn('has_variations');
        });
    }
};

