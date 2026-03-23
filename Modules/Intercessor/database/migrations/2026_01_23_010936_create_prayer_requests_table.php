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
        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('prayer_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('privacy_level', ['public', 'members_only', 'intercessors_only', 'pastoral_only'])->default('members_only');
            $table->enum('urgency_level', ['normal', 'high', 'critical'])->default('normal');
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('expiration_date')->nullable();
            $table->enum('status', ['draft', 'pending', 'active', 'answered', 'archived'])->default('pending');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
