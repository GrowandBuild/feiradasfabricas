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
        // Add columns only if they don't already exist (safe to re-run)
        if (! Schema::hasColumn('banners', 'primary_button_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('primary_button_text')->nullable()->after('show_primary_button_desktop');
            });
        }

        if (! Schema::hasColumn('banners', 'primary_button_link')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('primary_button_link')->nullable()->after('primary_button_text');
            });
        }

        if (! Schema::hasColumn('banners', 'secondary_button_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('secondary_button_text')->nullable()->after('show_secondary_button_desktop');
            });
        }

        if (! Schema::hasColumn('banners', 'secondary_button_link')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('secondary_button_link')->nullable()->after('secondary_button_text');
            });
        }

        if (! Schema::hasColumn('banners', 'cta_position')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('cta_position')->nullable()->default('bottom')->after('overlay_opacity'); // top, center, bottom
            });
        }

        if (! Schema::hasColumn('banners', 'cta_size')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('cta_size')->nullable()->default('medium')->after('cta_position'); // small, medium, large
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop only if columns exist
        if (Schema::hasColumn('banners', 'primary_button_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('primary_button_text');
            });
        }
        if (Schema::hasColumn('banners', 'primary_button_link')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('primary_button_link');
            });
        }
        if (Schema::hasColumn('banners', 'secondary_button_text')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('secondary_button_text');
            });
        }
        if (Schema::hasColumn('banners', 'secondary_button_link')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('secondary_button_link');
            });
        }
        if (Schema::hasColumn('banners', 'cta_position')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('cta_position');
            });
        }
        if (Schema::hasColumn('banners', 'cta_size')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('cta_size');
            });
        }
    }
};
