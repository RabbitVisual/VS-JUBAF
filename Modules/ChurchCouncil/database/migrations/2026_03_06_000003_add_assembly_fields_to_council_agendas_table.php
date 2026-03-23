<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Assembly-related metadata on agendas (recommendations to church assembly).
     */
    public function up(): void
    {
        Schema::table('council_agendas', function (Blueprint $table) {
            $table->boolean('requires_assembly_vote')->default(false)->after('status');
            $table->enum('assembly_decision', ['pending', 'approved', 'rejected'])->nullable()->after('requires_assembly_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('council_agendas', function (Blueprint $table) {
            $table->dropColumn(['requires_assembly_vote', 'assembly_decision']);
        });
    }
};

