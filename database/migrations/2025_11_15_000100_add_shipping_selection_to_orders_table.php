<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_service')) {
                $table->string('shipping_service')->nullable()->after('shipping_amount');
            }
            if (!Schema::hasColumn('orders', 'shipping_service_id')) {
                $table->unsignedBigInteger('shipping_service_id')->nullable()->after('shipping_service');
            }
            if (!Schema::hasColumn('orders', 'shipping_delivery_days')) {
                $table->unsignedInteger('shipping_delivery_days')->nullable()->after('shipping_company');
            }
            // Nota: 'shipping_company' e 'shipping_zip_code' já existem no schema base
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_service')) {
                $table->dropColumn('shipping_service');
            }
            if (Schema::hasColumn('orders', 'shipping_service_id')) {
                $table->dropColumn('shipping_service_id');
            }
            if (Schema::hasColumn('orders', 'shipping_delivery_days')) {
                $table->dropColumn('shipping_delivery_days');
            }
        });
    }
};
