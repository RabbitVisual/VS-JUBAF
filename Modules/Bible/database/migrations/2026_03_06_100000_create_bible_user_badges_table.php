<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: Gamification badges (Bereano da Semana, Fiel ao Pacto, Leitor do Corpo).
     */
    public function up(): void
    {
        Schema::create('bible_user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_key', 64);
            $table->foreignId('subscription_id')->nullable()->constrained('bible_plan_subscriptions')->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_user_badges');
    }
};
