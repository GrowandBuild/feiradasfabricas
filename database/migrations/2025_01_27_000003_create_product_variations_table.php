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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique(); // SKU único para variação
            $table->string('name')->nullable(); // Nome gerado automaticamente (ex: "Blusa - P - Vermelho")
            $table->decimal('price', 10, 2); // Preço da variação (NUNCA nullable)
            $table->decimal('b2b_price', 10, 2)->nullable(); // Preço B2B da variação
            $table->integer('stock_quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_default')->default(false); // Variação padrão
            $table->decimal('weight', 8, 2)->nullable(); // Pode sobrescrever peso do produto pai
            $table->json('images')->nullable(); // Imagens específicas da variação
            $table->timestamps();
            
            $table->index(['product_id', 'is_default']); // Para buscar variação padrão rapidamente
            $table->index(['product_id', 'in_stock']); // Para filtrar variações em estoque
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
};

