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
        Schema::create('product_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'is_approved']);
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_feedbacks');
    }
};
