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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->date('birth_date');
            $table->string('document')->nullable(); // CPF, RG, etc.
            $table->string('phone')->nullable();
            $table->json('custom_responses')->nullable(); // Respostas aos campos customizados
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('registration_id');
            $table->index('email');
            $table->index('checked_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
