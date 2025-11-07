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
        Schema::table('product_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variations', 'color_hex')) {
                $table->string('color_hex', 9)->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'color_hex')) {
                $table->dropColumn('color_hex');
            }
        });
    }
};

