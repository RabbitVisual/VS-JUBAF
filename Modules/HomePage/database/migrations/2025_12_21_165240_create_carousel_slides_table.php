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
        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('text_position')->default('center');
            $table->string('text_alignment')->default('center');
            $table->integer('overlay_opacity')->default(50);
            $table->string('overlay_color')->default('#000000');
            $table->string('text_color')->default('#ffffff');
            $table->string('image')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_position')->default('top_center');
            $table->integer('logo_scale')->default(100);
            $table->string('link')->nullable();
            $table->string('link_text')->nullable();
            $table->string('button_style')->default('primary');
            $table->integer('order')->default(0);
            $table->string('transition_type')->default('fade');
            $table->integer('transition_duration')->default(700);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_indicators')->default(true);
            $table->boolean('show_controls')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_slides');
    }
};
