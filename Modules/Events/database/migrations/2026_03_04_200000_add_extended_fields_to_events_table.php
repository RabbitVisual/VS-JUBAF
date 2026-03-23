<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Público-alvo: array de strings ex ["youth","children","families"]
            $table->json('target_audience')->nullable()->after('is_featured');

            // Restrições de idade do evento (diferente das faixas de inscrição)
            $table->tinyInteger('min_age_restriction')->unsigned()->nullable()->after('target_audience');
            $table->tinyInteger('max_age_restriction')->unsigned()->nullable()->after('min_age_restriction');

            // Código de vestimenta
            $table->string('dress_code', 100)->nullable()->after('max_age_restriction');

            // Prazo para inscrição
            $table->dateTime('registration_deadline')->nullable()->after('dress_code');

            // Máximo de participantes por inscrição (ex: família pode inscrever até 6)
            $table->tinyInteger('max_per_registration')->unsigned()->default(10)->after('registration_deadline');

            // Contato do evento
            $table->string('contact_name', 150)->nullable()->after('max_per_registration');
            $table->string('contact_email', 150)->nullable()->after('contact_name');
            $table->string('contact_phone', 30)->nullable()->after('contact_email');
            $table->string('contact_whatsapp', 30)->nullable()->after('contact_phone');

            // Recorrência
            $table->enum('recurrence_type', ['weekly', 'monthly', 'yearly'])->nullable()->after('contact_whatsapp');

            // Campos padrão do formulário de inscrição: quais são obrigatórios/opcionais/desabilitados
            // Ex: {"name":"required","email":"required","phone":"optional","birth_date":"optional","gender":"disabled","cpf":"disabled"}
            $table->json('default_required_fields')->nullable()->after('recurrence_type');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'target_audience',
                'min_age_restriction',
                'max_age_restriction',
                'dress_code',
                'registration_deadline',
                'max_per_registration',
                'contact_name',
                'contact_email',
                'contact_phone',
                'contact_whatsapp',
                'recurrence_type',
                'default_required_fields',
            ]);
        });
    }
};
