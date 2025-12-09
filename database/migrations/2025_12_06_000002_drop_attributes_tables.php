<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('attribute_values')) {
            Schema::dropIfExists('attribute_values');
        }

        if (Schema::hasTable('attributes')) {
            Schema::dropIfExists('attributes');
        }
    }

    public function down()
    {
        // recreate if needed manually
    }
};
