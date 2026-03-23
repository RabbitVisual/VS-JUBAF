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
        Schema::table('carousel_slides', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('image');
            $table->string('text_position')->default('center')->after('description'); // center, left, right, top, bottom
            $table->string('text_alignment')->default('center')->after('text_position'); // left, center, right
            $table->integer('overlay_opacity')->default(50)->after('text_alignment'); // 0-100
            $table->string('overlay_color')->default('#000000')->after('overlay_opacity');
            $table->string('text_color')->default('#ffffff')->after('overlay_color');
            $table->string('button_style')->default('primary')->after('link_text'); // primary, secondary, outline
            $table->string('transition_type')->default('fade')->after('order'); // fade, slide, zoom
            $table->integer('transition_duration')->default(700)->after('transition_type');
            $table->boolean('show_indicators')->default(true)->after('is_active');
            $table->boolean('show_controls')->default(true)->after('show_indicators');
            $table->timestamp('starts_at')->nullable()->after('show_controls');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carousel_slides', function (Blueprint $table) {
            $table->dropColumn([
                'alt_text',
                'text_position',
                'text_alignment',
                'overlay_opacity',
                'overlay_color',
                'text_color',
                'button_style',
                'transition_type',
                'transition_duration',
                'show_indicators',
                'show_controls',
                'starts_at',
                'ends_at',
            ]);
        });
    }
};
