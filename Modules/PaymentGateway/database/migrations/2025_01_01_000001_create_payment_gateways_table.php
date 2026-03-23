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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // stripe, mercado_pago, pix, credit_card
            $table->string('display_name'); // Stripe, Mercado Pago, Pix, Cartão de Crédito
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Nome do ícone do sistema
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->json('credentials')->nullable(); // Chaves, tokens, etc (criptografado)
            $table->json('settings')->nullable(); // Configurações específicas
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
