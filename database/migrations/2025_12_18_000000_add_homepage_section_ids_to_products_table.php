<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('homepage_section_ids')->nullable()->after('department_id');
            $table->index('homepage_section_ids');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['homepage_section_ids']);
            $table->dropColumn('homepage_section_ids');
        });
    }
};
