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
        Schema::create('council_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('scheduled_date');
            $table->datetime('actual_start_time')->nullable();
            $table->datetime('actual_end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable(); // For hybrid/online meetings
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('meeting_type', ['ordinary', 'extraordinary', 'emergency'])->default('ordinary');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('president_id')->nullable()->constrained('council_members')->nullOnDelete();
            $table->text('minutes')->nullable(); // Ata da reunião
            $table->json('participants')->nullable(); // Lista de participantes
            $table->integer('quorum_present')->nullable(); // Number of voting members present
            $table->timestamps();

            $table->index(['status', 'scheduled_date']);
            $table->index('meeting_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_meetings');
    }
};
