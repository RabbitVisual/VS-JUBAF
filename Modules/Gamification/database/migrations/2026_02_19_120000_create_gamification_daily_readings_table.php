<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_daily_readings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedSmallInteger('book_number');
            $table->unsignedSmallInteger('chapter_number');
            $table->string('bible_version_abbreviation', 20);
            $table->string('book_name', 100);
            $table->string('title', 150);
            $table->text('intro_text')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_daily_readings');
    }
};
