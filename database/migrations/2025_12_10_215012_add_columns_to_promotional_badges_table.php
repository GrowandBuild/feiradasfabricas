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
        // Verificar se a tabela existe, se não, criá-la
        if (!Schema::hasTable('promotional_badges')) {
            Schema::create('promotional_badges', function (Blueprint $table) {
                $table->id();
                $table->string('text');
                $table->string('image')->nullable();
                $table->string('link')->nullable();
                $table->enum('position', ['bottom-right', 'bottom-left', 'center-bottom', 'top-right', 'top-left', 'center-top', 'center'])->default('center-bottom');
                $table->integer('auto_close_seconds')->default(0);
                $table->boolean('show_close_button')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // Se a tabela já existe, apenas adicionar as colunas que faltam
            Schema::table('promotional_badges', function (Blueprint $table) {
                if (!Schema::hasColumn('promotional_badges', 'text')) {
                    $table->string('text')->after('id');
                }
                if (!Schema::hasColumn('promotional_badges', 'image')) {
                    $table->string('image')->nullable()->after('text');
                }
                if (!Schema::hasColumn('promotional_badges', 'link')) {
                    $table->string('link')->nullable()->after('image');
                }
                if (!Schema::hasColumn('promotional_badges', 'position')) {
                    $table->enum('position', ['bottom-right', 'bottom-left', 'center-bottom', 'top-right', 'top-left', 'center-top', 'center'])->default('center-bottom')->after('link');
                }
                if (!Schema::hasColumn('promotional_badges', 'auto_close_seconds')) {
                    $table->integer('auto_close_seconds')->default(0)->after('position');
                }
                if (!Schema::hasColumn('promotional_badges', 'show_close_button')) {
                    $table->boolean('show_close_button')->default(true)->after('auto_close_seconds');
                }
                if (!Schema::hasColumn('promotional_badges', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('show_close_button');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se a tabela existe, remover as colunas adicionadas
        if (Schema::hasTable('promotional_badges')) {
            Schema::table('promotional_badges', function (Blueprint $table) {
                if (Schema::hasColumn('promotional_badges', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('promotional_badges', 'show_close_button')) {
                    $table->dropColumn('show_close_button');
                }
                if (Schema::hasColumn('promotional_badges', 'auto_close_seconds')) {
                    $table->dropColumn('auto_close_seconds');
                }
                if (Schema::hasColumn('promotional_badges', 'position')) {
                    $table->dropColumn('position');
                }
                if (Schema::hasColumn('promotional_badges', 'link')) {
                    $table->dropColumn('link');
                }
                if (Schema::hasColumn('promotional_badges', 'image')) {
                    $table->dropColumn('image');
                }
                if (Schema::hasColumn('promotional_badges', 'text')) {
                    $table->dropColumn('text');
                }
            });
        }
    }
};
