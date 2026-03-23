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
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da meta
            $table->text('description')->nullable();
            $table->enum('type', ['monthly', 'yearly', 'campaign', 'custom']); // Tipo de meta
            $table->decimal('target_amount', 15, 2); // Valor da meta
            $table->decimal('current_amount', 15, 2)->default(0); // Valor atual
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('category', [
                'tithe',           // Dízimo
                'offering',        // Oferta
                'donation',        // Doação
                'total_income',    // Receita total
                'campaign',        // Campanha
                'other',           // Outros
            ])->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable(); // Se for meta de campanha
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_goals');
    }
};
