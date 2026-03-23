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
        Schema::create('users', function (Blueprint $table) {
            // Identificação
            $table->id();
            $table->string('name'); // Nome completo (para compatibilidade com Laravel Auth)
            $table->string('first_name', 100)->nullable(); // Nome
            $table->string('last_name', 100)->nullable(); // Sobrenome
            $table->string('cpf', 14)->unique()->nullable(); // CPF
            $table->date('date_of_birth')->nullable(); // Data de nascimento
            $table->enum('gender', ['M', 'F', 'O'])->nullable(); // Gênero: Masculino, Feminino, Outro
            $table->enum('marital_status', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'])->nullable(); // Estado civil

            // Contato
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable(); // Telefone fixo
            $table->string('cellphone', 20)->nullable(); // Celular
            $table->timestamp('email_verified_at')->nullable();

            // Endereço
            $table->string('address', 255)->nullable(); // Logradouro
            $table->string('address_number', 20)->nullable(); // Número
            $table->string('address_complement', 100)->nullable(); // Complemento
            $table->string('neighborhood', 100)->nullable(); // Bairro
            $table->string('city', 100)->nullable(); // Cidade
            $table->string('state', 2)->nullable(); // Estado (UF)
            $table->string('zip_code', 10)->nullable(); // CEP

            // Dados da Congregação
            $table->date('membership_date')->nullable(); // Data de entrada na igreja
            $table->integer('time_congregating_months')->nullable(); // Tempo congregando em meses
            $table->date('baptism_date')->nullable(); // Data de batismo
            $table->string('baptism_place', 255)->nullable(); // Local de batismo
            $table->boolean('is_baptized')->default(false); // É batizado?

            // Profissional
            $table->string('profession', 100)->nullable(); // Profissão
            $table->string('education_level', 50)->nullable(); // Escolaridade: fundamental, medio, superior, pos_graduacao
            $table->string('workplace', 255)->nullable(); // Local de trabalho

            // Contato de Emergência
            $table->string('emergency_contact_name', 100)->nullable(); // Nome do contato de emergência
            $table->string('emergency_contact_phone', 20)->nullable(); // Telefone do contato de emergência
            $table->string('emergency_contact_relationship', 50)->nullable(); // Relacionamento: pai, mae, esposo, esposa, filho, etc.

            // Sistema
            $table->string('password');
            $table->unsignedBigInteger('role_id')->default(2); // Foreign key para roles (default: membro)
            $table->boolean('is_active')->default(true); // Ativo/Inativo
            $table->string('photo')->nullable(); // Foto do membro
            $table->text('notes')->nullable(); // Observações gerais
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Soft deletes para manter histórico
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
