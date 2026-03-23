<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projection_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('background_type', 32)->default('solid'); // solid, gradient, image, video
            $table->text('background_value')->nullable(); // color hex, gradient json, url
            $table->string('font_family')->nullable();
            $table->string('font_size_base', 32)->nullable(); // e.g. 4vw
            $table->string('text_color', 32)->nullable();
            $table->string('text_shadow', 128)->nullable();
            $table->string('alignment', 16)->default('center');
            $table->unsignedTinyInteger('padding')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projection_themes');
    }
};
