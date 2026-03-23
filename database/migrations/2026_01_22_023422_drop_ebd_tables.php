<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Em novos bancos (dev/local) não devemos mais dropar as tabelas EBD.
        // Esta migration fica como legado apenas para bases antigas específicas.
        if (app()->environment('local', 'development', 'dev')) {
            return;
        }

        // Para produção/legado, manteríamos a lógica de drop, se ainda fosse necessária.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible - this is a permanent deletion
    }
};
