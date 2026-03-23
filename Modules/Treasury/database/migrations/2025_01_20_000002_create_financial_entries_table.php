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
            $table->unsignedBigInteger('member_id')->nullable(); // Usuário vinculado (ex: dizimista)
            $table->unsignedBigInteger('payment_id')->nullable(); // Relacionamento com Payment (PaymentGateway)
            $table->unsignedBigInteger('campaign_id')->nullable(); // Relacionamento com Campanha
            $table->unsignedBigInteger('category_id')->nullable(); // Categoria financeira (v2)
            $table->unsignedBigInteger('ministry_id')->nullable(); // Relacionamento com Ministério
            $table->unsignedBigInteger('fund_id')->nullable(); // Fundo/centro de custo (v2)
            $table->unsignedBigInteger('reversal_of_id')->nullable(); // Estorno referenciando lançamento original
            $table->string('payment_method')->nullable(); // Método de pagamento (cash, transfer, etc)
            $table->string('reference_number')->nullable(); // Número de referência/comprovante
            $table->json('metadata')->nullable(); // Dados extras
            $table->unsignedBigInteger('council_approval_id')->nullable();
            $table->timestamp('council_approved_at')->nullable();
            $table->enum('expense_status', ['pending', 'approved', 'paid'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('set null');

            // Foreign keys condicionais (verificam se as tabelas existem)
            if (Schema::hasTable('payments')) {
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            }

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');

            if (Schema::hasTable('financial_categories')) {
                $table->foreign('category_id')->references('id')->on('financial_categories')->onDelete('set null');
            }

            if (Schema::hasTable('ministries')) {
                $table->foreign('ministry_id')->references('id')->on('ministries')->onDelete('set null');
            }

            if (Schema::hasTable('financial_funds')) {
                $table->foreign('fund_id')->references('id')->on('financial_funds')->onDelete('set null');
            }

            $table->foreign('reversal_of_id')->references('id')->on('financial_entries')->onDelete('set null');

            if (Schema::hasTable('council_approvals')) {
                $table->foreign('council_approval_id')->references('id')->on('council_approvals')->onDelete('set null');
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
