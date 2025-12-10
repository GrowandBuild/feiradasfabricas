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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->string('value'); // Ex: "Vermelho", "P", "38"
            $table->string('slug'); // Ex: "vermelho", "p", "38"
            $table->string('display_value')->nullable(); // Ex: "Pequeno" para "P"
            $table->string('color_hex')->nullable(); // Para type='color' - ex: "#FF0000"
            $table->string('image_url')->nullable(); // Para type='image' - ex: "/images/swatches/vermelho.jpg"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['attribute_id', 'slug']); // Um atributo não pode ter valores duplicados
            $table->index(['attribute_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_values');
    }
};

