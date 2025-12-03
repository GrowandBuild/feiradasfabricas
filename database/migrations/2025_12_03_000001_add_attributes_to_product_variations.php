<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAttributesToProductVariations extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds an `attributes` JSON column and migrates existing ram/storage/color fields into it.
     */
    public function up()
    {
        if (!Schema::hasColumn('product_variations', 'attributes')) {
            Schema::table('product_variations', function (Blueprint $table) {
                $table->json('attributes')->nullable()->after('color_hex');
            });
        }

        // Migrate existing column values into attributes JSON for backward compatibility
        try {
            $rows = DB::table('product_variations')->select('id', 'ram', 'storage', 'color', 'color_hex')->get();
            foreach ($rows as $r) {
                $attrs = [];
                if (!is_null($r->ram) && $r->ram !== '') $attrs['ram'] = $r->ram;
                if (!is_null($r->storage) && $r->storage !== '') $attrs['storage'] = $r->storage;
                if (!is_null($r->color) && $r->color !== '') $attrs['color'] = $r->color;
                if (!is_null($r->color_hex) && $r->color_hex !== '') $attrs['color_hex'] = $r->color_hex;

                if (!empty($attrs)) {
                    DB::table('product_variations')->where('id', $r->id)->update(['attributes' => json_encode($attrs)]);
                }
            }
        } catch (\Exception $e) {
            // If something goes wrong, log but don't fail migration completely
            \Log::warning('Could not migrate product_variations attributes: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('product_variations', 'attributes')) {
            Schema::table('product_variations', function (Blueprint $table) {
                $table->dropColumn('attributes');
            });
        }
    }
}
