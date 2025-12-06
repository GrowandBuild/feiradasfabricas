<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // add a unique index on product_id + attributes_hash to prevent duplicates in future
            // MySQL allows multiple NULL values in unique index, so ensure backfill is run before making attributes_hash non-nullable
            $table->unique(['product_id', 'attributes_hash'], 'pv_product_attrs_hash_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropUnique('pv_product_attrs_hash_unique');
        });
    }
};
