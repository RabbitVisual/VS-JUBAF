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
        Schema::create('ebd_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da classe (ex: "Adultos - Sala 1", "Jovens", "Adolescentes", "Crianças")
            $table->string('age_group'); // adult, youth, teen, children
            $table->text('description')->nullable();
            $table->string('room')->nullable(); // Sala física
            $table->time('schedule_time')->default('09:00:00'); // Horário padrão (09:00)
            $table->integer('max_students')->nullable(); // Capacidade máxima
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Ordem de exibição
            $table->timestamps();
            $table->softDeletes();

            $table->index(['age_group', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_classes');
    }
};
