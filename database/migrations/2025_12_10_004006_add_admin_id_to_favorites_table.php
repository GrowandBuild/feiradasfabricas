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
        Schema::table('favorites', function (Blueprint $table) {
            // Adicionar admin_id se não existir
            if (!Schema::hasColumn('favorites', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('customer_id')->constrained()->onDelete('cascade');
                $table->unique(['admin_id', 'product_id']);
                $table->index('admin_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            if (Schema::hasColumn('favorites', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropUnique(['admin_id', 'product_id']);
                $table->dropIndex(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });
    }
};
