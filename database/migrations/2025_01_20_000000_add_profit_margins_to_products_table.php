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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('profit_margin_b2b', 5, 2)->nullable()->default(10.00)->after('cost_price')->comment('Margem de lucro B2B em percentual');
            $table->decimal('profit_margin_b2c', 5, 2)->nullable()->default(20.00)->after('profit_margin_b2b')->comment('Margem de lucro B2C em percentual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['profit_margin_b2b', 'profit_margin_b2c']);
        });
    }
};

