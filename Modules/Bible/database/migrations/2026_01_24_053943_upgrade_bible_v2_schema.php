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
        // 1. Create Content Blocks Table (The Polymath)
        Schema::create('bible_plan_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->onDelete('cascade');

            $table->integer('order_index')->default(0);
            $table->enum('type', ['scripture', 'devotional', 'video'])->default('scripture');

            // For Devotionals & Videos
            $table->string('title')->nullable(); // "Morning Prayer"
            $table->text('body')->nullable(); // HTML Content or Video URL

            // For Scripture (Poly-columns for optimized querying without JSON overhead)
            $table->unsignedBigInteger('book_id')->nullable();
            $table->integer('chapter_start')->nullable();
            $table->integer('chapter_end')->nullable();
            $table->integer('verse_start')->nullable();
            $table->integer('verse_end')->nullable();

            $table->timestamps();
        });

        // 2. Enhance Subscriptions for Gamification
        Schema::table('bible_plan_subscriptions', function (Blueprint $table) {
            $table->integer('current_streak')->default(0)->after('is_completed');
            $table->integer('longest_streak')->default(0)->after('current_streak');
            $table->timestamp('last_activity_at')->nullable()->after('completed_at');
        });

        // 3. Clean up the old simpler tables if they exist (Upgrade path)
        // Note: In production we might migrate data, but here we assume fresh dev or safe drop
        if (Schema::hasTable('bible_plan_readings')) {
            // We could loop and migrate here, but for this task we drop to replace with Contents
            Schema::dropIfExists('bible_plan_readings');
        }

        // Remove legacy cols from Days if they exist (now handled by Contents)
        Schema::table('bible_plan_days', function (Blueprint $table) {
            if (Schema::hasColumn('bible_plan_days', 'devotional_content')) {
                $table->dropColumn('devotional_content');
            }
            if (Schema::hasColumn('bible_plan_days', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_plan_contents');

        Schema::table('bible_plan_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'longest_streak', 'last_activity_at']);
        });
    }
};
