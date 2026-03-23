<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registration_segments', function (Blueprint $table) {
            // Descrição da categoria/faixa para o participante
            $table->text('description')->nullable()->after('label');

            // Restrição de gênero da faixa (all = sem restrição)
            $table->enum('gender', ['all', 'male', 'female'])->default('all')->after('description');

            // Override de campos obrigatórios por segmento
            // Ex: {"name":"required","email":"required","phone":"required","birth_date":"optional","gender":"disabled","cpf":"required"}
            // null = herda do evento
            $table->json('required_fields')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('event_registration_segments', function (Blueprint $table) {
            $table->dropColumn(['description', 'gender', 'required_fields']);
        });
    }
};
