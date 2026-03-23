<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channel_status', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 32); // mail, webpush, sms
            $table->string('provider', 64); // mailgun, ses, firebase, twilio
            $table->timestamp('last_failure_at')->nullable();
            $table->unsignedInteger('failure_count')->default(0);
            $table->timestamp('open_until')->nullable(); // circuit open until this time
            $table->timestamps();

            $table->unique(['channel', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channel_status');
    }
};
