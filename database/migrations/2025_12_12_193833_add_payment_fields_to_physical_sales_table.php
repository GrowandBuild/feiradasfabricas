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
        Schema::table('physical_sales', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('payment_method'); // pending, pending_confirmation, confirmed, failed
            $table->string('payment_reference')->nullable()->after('payment_status'); // NSU, código de autorização, etc.
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_reference');
            $table->foreignId('payment_confirmed_by')->nullable()->after('payment_confirmed_at')->constrained('admins')->onDelete('set null');
            $table->string('status')->default('pending')->after('notes'); // pending, completed, cancelled
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_sales', function (Blueprint $table) {
            $table->dropForeign(['payment_confirmed_by']);
            $table->dropColumn([
                'payment_status',
                'payment_reference',
                'payment_confirmed_at',
                'payment_confirmed_by',
                'status'
            ]);
        });
    }
};
