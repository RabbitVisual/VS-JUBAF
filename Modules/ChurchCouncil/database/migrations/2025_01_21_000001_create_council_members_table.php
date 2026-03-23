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
        Schema::create('council_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('council_position'); // Presidente, Vice-Presidente, Secretário, Tesoureiro, etc.
            $table->enum('council_role', ['president', 'vice_president', 'secretary', 'treasurer', 'member', 'lideranca', 'deacon'])->default('member');
            $table->date('term_start');
            $table->date('term_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('responsibilities')->nullable();
            $table->json('permissions')->nullable(); // Permissões específicas do membro
            $table->timestamps();

            $table->unique(['user_id', 'council_position'], 'unique_user_position');
            $table->index(['council_role', 'is_active']);
            $table->index('term_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_members');
    }
};
