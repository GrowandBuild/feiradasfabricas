<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove a tabela promotional_badges completamente do banco de dados
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('promotional_badges');
    }

    /**
     * Reverse the migrations.
     * Não recria a tabela - ela foi removida propositalmente
     *
     * @return void
     */
    public function down()
    {
        // Tabela removida propositalmente - não recriar
    }
};
