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
        Schema::table('banners', function (Blueprint $table) {
            // Campos de estilo do texto
            $table->string('text_color')->nullable()->after('description');
            $table->string('text_size')->default('3rem')->after('text_color'); // Tamanho da fonte
            $table->string('text_align')->default('center')->after('text_size'); // left, center, right
            $table->string('text_font_weight')->default('700')->after('text_align'); // 300, 400, 600, 700, 800
            $table->integer('text_padding_top')->default(0)->after('text_font_weight');
            $table->integer('text_padding_bottom')->default(0)->after('text_padding_top');
            $table->integer('text_padding_left')->default(0)->after('text_padding_bottom');
            $table->integer('text_padding_right')->default(0)->after('text_padding_left');
            $table->integer('text_margin_top')->default(0)->after('text_padding_right');
            $table->integer('text_margin_bottom')->default(0)->after('text_margin_top');
            $table->integer('text_margin_left')->default(0)->after('text_margin_bottom');
            $table->integer('text_margin_right')->default(0)->after('text_margin_left');
            $table->string('text_shadow_color')->nullable()->after('text_margin_right');
            $table->integer('text_shadow_blur')->default(0)->after('text_shadow_color');
            
            // Campos de estilo da descrição
            $table->string('description_color')->nullable()->after('text_shadow_blur');
            $table->string('description_size')->default('1.2rem')->after('description_color');
            $table->string('description_align')->default('center')->after('description_size');
            $table->integer('description_margin_top')->default(2)->after('description_align');
            
            // Campos de estilo do banner
            $table->string('banner_background_color')->nullable()->after('description_margin_top');
            $table->string('banner_height')->default('400px')->after('banner_background_color');
            $table->integer('banner_padding_top')->default(3)->after('banner_height');
            $table->integer('banner_padding_bottom')->default(3)->after('banner_padding_top');
            
            // Campos para mostrar/ocultar elementos
            $table->boolean('show_title')->default(true)->after('banner_padding_bottom');
            $table->boolean('show_description')->default(true)->after('show_title');
            $table->boolean('show_overlay')->default(true)->after('show_description');
            
            // Overlay settings
            $table->string('overlay_color')->default('rgba(0,0,0,0.7)')->after('show_overlay');
            $table->integer('overlay_opacity')->default(70)->after('overlay_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'text_color',
                'text_size',
                'text_align',
                'text_font_weight',
                'text_padding_top',
                'text_padding_bottom',
                'text_padding_left',
                'text_padding_right',
                'text_margin_top',
                'text_margin_bottom',
                'text_margin_left',
                'text_margin_right',
                'text_shadow_color',
                'text_shadow_blur',
                'description_color',
                'description_size',
                'description_align',
                'description_margin_top',
                'banner_background_color',
                'banner_height',
                'banner_padding_top',
                'banner_padding_bottom',
                'show_title',
                'show_description',
                'show_overlay',
                'overlay_color',
                'overlay_opacity',
            ]);
        });
    }
};
