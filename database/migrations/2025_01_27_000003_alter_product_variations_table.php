<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A tabela já existe, vamos apenas adicionar os campos que faltam
        Schema::table('product_variations', function (Blueprint $table) {
            // Verificar e adicionar campos que podem não existir
            if (!Schema::hasColumn('product_variations', 'name')) {
                $table->string('name')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_variations', 'compare_price')) {
                $table->decimal('compare_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('product_variations', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('in_stock');
            }
            if (!Schema::hasColumn('product_variations', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('is_default');
            }
            
            // Converter images de longtext para JSON se necessário
            // Isso será feito manualmente se necessário
            
            // Adicionar índices se não existirem
            if (!Schema::hasColumn('product_variations', 'is_default')) {
                // Índice será criado junto com a coluna
            }
        });
        
        // Adicionar índices separadamente
        Schema::table('product_variations', function (Blueprint $table) {
            $table->index(['product_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('product_variations', 'compare_price')) {
                $table->dropColumn('compare_price');
            }
            if (Schema::hasColumn('product_variations', 'is_default')) {
                $table->dropColumn('is_default');
            }
            if (Schema::hasColumn('product_variations', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};


