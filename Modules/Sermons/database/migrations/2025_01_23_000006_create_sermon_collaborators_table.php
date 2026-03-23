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
        Schema::create('sermon_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Permissões do colaborador
            $table->enum('role', ['viewer', 'editor', 'co_author'])->default('viewer');
            $table->boolean('can_edit')->default(false); // Pode editar
            $table->boolean('can_delete')->default(false); // Pode deletar
            $table->boolean('can_invite')->default(false); // Pode convidar outros

            // Status
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->unique(['sermon_id', 'user_id']);
            $table->index(['sermon_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_collaborators');
    }
};
