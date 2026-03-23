<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projection_card_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title_label')->nullable(); // e.g. "Título"
            $table->string('subtitle_label')->nullable(); // e.g. "Subtítulo"
            $table->string('background_type', 32)->default('solid');
            $table->text('background_value')->nullable();
            $table->string('font_family')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projection_card_templates');
    }
};
