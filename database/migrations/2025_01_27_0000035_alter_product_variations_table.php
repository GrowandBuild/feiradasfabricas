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
        // Adicionar apenas o campo compare_price que não existe na migration CREATE
        Schema::table('product_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variations', 'compare_price')) {
                $table->decimal('compare_price', 10, 2)->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'compare_price')) {
                $table->dropColumn('compare_price');
            }
        });
    }
};



