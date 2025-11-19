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
        if (! Schema::hasColumn('banners', 'cta_layout')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('cta_layout')->nullable()->default('horizontal')->after('cta_align')->comment('Layout dos CTAs: horizontal|vertical');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('banners', 'cta_layout')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('cta_layout');
            });
        }
    }
};
