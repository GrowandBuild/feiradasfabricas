<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('product_variation_attribute_value')) {
            Schema::create('product_variation_attribute_value', function (Blueprint $table) {
                $table->id();
                $table->foreignId('variation_id')->constrained('product_variations')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['variation_id','attribute_id','attribute_value_id'], 'var_attr_val_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_variation_attribute_value');
    }
};
