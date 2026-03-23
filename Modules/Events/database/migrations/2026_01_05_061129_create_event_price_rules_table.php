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
        Schema::create('event_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('label'); // Ex: "Crianças", "Adultos", "Idosos"
            $table->integer('min_age')->nullable(); // null = sem limite mínimo
            $table->integer('max_age')->nullable(); // null = sem limite máximo
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('order')->default(0); // Ordem de exibição
            $table->timestamps();

            // Índices
            $table->index('event_id');
            $table->index(['event_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_price_rules');
    }
};
