<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('product_variations')) {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('product_variations');
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down()
    {
        // intentionally left empty: recreate manually if needed
    }
};
