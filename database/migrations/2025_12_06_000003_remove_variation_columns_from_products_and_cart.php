<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'variation_images')) {
                    $table->dropColumn('variation_images');
                }
                if (Schema::hasColumn('products', 'variation_images_urls')) {
                    $table->dropColumn('variation_images_urls');
                }
            });
        }

        if (Schema::hasTable('cart_items')) {
            Schema::table('cart_items', function (Blueprint $table) {
                if (Schema::hasColumn('cart_items', 'product_variation_id')) {
                    $table->dropForeign(['product_variation_id']);
                    $table->dropColumn('product_variation_id');
                }
            });
        }
    }

    public function down()
    {
        // manual recreation if needed
    }
};
