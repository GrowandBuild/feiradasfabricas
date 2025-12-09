<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('product_variations')) {
            Schema::create('product_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('sku')->nullable()->unique();
                $table->decimal('price', 10, 2)->nullable();
                $table->decimal('compare_price', 10, 2)->nullable();
                $table->integer('stock_quantity')->default(0);
                $table->json('dimensions')->nullable();
                $table->json('images')->nullable();
                $table->json('options')->nullable(); // array of {attribute_id, attribute_value_id}
                $table->string('options_hash')->nullable()->index();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
};
