<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'shipping_cep')) {
                $table->string('shipping_cep')->nullable()->after('zip_code');
            }
            if (!Schema::hasColumn('customers', 'shipping_option')) {
                $table->json('shipping_option')->nullable()->after('shipping_cep');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'shipping_option')) {
                $table->dropColumn('shipping_option');
            }
            if (Schema::hasColumn('customers', 'shipping_cep')) {
                $table->dropColumn('shipping_cep');
            }
        });
    }
};
