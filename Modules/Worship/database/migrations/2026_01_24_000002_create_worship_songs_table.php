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
        Schema::create('worship_songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->integer('bpm')->nullable();
            $table->string('time_signature')->default('4/4')->nullable();
            $table->string('original_key')->nullable();
            $table->text('content_chordpro')->nullable();
            $table->text('lyrics_only')->nullable();
            $table->string('youtube_id')->nullable();
            $table->json('themes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_songs');
    }
};
