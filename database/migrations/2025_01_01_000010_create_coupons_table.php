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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']); // Porcentagem ou valor fixo
            $table->decimal('value', 10, 2); // Valor do desconto
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Valor mínimo do pedido
            $table->integer('usage_limit')->nullable(); // Limite de uso
            $table->integer('used_count')->default(0); // Quantas vezes foi usado
            $table->timestamp('starts_at')->nullable(); // Data de início
            $table->timestamp('expires_at')->nullable(); // Data de expiração
            $table->boolean('is_active')->default(true);
            $table->json('applicable_products')->nullable(); // IDs dos produtos aplicáveis
            $table->json('applicable_categories')->nullable(); // IDs das categorias aplicáveis
            $table->enum('customer_type', ['all', 'b2c', 'b2b'])->default('all');
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
        Schema::dropIfExists('coupons');
    }
};
