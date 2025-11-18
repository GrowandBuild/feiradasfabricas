<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índices simples que ajudam em ordenação e buscas por prefixo
        Schema::table('products', function (Blueprint $table) {
            if (!self::hasIndex('products', 'products_name_index')) {
                $table->index('name', 'products_name_index');
            }
            if (!self::hasIndex('products', 'products_brand_index')) {
                $table->index('brand', 'products_brand_index');
            }
            if (!self::hasIndex('products', 'products_model_index')) {
                $table->index('model', 'products_model_index');
            }
            if (!self::hasIndex('products', 'products_stock_flags_index')) {
                $table->index(['is_unavailable', 'in_stock', 'is_active'], 'products_stock_flags_index');
            }
        });

        Schema::table('product_variations', function (Blueprint $table) {
            if (!self::hasIndex('product_variations', 'pv_color_index')) {
                $table->index('color', 'pv_color_index');
            }
            if (!self::hasIndex('product_variations', 'pv_storage_index')) {
                $table->index('storage', 'pv_storage_index');
            }
            if (!self::hasIndex('product_variations', 'pv_ram_index')) {
                $table->index('ram', 'pv_ram_index');
            }
        });

        // FULLTEXT (MySQL/MariaDB) para melhorar LIKE %termo%
        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                // Evitar erro se já existir
                DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_products_main (name, brand, model, description)');
            }
        } catch (\Throwable $e) {
            // Ignorar caso o motor/versão não suporte FULLTEXT, ou o índice já exista
        }
    }

    public function down(): void
    {
        // Remover índices criados (se existirem)
        Schema::table('products', function (Blueprint $table) {
            self::dropIndexIfExists('products', 'products_name_index');
            self::dropIndexIfExists('products', 'products_brand_index');
            self::dropIndexIfExists('products', 'products_model_index');
            self::dropIndexIfExists('products', 'products_stock_flags_index');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            self::dropIndexIfExists('product_variations', 'pv_color_index');
            self::dropIndexIfExists('product_variations', 'pv_storage_index');
            self::dropIndexIfExists('product_variations', 'pv_ram_index');
        });

        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE products DROP INDEX fulltext_products_main');
            }
        } catch (\Throwable $e) {
            // ignorar
        }
    }

    private static function hasIndex(string $table, string $index): bool
    {
        try {
            $conn = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $conn->listTableDetails($table);
            return $doctrineTable->hasIndex($index);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function dropIndexIfExists(string $table, string $index): void
    {
        try {
            if (self::hasIndex($table, $index)) {
                Schema::table($table, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            }
        } catch (\Throwable $e) {
            // ignorar
        }
    }
};
