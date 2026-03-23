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
        Schema::create('worship_academy_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('instrument_id')->constrained('worship_instruments')->cascadeOnDelete();
            $table->string('level')->default('beginner'); // beginner, intermediate, advanced
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        Schema::create('worship_academy_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('worship_academy_courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('video_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->foreignId('requirement_song_id')->nullable()->constrained('worship_songs')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('worship_musician_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('worship_academy_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_musician_progress');
        Schema::dropIfExists('worship_academy_lessons');
        Schema::dropIfExists('worship_academy_courses');
    }
};
