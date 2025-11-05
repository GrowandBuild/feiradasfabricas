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
        Schema::create('department_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Nome da marca (ex: "Apple", "Samsung")
            $table->string('image'); // Logo/marca da empresa
            $table->string('link')->nullable(); // Link opcional (pode apontar para produtos da marca)
            $table->integer('sort_order')->default(0); // Ordem de exibição
            $table->boolean('is_active')->default(true); // Se está ativo
            $table->timestamps();
            
            $table->index(['department_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_badges');
    }
};
