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
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('banner_path')->nullable();

                // Datas e Localização
                $table->dateTime('start_date');
                $table->dateTime('end_date')->nullable();
                $table->string('location')->nullable(); // String ou JSON para coordenadas
                $table->json('location_data')->nullable(); // Coordenadas, endereço completo, etc.

                // Capacidade e Status
                $table->integer('capacity')->nullable(); // null = ilimitado
                $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
                $table->enum('visibility', ['public', 'members', 'both'])->default('public');

                // Formulário customizado
                $table->json('form_fields')->nullable(); // Campos extras do formulário

                // Metadados
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                // Índices
                $table->index('slug');
                $table->index('status');
                $table->index('visibility');
                $table->index('start_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
