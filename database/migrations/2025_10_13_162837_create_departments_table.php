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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do departamento (ex: Eletrônicos, Vestuário)
            $table->string('slug')->unique(); // Slug para URLs (ex: eletronicos, vestuario)
            $table->text('description')->nullable(); // Descrição do departamento
            $table->string('icon')->nullable(); // Ícone FontAwesome para o departamento
            $table->string('color')->default('#667eea'); // Cor principal do departamento
            $table->boolean('is_active')->default(true); // Se o departamento está ativo
            $table->integer('sort_order')->default(0); // Ordem de exibição
            $table->json('settings')->nullable(); // Configurações específicas do departamento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
