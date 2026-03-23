<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_minutes_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('minutes_version_id')->constrained('meeting_minutes_versions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->unique(['minutes_version_id', 'user_id'], 'minutes_signatures_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes_signatures');
    }
};

