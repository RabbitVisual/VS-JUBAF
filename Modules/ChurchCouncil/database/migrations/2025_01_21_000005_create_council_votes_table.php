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
        Schema::create('council_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('council_agendas')->onDelete('cascade');
            $table->foreignId('council_member_id')->constrained('council_members')->onDelete('cascade');
            $table->enum('vote', ['yes', 'no', 'abstain', 'absent']);
            $table->text('comments')->nullable();
            $table->datetime('voted_at');
            $table->timestamps();

            $table->unique(['agenda_id', 'council_member_id'], 'unique_vote_per_member');
            $table->index(['vote', 'voted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_votes');
    }
};
