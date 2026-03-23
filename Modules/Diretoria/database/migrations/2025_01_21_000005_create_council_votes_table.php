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
        Schema::create('diretoria_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('diretoria_agendas')->onDelete('cascade');
            $table->foreignId('diretoria_member_id')->constrained('diretoria_members')->onDelete('cascade');
            $table->enum('vote', ['yes', 'no', 'abstain', 'absent']);
            $table->text('comments')->nullable();
            $table->datetime('voted_at');
            $table->timestamps();

            $table->unique(['agenda_id', 'diretoria_member_id'], 'unique_vote_per_member');
            $table->index(['vote', 'voted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretoria_votes');
    }
};
