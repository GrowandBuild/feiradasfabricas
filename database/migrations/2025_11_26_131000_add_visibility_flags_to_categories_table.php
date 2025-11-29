<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityFlagsToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'show_avatar')) {
                $table->boolean('show_avatar')->default(true)->after('cover');
            }
            if (!Schema::hasColumn('categories', 'show_cover')) {
                $table->boolean('show_cover')->default(true)->after('show_avatar');
            }
            if (!Schema::hasColumn('categories', 'show_title')) {
                $table->boolean('show_title')->default(true)->after('show_cover');
            }
            if (!Schema::hasColumn('categories', 'show_description')) {
                $table->boolean('show_description')->default(true)->after('show_title');
            }
            if (!Schema::hasColumn('categories', 'show_button')) {
                $table->boolean('show_button')->default(true)->after('show_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'show_button')) {
                $table->dropColumn('show_button');
            }
            if (Schema::hasColumn('categories', 'show_description')) {
                $table->dropColumn('show_description');
            }
            if (Schema::hasColumn('categories', 'show_title')) {
                $table->dropColumn('show_title');
            }
            if (Schema::hasColumn('categories', 'show_cover')) {
                $table->dropColumn('show_cover');
            }
            if (Schema::hasColumn('categories', 'show_avatar')) {
                $table->dropColumn('show_avatar');
            }
        });
    }
}
