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
        if (!Schema::hasTable('product_variation_attributes')) {
            Schema::create('product_variation_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id')->constrained('product_variations')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade');
            $table->timestamps();
            
            // Uma variação não pode ter 2 valores do mesmo atributo
            $table->unique(['variation_id', 'attribute_id']);
            
            // Índices para busca rápida de variações por atributo
            $table->index(['attribute_id', 'attribute_value_id'], 'pva_attr_attrval_idx');
            $table->index('variation_id');
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
        Schema::dropIfExists('product_variation_attributes');
    }
};

