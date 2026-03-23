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
        Schema::create('bible_commentaries', function (Blueprint $table) {
            $table->id();

            // Reference
            $table->string('book'); // e.g., "Genesis"
            $table->integer('chapter');
            $table->integer('verse_start');
            $table->integer('verse_end')->nullable(); // Null if single verse

            // Content
            $table->string('title')->nullable(); // Optional title for the commentary block
            $table->longText('content'); // The commentary text
            $table->string('audio_path')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('cover_image')->nullable();

            // Relationships
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Status
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->boolean('is_official')->default(false); // Official church commentary vs user thought

            $table->timestamps();
            $table->softDeletes();

            // Optimizations for lookup
            $table->index(['book', 'chapter', 'verse_start']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_commentaries');
    }
};
