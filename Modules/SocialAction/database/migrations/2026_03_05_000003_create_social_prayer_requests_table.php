<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');          // encrypted at model
            $table->text('request');         // encrypted at model
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['pending', 'prayed', 'archived'])->default('pending');
            $table->timestamp('prayed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_prayer_requests');
    }
};
