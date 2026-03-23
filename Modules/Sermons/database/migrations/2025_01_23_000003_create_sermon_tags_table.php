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
        Schema::create('sermon_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da tag
            $table->string('slug')->unique(); // Slug para URLs
            $table->string('color')->nullable(); // Cor para visualização
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_tags');
    }
};
