<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_chapter_audio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_version_id')->constrained('bible_versions')->onDelete('cascade');
            $table->unsignedSmallInteger('book_number');
            $table->unsignedSmallInteger('chapter_number');
            $table->string('audio_url', 1000);
            $table->timestamps();

            $table->unique(['bible_version_id', 'book_number', 'chapter_number'], 'bible_chapter_audio_version_book_ch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_chapter_audio');
    }
};
