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
        Schema::create('bible_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('subtitle')->nullable();
            $table->text('description')->nullable(); // Short summary

            // Content
            $table->longText('content'); // Main study content (Rich Text)
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable(); // Optional video companion
            $table->string('audio_url')->nullable(); // Optional audio companion
            $table->string('audio_file')->nullable();

            // Relationships
            $table->foreignId('series_id')->nullable()->constrained('bible_series')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('sermon_categories')->nullOnDelete(); // Reusing sermon categories for now
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Status
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'members', 'private'])->default('members');
            $table->boolean('is_featured')->default(false);

            // Metadata
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility', 'series_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_studies');
    }
};
