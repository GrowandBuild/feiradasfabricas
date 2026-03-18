<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_homepage_section', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('homepage_section_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('homepage_section_id')->references('id')->on('homepage_sections')->onDelete('cascade');

            $table->unique(['product_id', 'homepage_section_id']);
            $table->index(['product_id', 'homepage_section_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_homepage_section');
    }
};
