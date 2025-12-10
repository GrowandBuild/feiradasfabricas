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
        Schema::table('promotional_badges', function (Blueprint $table) {
            // Verificar se as colunas não existem antes de adicionar
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
                $table->enum('position', ['bottom-right', 'bottom-left', 'center-bottom'])->default('center-bottom')->after('link');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotional_badges', function (Blueprint $table) {
            $table->dropColumn([
                'text',
                'image',
                'link',
                'position',
                'auto_close_seconds',
                'show_close_button',
                'is_active',
            ]);
        });
    }
};
