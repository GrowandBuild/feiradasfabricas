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
        if (Schema::hasTable('product_variations')) {
            // Adicionar coluna images se não existir
            if (!Schema::hasColumn('product_variations', 'images')) {
                Schema::table('product_variations', function (Blueprint $table) {
                    $table->json('images')->nullable()->after('weight');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('product_variations', 'images')) {
            Schema::table('product_variations', function (Blueprint $table) {
                $table->dropColumn('images');
            });
        }
    }
};

