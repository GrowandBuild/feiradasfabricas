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
        Schema::table('banners', function (Blueprint $table) {
            $table->boolean('show_primary_button_desktop')->default(true)->after('show_overlay');
            $table->boolean('show_primary_button_mobile')->default(true)->after('show_primary_button_desktop');
            $table->boolean('show_secondary_button_desktop')->default(true)->after('show_primary_button_mobile');
            $table->boolean('show_secondary_button_mobile')->default(true)->after('show_secondary_button_desktop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'show_primary_button_desktop',
                'show_primary_button_mobile',
                'show_secondary_button_desktop',
                'show_secondary_button_mobile',
            ]);
        });
    }
};

