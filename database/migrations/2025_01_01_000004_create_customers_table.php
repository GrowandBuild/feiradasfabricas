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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('b2c'); // 'b2c' ou 'b2b'
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            
            // Campos específicos para B2B
            $table->string('company_name')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('ie')->nullable(); // Inscrição Estadual
            $table->string('contact_person')->nullable();
            $table->string('department')->nullable();
            
            // Endereço
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->default('Brasil');
            
            // Campos de status B2B
            $table->enum('b2b_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('b2b_notes')->nullable();
            $table->decimal('credit_limit', 12, 2)->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['type', 'b2b_status']);
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
