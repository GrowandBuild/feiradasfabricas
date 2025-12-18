<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('physical_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique(); // Número da venda (ex: VF-2025-001)
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null'); // Operador do caixa
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null'); // Cliente (se identificado)
            
            // Dados da venda
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method'); // dinheiro, cartao_debito, cartao_credito, pix, etc
            $table->integer('installments')->default(1); // Parcelas (se cartão)
            $table->text('notes')->nullable(); // Observações
            
            // Status e sincronização
            $table->boolean('synced_to_ecommerce')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->boolean('nfe_issued')->default(false);
            $table->string('nfe_key')->nullable();
            
            $table->timestamps();
            
            $table->index(['synced_to_ecommerce', 'created_at']);
            $table->index('sale_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('physical_sales');
    }
};
