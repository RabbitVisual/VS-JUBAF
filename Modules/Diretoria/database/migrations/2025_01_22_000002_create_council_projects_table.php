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
        Schema::create('diretoria_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('justification')->nullable();
            $table->text('goals')->nullable();

            // Workflow Status
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'cancelled'])->default('draft');

            // Financials
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('BRL');
            $table->text('budget_details')->nullable(); // Detailed budget breakdown or JSON

            // Ownership
            $table->foreignId('proposer_id')->constrained('users')->onDelete('cascade'); // Who submitted
            $table->string('department')->nullable(); // Ministry or Department name

            // diretoria Actions
            $table->foreignId('reviewed_by')->nullable()->constrained('diretoria_members')->nullOnDelete();
            $table->datetime('reviewed_at')->nullable();
            $table->text('diretoria_comments')->nullable();

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('proposer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretoria_projects');
    }
};
