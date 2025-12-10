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
        if (!Schema::hasTable('attribute_values')) {
            // Se a tabela não existe, criar completa
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
                $table->string('value');
                $table->string('slug');
                $table->string('display_value')->nullable();
                $table->string('color_hex')->nullable();
                $table->string('image_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique(['attribute_id', 'slug']);
                $table->index(['attribute_id', 'is_active', 'sort_order']);
            });
        } else {
            // Se a tabela existe, adicionar colunas que faltam
            Schema::table('attribute_values', function (Blueprint $table) {
                // Adicionar slug se não existir
                if (!Schema::hasColumn('attribute_values', 'slug')) {
                    $table->string('slug')->after('value');
                }
                
                // Adicionar display_value se não existir
                if (!Schema::hasColumn('attribute_values', 'display_value')) {
                    $table->string('display_value')->nullable()->after('slug');
                }
                
                // Adicionar color_hex se não existir
                if (!Schema::hasColumn('attribute_values', 'color_hex')) {
                    $table->string('color_hex')->nullable()->after('display_value');
                }
                
                // Adicionar image_url se não existir
                if (!Schema::hasColumn('attribute_values', 'image_url')) {
                    $table->string('image_url')->nullable()->after('color_hex');
                }
                
                // Adicionar sort_order se não existir
                if (!Schema::hasColumn('attribute_values', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('image_url');
                }
                
                // Adicionar is_active se não existir
                if (!Schema::hasColumn('attribute_values', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                }
            });
            
            // Popular slugs e display_values existentes
            $values = DB::table('attribute_values')->get();
            foreach ($values as $value) {
                $updates = [];
                
                // Popular slug se não existir ou estiver vazio
                if (empty($value->slug ?? null)) {
                    $updates['slug'] = Str::slug($value->value);
                }
                
                // Popular display_value se não existir ou estiver vazio
                if (empty($value->display_value ?? null)) {
                    $updates['display_value'] = $value->value;
                }
                
                if (!empty($updates)) {
                    DB::table('attribute_values')
                        ->where('id', $value->id)
                        ->update($updates);
                }
            }
            
            // Adicionar índices se não existirem
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->unique(['attribute_id', 'slug'], 'attribute_values_attribute_id_slug_unique');
                });
            } catch (\Exception $e) {
                // Índice já existe, ignorar
            }
            
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->index(['attribute_id', 'is_active', 'sort_order'], 'attribute_values_attr_active_sort_idx');
                });
            } catch (\Exception $e) {
                // Índice já existe, ignorar
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Não remover colunas para evitar perda de dados
        // Se necessário, criar migração específica para rollback
    }
};

