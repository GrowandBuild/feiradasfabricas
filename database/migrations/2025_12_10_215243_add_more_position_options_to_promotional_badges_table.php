<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona mais opções de posicionamento ao enum
     */
    public function up(): void
    {
        // MySQL não permite alterar ENUM diretamente, então precisamos usar query raw
        DB::statement("ALTER TABLE promotional_badges MODIFY COLUMN position ENUM(
            'bottom-right',
            'bottom-left',
            'center-bottom',
            'top-right',
            'top-left',
            'center-top',
            'center'
        ) DEFAULT 'center-bottom'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE promotional_badges MODIFY COLUMN position ENUM(
            'bottom-right',
            'bottom-left',
            'center-bottom'
        ) DEFAULT 'center-bottom'");
    }
};
