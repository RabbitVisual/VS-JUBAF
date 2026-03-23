<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->integer('verse_number');
            $table->text('text');
            $table->bigInteger('original_verse_id')->nullable(); // ID original do CSV
            $table->timestamps();

            $table->unique(['chapter_id', 'verse_number']);
            $table->index('chapter_id');

            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('text'); // Para busca de texto completo
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
