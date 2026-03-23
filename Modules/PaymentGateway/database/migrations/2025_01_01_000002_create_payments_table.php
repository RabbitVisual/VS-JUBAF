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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('restrict');
            $table->string('payment_type'); // donation, offering, ministry_donation
            $table->morphs('payable'); // Relacionamento polimórfico (Ministry, etc)
            $table->string('transaction_id')->unique(); // ID único da transação
            $table->string('gateway_transaction_id')->nullable(); // ID da transação no gateway
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BRL');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // credit_card, pix, etc
            $table->json('gateway_response')->nullable(); // Resposta completa do gateway
            $table->json('metadata')->nullable(); // Dados adicionais
            $table->text('description')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_document')->nullable(); // CPF/CNPJ
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['payment_type', 'status']);
            $table->index('transaction_id');
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
