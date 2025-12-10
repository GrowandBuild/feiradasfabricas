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
        if (!Schema::hasTable('regional_shipping')) {
            Schema::create('regional_shipping', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da região (ex: "Centro", "Zona Norte", "Região Metropolitana")
            $table->text('description')->nullable(); // Descrição opcional
            $table->string('cep_start', 8)->nullable(); // CEP inicial do range (ex: "70000000")
            $table->string('cep_end', 8)->nullable(); // CEP final do range (ex: "70999999")
            $table->text('cep_list')->nullable(); // Lista de CEPs específicos (JSON ou separados por vírgula)
            $table->enum('pricing_type', ['fixed', 'per_weight', 'per_item'])->default('fixed'); // Tipo de precificação
            $table->decimal('fixed_price', 10, 2)->nullable(); // Preço fixo
            $table->decimal('price_per_kg', 10, 2)->nullable(); // Preço por kg
            $table->decimal('price_per_item', 10, 2)->nullable(); // Preço por item
            $table->decimal('min_price', 10, 2)->nullable(); // Preço mínimo
            $table->decimal('max_price', 10, 2)->nullable(); // Preço máximo
            $table->integer('delivery_days_min')->default(1); // Prazo mínimo de entrega (dias)
            $table->integer('delivery_days_max')->nullable(); // Prazo máximo de entrega (dias)
            $table->integer('sort_order')->default(0); // Ordem de prioridade (menor = maior prioridade)
            $table->boolean('is_active')->default(true); // Ativo/Inativo
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index(['is_active', 'sort_order']);
            $table->index('cep_start');
            $table->index('cep_end');
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regional_shipping');
    }
};
