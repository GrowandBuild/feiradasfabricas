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
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('ram')->nullable(); // Ex: "8GB", "12GB"
            $table->string('storage')->nullable(); // Ex: "128GB", "256GB"
            $table->string('color')->nullable(); // Ex: "Preto", "Branco"
            $table->string('color_hex', 9)->nullable(); // Ex: #FFFFFF ou rgba
            $table->string('sku')->unique(); // SKU único para esta variação
            $table->decimal('price', 10, 2);
            $table->decimal('b2b_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
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
