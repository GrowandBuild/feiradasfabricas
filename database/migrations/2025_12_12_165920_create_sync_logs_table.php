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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'product', 'sale', 'coupon', 'inventory'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('sync_type'); // 'inventory', 'sale', 'coupon'
            $table->string('direction'); // 'ecommerce_to_physical', 'physical_to_ecommerce', 'bidirectional'
            $table->enum('status', ['pending', 'success', 'failed', 'partial'])->default('pending');
            $table->text('data')->nullable(); // JSON com dados sincronizados
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->index(['entity_type', 'entity_id']);
            $table->index(['sync_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('sync_logs');
    }
};
