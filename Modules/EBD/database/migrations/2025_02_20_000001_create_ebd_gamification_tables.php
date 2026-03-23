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
        // 1. ebd_gamification_levels
        Schema::create('ebd_gamification_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level_number')->index();
            $table->string('name');
            $table->integer('xp_required');
            $table->string('icon_path');
            $table->timestamps();
        });

        // 2. ebd_gamification_points
        Schema::create('ebd_gamification_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('points');
            $table->enum('source_type', ['lesson', 'quiz', 'presence', 'bonus', 'game']);
            $table->string('description');
            $table->timestamps();
        });

        // 3. ebd_badges
        Schema::create('ebd_badges', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon_path');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        // 4. ebd_user_badges
        Schema::create('ebd_user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('ebd_badges')->cascadeOnDelete();
            $table->dateTime('awarded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_user_badges');
        Schema::dropIfExists('ebd_badges');
        Schema::dropIfExists('ebd_gamification_points');
        Schema::dropIfExists('ebd_gamification_levels');
    }
};
