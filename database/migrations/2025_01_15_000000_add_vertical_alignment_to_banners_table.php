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
            $table->string('text_vertical_align')->default('bottom')->after('text_align'); // top, center, bottom
            $table->string('description_vertical_align')->default('bottom')->after('description_align'); // top, center, bottom
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'text_vertical_align',
                'description_vertical_align',
            ]);
        });
    }
};

