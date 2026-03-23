<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Canonical log for daily reading check-ins (gamification, Bereano da Semana, etc.).
     */
    public function up(): void
    {
        Schema::create('user_reading_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->onDelete('cascade');
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->onDelete('cascade');
            $table->integer('day_number');
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['subscription_id', 'plan_day_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reading_logs');
    }
};
