<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('verse_id')->constrained('verses')->onDelete('cascade');
            $table->text('note')->nullable(); // Nota pessoal do usuário
            $table->timestamps();

            $table->unique(['user_id', 'verse_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_favorites');
    }
};
