<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('department_sections')) {
            Schema::create('department_sections', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->enum('type', ['brand', 'category', 'tag', 'dynamic'])->default('brand');
                $table->string('reference')->nullable()->comment('Name or key for the referenced entity (brand name, tag name, dynamic key)');
                $table->unsignedBigInteger('reference_id')->nullable()->comment('Optional numeric reference (eg category id)');
                $table->string('title')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('enabled')->default(true);
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_sections');
    }
};
