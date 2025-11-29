<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->json('product_ids')->nullable();
            $table->integer('limit')->default(4);
            $table->integer('position')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index('department_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('homepage_sections');
    }
};
