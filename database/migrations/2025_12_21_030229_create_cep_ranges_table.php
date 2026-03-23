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
        Schema::create('cep_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('uf', 2)->comment('Unidade Federativa (Estado)');
            $table->string('cidade', 255)->comment('Nome da cidade');
            $table->string('cep_de', 8)->comment('CEP inicial da faixa');
            $table->string('cep_ate', 8)->comment('CEP final da faixa');
            $table->string('tipo', 50)->nullable()->comment('Tipo: urbano, rural, total, etc.');
            $table->timestamps();

            // Índices para busca rápida
            $table->index('uf');
            $table->index('cidade');
            $table->index(['cep_de', 'cep_ate']);
            $table->index('cep_de');
            $table->index('cep_ate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cep_ranges');
    }
};
