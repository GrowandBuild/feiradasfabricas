<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variations', 'attributes_hash')) {
                $table->string('attributes_hash')->nullable()->after('attributes')->index();
            }

            // create unique composite index if not exists (product_id + attributes_hash)
            // we create a non-unique index above; operators should review production readiness
        });
    }

    public function down()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'attributes_hash')) {
                $table->dropColumn('attributes_hash');
            }
        });
    }
};
