<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Versioning for diretoria agendas (historical snapshots).
     */
    public function up(): void
    {
        Schema::create('diretoria_agenda_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diretoria_agenda_id')->constrained('diretoria_agendas')->onDelete('cascade');
            $table->unsignedInteger('version')->default(1);
            $table->json('payload');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['diretoria_agenda_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretoria_agenda_versions');
    }
};
