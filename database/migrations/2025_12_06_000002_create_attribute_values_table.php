<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('attribute_values')) {
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->string('value');
                $table->string('label')->nullable();
                $table->json('meta')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('attribute_values');
    }
};
