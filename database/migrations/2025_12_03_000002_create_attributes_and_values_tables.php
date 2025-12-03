<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->nullable()->index();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id')->index();
            $table->string('value');
            $table->string('hex')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
