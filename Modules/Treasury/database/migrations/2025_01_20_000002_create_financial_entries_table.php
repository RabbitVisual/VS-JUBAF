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
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']); // Entrada ou Saída
            $table->enum('category', [
                'tithe',           // Dízimo
                'offering',        // Oferta
                'donation',        // Doação
                'ministry_donation', // Doação para ministério
                'campaign',        // Campanha
                'maintenance',     // Manutenção
                'utilities',       // Contas (água, luz, etc)
                'salary',         // Salários
                'equipment',       // Equipamentos
                'event',          // Eventos
                'other',           // Outros
            ]);
            $table->string('title'); // Título/Descrição
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2); // Valor
            $table->date('entry_date'); // Data da entrada/saída
            $table->unsignedBigInteger('user_id')->nullable(); // Usuário que registrou
            $table->unsignedBigInteger('payment_id')->nullable(); // Relacionamento com Payment (PaymentGateway)
            $table->unsignedBigInteger('campaign_id')->nullable(); // Relacionamento com Campanha
            $table->unsignedBigInteger('ministry_id')->nullable(); // Relacionamento com Ministério
            $table->string('payment_method')->nullable(); // Método de pagamento (cash, transfer, etc)
            $table->string('reference_number')->nullable(); // Número de referência/comprovante
            $table->json('metadata')->nullable(); // Dados extras
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Foreign keys condicionais (verificam se as tabelas existem)
            if (Schema::hasTable('payments')) {
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            }

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');

            if (Schema::hasTable('ministries')) {
                $table->foreign('ministry_id')->references('id')->on('ministries')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};
