<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tornar a coluna image opcional (NULL) sem exigir doctrine/dbal
        DB::statement("ALTER TABLE `banners` MODIFY `image` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para NOT NULL (pode falhar se houver registros com NULL)
        DB::statement("ALTER TABLE `banners` MODIFY `image` VARCHAR(255) NOT NULL");
    }
};



