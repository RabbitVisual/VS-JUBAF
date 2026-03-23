<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->integer('chapter_number');
            $table->integer('total_verses')->default(0);
            $table->timestamps();

            $table->unique(['book_id', 'chapter_number']);
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
