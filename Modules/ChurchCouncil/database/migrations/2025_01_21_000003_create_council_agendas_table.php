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
        Schema::create('council_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('council_meetings')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->integer('order')->default(0);
            $table->enum('status', ['pending', 'discussed', 'approved', 'rejected', 'postponed'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->foreignId('presented_by')->nullable()->constrained('council_members')->nullOnDelete();
            $table->text('discussion_notes')->nullable();
            $table->text('decision')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('council_members')->nullOnDelete();
            $table->datetime('discussed_at')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'order']);
            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_agendas');
    }
};
