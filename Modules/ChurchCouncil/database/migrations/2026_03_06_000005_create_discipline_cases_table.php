<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Discipline cases (Mt 18 flow aligned with Baptist practice).
     */
    public function up(): void
    {
        Schema::create('discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('case_type', ['admonition', 'temporary_suspension', 'exclusion', 'restoration'])->default('admonition');
            $table->enum('status', ['opened', 'under_care', 'recommended_to_assembly', 'decided_by_assembly', 'closed'])->default('opened');
            $table->string('current_stage')->nullable();
            $table->text('summary');
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('closed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_cases');
    }
};

