<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'sell_b2b') || !Schema::hasColumn('products', 'sell_b2c')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('sell_b2b')->default(true)->after('department_id');
                $table->boolean('sell_b2c')->default(true)->after('sell_b2b');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('products', 'sell_b2b') || Schema::hasColumn('products', 'sell_b2c')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'sell_b2b')) $table->dropColumn('sell_b2b');
                if (Schema::hasColumn('products', 'sell_b2c')) $table->dropColumn('sell_b2c');
            });
        }
    }
};
