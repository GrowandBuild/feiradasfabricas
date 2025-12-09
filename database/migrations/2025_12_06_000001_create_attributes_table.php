<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('attributes')) {
            Schema::create('attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->string('ui_type')->default('select'); // select, swatch, text, color
                $table->boolean('required')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('attributes');
    }
};
