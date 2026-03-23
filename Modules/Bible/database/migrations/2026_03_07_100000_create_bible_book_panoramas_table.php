<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_book_panoramas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('book_number')->comment('1-66 canônico');
            $table->string('testament', 10)->default('old'); // old | new
            $table->string('author')->nullable();
            $table->string('date_written')->nullable();
            $table->text('theme_central')->nullable();
            $table->text('recipients')->nullable();
            $table->string('language', 10)->default('pt');
            $table->timestamps();

            $table->unique(['book_number', 'language']);
            $table->index('book_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_book_panoramas');
    }
};
