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
            return; // Tabela criada, não precisa continuar
        }
        
        // Se a tabela existe, adicionar colunas que faltam UMA POR UMA
        // Isso evita erros se algumas já existirem
        
        // 1. Adicionar slug
        if (!Schema::hasColumn('attribute_values', 'slug')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->string('slug')->after('value');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna slug: ' . $e->getMessage());
            }
        }
        
        // 2. Adicionar display_value
        if (!Schema::hasColumn('attribute_values', 'display_value')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->string('display_value')->nullable()->after('slug');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna display_value: ' . $e->getMessage());
            }
        }
        
        // 3. Adicionar color_hex
        if (!Schema::hasColumn('attribute_values', 'color_hex')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->string('color_hex')->nullable()->after('display_value');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna color_hex: ' . $e->getMessage());
            }
        }
        
        // 4. Adicionar image_url
        if (!Schema::hasColumn('attribute_values', 'image_url')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->string('image_url')->nullable()->after('color_hex');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna image_url: ' . $e->getMessage());
            }
        }
        
        // 5. Adicionar sort_order
        if (!Schema::hasColumn('attribute_values', 'sort_order')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->integer('sort_order')->default(0)->after('image_url');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna sort_order: ' . $e->getMessage());
            }
        }
        
        // 6. Adicionar is_active
        if (!Schema::hasColumn('attribute_values', 'is_active')) {
            try {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                });
            } catch (\Exception $e) {
                \Log::warning('Erro ao adicionar coluna is_active: ' . $e->getMessage());
            }
        }
        
        // Popular valores existentes
        try {
            $values = DB::table('attribute_values')->get();
            foreach ($values as $value) {
                $updates = [];
                
                // Popular slug se não existir ou estiver vazio
                if (Schema::hasColumn('attribute_values', 'slug') && (empty($value->slug ?? null))) {
                    $updates['slug'] = Str::slug($value->value);
                }
                
                // Popular display_value se não existir ou estiver vazio
                if (Schema::hasColumn('attribute_values', 'display_value') && (empty($value->display_value ?? null))) {
                    $updates['display_value'] = $value->value;
                }
                
                if (!empty($updates)) {
                    DB::table('attribute_values')
                        ->where('id', $value->id)
                        ->update($updates);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Erro ao popular valores existentes: ' . $e->getMessage());
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
    }
};

