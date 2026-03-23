<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_reading_audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->onDelete('cascade');
            $table->string('action', 64);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_reading_audit_log');
    }
};
