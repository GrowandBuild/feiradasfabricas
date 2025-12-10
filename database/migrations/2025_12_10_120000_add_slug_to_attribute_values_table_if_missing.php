<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Se a tabela existe mas não tem a coluna slug, adicionar
        if (Schema::hasTable('attribute_values') && !Schema::hasColumn('attribute_values', 'slug')) {
            Schema::table('attribute_values', function (Blueprint $table) {
                $table->string('slug')->after('value');
            });
            
            // Popular slugs existentes baseado no value
            $values = DB::table('attribute_values')->get();
            foreach ($values as $value) {
                DB::table('attribute_values')
                    ->where('id', $value->id)
                    ->update(['slug' => Str::slug($value->value)]);
            }
            
            // Adicionar índice único após popular
            Schema::table('attribute_values', function (Blueprint $table) {
                $table->unique(['attribute_id', 'slug']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('attribute_values', 'slug')) {
            Schema::table('attribute_values', function (Blueprint $table) {
                $table->dropUnique(['attribute_id', 'slug']);
                $table->dropColumn('slug');
            });
        }
    }
};

