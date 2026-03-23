<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_audit_logs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 32); // in_app, email, webpush, sms
            $table->string('status', 32); // sent, failed, opened
            $table->foreignId('notification_id')->nullable()->constrained('system_notifications')->nullOnDelete();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::table('notification_audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'channel', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_audit_logs');
    }
};
