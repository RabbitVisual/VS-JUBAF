<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Additional assembly decision metadata (date and vote counts).
     */
    public function up(): void
    {
        Schema::table('council_agendas', function (Blueprint $table) {
            $table->datetime('assembly_decided_at')->nullable()->after('assembly_decision');
            $table->unsignedInteger('assembly_votes_for')->nullable()->after('assembly_decided_at');
            $table->unsignedInteger('assembly_votes_against')->nullable()->after('assembly_votes_for');
            $table->unsignedInteger('assembly_votes_abstain')->nullable()->after('assembly_votes_against');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('council_agendas', function (Blueprint $table) {
            $table->dropColumn([
                'assembly_decided_at',
                'assembly_votes_for',
                'assembly_votes_against',
                'assembly_votes_abstain',
            ]);
        });
    }
};

